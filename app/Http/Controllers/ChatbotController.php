<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Client\ConnectionException;

class ChatbotController extends Controller
{
    public function index(Request $request)
    {
        $usuario = $request->user();

        $conversaciones = $usuario->conversaciones()
            ->latest('updated_at')
            ->limit(50)
            ->get();

        $conversacion = null;
        $mensajes = collect();

        if ($request->filled('chat')) {
            $conversacion = $usuario->conversaciones()
                ->findOrFail($request->integer('chat'));

            $mensajes = $conversacion->mensajes()
                ->orderBy('id')
                ->get(['rol', 'contenido']);
        }

        return view('chatbot.index', compact(
            'conversaciones',
            'conversacion',
            'mensajes'
        ));
    }

    public function send(Request $request)
    {
        $datos = $request->validate([
            'mensaje' => 'nullable|required_without:imagen|string|max:4000',
            'imagen' => 'nullable|required_without:mensaje|image|mimes:jpg,jpeg,png,webp|max:5120',
            'conversacion_id' => 'nullable|integer|min:1'
        ]);

        $usuario = $request->user();
        $conversacion = null;
        $historial = [];

        if (!empty($datos['conversacion_id'])) {

            $conversacion = $usuario->conversaciones()
                ->findOrFail($datos['conversacion_id']);

            $anteriores = $conversacion->mensajes()
                ->latest('id')
                ->limit(12)
                ->get()
                ->reverse()
                ->values();

            foreach ($anteriores as $anterior) {

                $contenido = $anterior->gemini_content
                    ? json_decode($anterior->gemini_content, true)
                    : null;

                $historial[] = $contenido ?: [
                    'role' => $anterior->rol,
                    'parts' => [
                        ['text' => $anterior->contenido]
                    ]
                ];
            }
        }

        $instrucciones = <<<'PROMPT'
Eres NEXOIA, un asistente inteligente especializado
en ofrecer consejos personalizados sobre distintos temas.

Tu objetivo es ayudar al usuario a tomar decisiones
informadas y encontrar soluciones practicas.

REGLAS:
- Adapta tus consejos a la situacion del usuario.
- Utiliza el contexto de la conversacion.
- Responde de forma natural, cercana y respetuosa.
- Pregunta cuando falte informacion importante.
- Ofrece soluciones concretas y realistas.
- No inventes informacion ni garantices resultados.
- No juzgues al usuario.
- Respeta su autonomia para tomar decisiones.
- No sustituyas a profesionales medicos, legales
  o financieros.
- Prioriza la seguridad ante situaciones de riesgo.
- Responde en el idioma del usuario.
- Responde directamente, sin introducciones innecesarias.
- Prioriza respuestas de 80 a 150 palabras cuando sea suficiente.
- Amplia la respuesta cuando el usuario lo solicite.
- Usa parrafos breves y listas sencillas.
PROMPT;

        $datos['mensaje'] = trim((string) ($datos['mensaje'] ?? ''));

        if ($datos['mensaje'] === '') {
            $datos['mensaje'] = 'Analiza esta imagen y explica lo que observas.';
        }

        $imagen = $request->file('imagen');

        if ($imagen) {
            $datos['mensaje'] .= "\n[Imagen adjunta]";
        }

        $partes = [
            ['text' => $datos['mensaje']]
        ];

        if ($imagen) {

            $partes[] = [
                'inlineData' => [
                    'mimeType' => $imagen->getMimeType(),
                    'data' => base64_encode(
                        file_get_contents($imagen->getRealPath())
                    )
                ]
            ];
        }

        $historial[] = [
            'role' => 'user',
            'parts' => $partes
        ];

        try {

            $respuesta = Http::connectTimeout(10)
                ->timeout(60)
                ->withHeaders([
                    'x-goog-api-key' =>
                        config('services.gemini.api_key')
                ])
                ->post(
                    'https://generativelanguage.googleapis.com/v1beta/models/'
                    . config('services.gemini.model')
                    . ':generateContent',
                    [
                        'systemInstruction' => [
                            'parts' => [
                                ['text' => $instrucciones]
                            ]
                        ],
                        'contents' => $historial,
                        'generationConfig' => [
                            'temperature' => 0.8,
                            'thinkingConfig' => [
                                'thinkingLevel' => 'MINIMAL',
                                'includeThoughts' => false
                            ],
                            'maxOutputTokens' => 2048
                        ]
                    ]
                );

            if ($respuesta->failed()) {

                $error = match ($respuesta->status()) {
                    429 => 'Se alcanzo el limite de solicitudes.',
                    503 => 'Gemini esta temporalmente ocupado. Intenta nuevamente.',
                    default => 'No fue posible obtener una respuesta de Gemini.'
                };

                return response()->json([
                    'error' => $error
                ], 503);
            }

            $contenidoGemini = $respuesta->json(
                'candidates.0.content'
            );

            $texto = collect($contenidoGemini['parts'] ?? [])
                ->reject(fn ($parte) => $parte['thought'] ?? false)
                ->pluck('text')
                ->filter()
                ->implode("\n");

            if (trim($texto) === '') {
                return response()->json([
                    'error' => 'Gemini no genero una respuesta de texto.'
                ], 422);
            }

            $conversacion = DB::transaction(function () use (
                $usuario,
                $conversacion,
                $datos,
                $texto,
                $contenidoGemini
            ) {

                if (!$conversacion) {
                    $conversacion = $usuario->conversaciones()
                        ->create([
                            'titulo' => Str::limit(
                                $datos['mensaje'], 50
                            ),
                            'categoria' => 'General'
                        ]);
                }

                $conversacion->mensajes()->create([
                    'rol' => 'user',
                    'contenido' => $datos['mensaje']
                ]);

                $mensaje = $conversacion->mensajes()->make([
                    'rol' => 'model',
                    'contenido' => $texto
                ]);

                $mensaje->gemini_content = json_encode(
                    $contenidoGemini,
                    JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
                );

                $mensaje->save();

                $conversacion->touch();

                return $conversacion;
            });

            return response()->json([
                'respuesta' => $texto,
                'conversacion_id' => $conversacion->id,
                'titulo' => $conversacion->titulo
            ]);

        } catch (ConnectionException $e) {

            return response()->json([
                'error' => 'No se pudo establecer conexion con Gemini.'
            ], 503);

        } catch (\Throwable $e) {

            logger()->error('Error en NEXOIA', [
                'tipo' => get_class($e)
            ]);

            return response()->json([
                'error' => 'Ocurrio un error interno.'
            ], 500);
        }
    }
}