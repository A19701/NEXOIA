<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>NEXOIA | Asistente personal</title>

<style>
*{box-sizing:border-box}

body{
    margin:0;
    font-family:Arial,sans-serif;
    background:#090d16;
    color:#f1f5f9;
}

.app{
    display:flex;
    height:100dvh;
}

.sidebar{
    width:260px;
    background:#121827;
    border-right:1px solid #263247;
    padding:22px;
    display:flex;
    flex-direction:column;
}

.logo{
    font-size:24px;
    font-weight:bold;
    letter-spacing:3px;
    margin-bottom:28px;
}

.logo span{color:#818cf8}

.nuevo,button{
    background:#6366f1;
    color:white;
    border:0;
    border-radius:9px;
    padding:12px;
    cursor:pointer;
    text-decoration:none;
    font-weight:bold;
    text-align:center;
}

button:hover,.nuevo:hover{background:#4f46e5}
button:disabled{opacity:.5;cursor:wait}

.historial{
    flex:1;
    overflow-y:auto;
    margin-top:24px;
}

.historial h4{
    color:#94a3b8;
    font-size:11px;
    letter-spacing:1px;
}

.historial a{
    display:block;
    color:#cbd5e1;
    text-decoration:none;
    padding:12px 8px;
    border-radius:8px;
    margin-bottom:4px;
    overflow:hidden;
    text-overflow:ellipsis;
    white-space:nowrap;
    font-size:13px;
}

.historial a:hover{background:#202b40}

.usuario{
    border-top:1px solid #263247;
    padding-top:18px;
}

.usuario button{
    margin-top:12px;
    background:#263247;
    width:100%;
}

.principal{
    flex:1;
    min-width:0;
    display:flex;
    flex-direction:column;
}

header{
    padding:20px 30px;
    border-bottom:1px solid #263247;
    display:flex;
    justify-content:space-between;
}

header small{color:#a5b4fc}

.chat{
    flex:1;
    overflow-y:auto;
    padding:30px;
    width:100%;
    max-width:950px;
    margin:auto;
}

.bienvenida{
    text-align:center;
    padding:35px 0;
}

.bienvenida h1{font-size:32px}

.bienvenida p{
    color:#94a3b8;
    line-height:1.6;
}

.categorias{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:14px;
    max-width:580px;
    margin:30px auto;
}

.categoria{
    background:#171e2e;
    border:1px solid #293249;
    padding:22px;
    text-align:left;
}

.categoria:hover{
    background:#222e45;
    border-color:#818cf8;
}

.mensaje{
    display:flex;
    flex-direction:column;
    margin:20px 0;
}

.mensaje.user{align-items:flex-end}
.mensaje.model{align-items:flex-start}

.autor{
    color:#94a3b8;
    font-size:12px;
    margin:0 0 8px;
}

.burbuja{
    background:#171e2e;
    border:1px solid #263247;
    border-radius:15px;
    padding:16px;
    max-width:90%;
    white-space:pre-wrap;
    overflow-wrap:anywhere;
    line-height:1.7;
    font-size:15px;
}

.user .burbuja{
    background:#3730a3;
    border-color:#3730a3;
}

.entrada{
    padding:15px 25px;
    max-width:950px;
    width:100%;
    margin:auto;
}

form#formulario{
    display:flex;
    gap:12px;
    background:#171e2e;
    padding:12px;
    border:1px solid #374151;
    border-radius:16px;
}

textarea{
    flex:1;
    min-width:0;
    resize:none;
    border:none;
    outline:none;
    background:transparent;
    color:white;
    font:inherit;
    padding:10px;
}

.aviso{
    text-align:center;
    color:#94a3b8;
    font-size:11px;
    line-height:1.6;
}

#estado{
    color:#a5b4fc;
    font-size:13px;
    padding:8px 0;
    min-height:30px;
}

@media(max-width:700px){
    .app{flex-direction:column}
    .sidebar{width:100%;padding:12px;max-height:220px}
    .logo{margin-bottom:10px;font-size:18px}
    .historial{margin-top:5px;min-height:40px}
    .usuario{display:flex;align-items:center;gap:10px}
    .usuario button{margin:0}
    .principal{min-height:0}
    .chat{padding:15px}
    .entrada{padding:10px}
    .bienvenida h1{font-size:23px}
}
</style>
<link rel="stylesheet" href="{{ asset('branding/nexoia.css') }}"><style>
#btnAdjuntar {
    background: #263247;
    min-width: 46px;
    font-size: 21px;
}

#btnAdjuntar:hover {
    background: #3730a3;
}

#previewImagen {
    display: flex;
    align-items: center;
    gap: 12px;
    background: #171e2e;
    border: 1px solid #374151;
    border-radius: 12px;
    padding: 10px;
    margin-bottom: 10px;
}

#previewImagen[hidden] {
    display: none;
}

#miniaturaImagen {
    width: 65px;
    height: 65px;
    object-fit: contain;
    border-radius: 8px;
}

#nombreImagen {
    flex: 1;
    min-width: 0;
    overflow-wrap: anywhere;
    font-size: 12px;
    color: #cbd5e1;
}

#quitarImagen {
    background: #263247;
    font-size: 12px;
}

.fila-chat {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 4px;
}

.fila-chat a {
    flex: 1;
    min-width: 0;
    margin: 0;
}

.fila-chat form {
    margin: 0;
}

.borrar-chat {
    background: transparent;
    color: #94a3b8;
    padding: 7px;
    border-radius: 7px;
}

.borrar-chat:hover {
    background: #451a1a;
    color: #f87171;
}

.borrar-chat svg {
    width: 16px;
    height: 16px;
    display: block;
}
</style>
<link rel="stylesheet" href="{{ asset('branding/nexoia-premium.css') }}?v=1"><link rel="stylesheet" href="{{ asset('branding/nexoia-iconos.css') }}?v=1"></head>

<body>

<div class="app">

    <aside class="sidebar">

        <div class="logo nexoia-marca">
    <img src="{{ asset('branding/logo-nexoia.webp') }}" alt="NEXOIA">
    <span>NEXOIA</span>
</div>

        <a class="nuevo" href="{{ route('dashboard') }}">
            + Nueva conversación
        </a>

        <div class="buscar-chats">
    <span>&#9906;</span>
    <input id="buscarChats" type="search"
           placeholder="Buscar conversaciones"
           aria-label="Buscar conversaciones">
</div>

<div class="historial" id="historial">
            <h4>CONVERSACIONES</h4>

            @foreach($conversaciones as $item)
                <a href="{{ route('dashboard', ['chat' => $item->id]) }}">
                    {{ $item->titulo }}
                </a>
            @endforeach
        </div>

        <div class="usuario">
            {{ auth()->user()->name }}

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        </div>

    </aside>

    <main class="principal">

        <header>
            <strong>Asistente personal</strong>
            <div class="premium-controles">
    <small>Google Gemini</small>
    <button type="button" id="abrirVoz" class="modo-voz">
        <svg viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2"
             stroke-linecap="round">
            <path d="M3 10v4 M7 6v12 M12 3v18 M17 6v12 M21 10v4"/>
        </svg>
        Modo voz
    </button>
</div>
        </header>

        <section class="chat" id="chat">

            @if($mensajes->isEmpty())
                <div class="bienvenida" id="bienvenida">
        <img class="robot-bienvenida" src="{{ asset('branding/logo-nexoia.webp') }}" alt="NEXOIA">

                    <h1>Hola, {{ auth()->user()->name }}</h1>

                    <h2>¿En qué puedo ayudarte hoy?</h2>

                    <p>
                        Cuéntame lo que necesitas.
                        Encontraremos nuevas perspectivas juntos.
                    </p>

                    <div class="categorias">

                        <button class="categoria" type="button"
                            data-prompt="Necesito consejos sobre relaciones personales.">
                            Relaciones personales
                        </button>

                        <button class="categoria" type="button"
                            data-prompt="Quiero mejorar mis hábitos de estudio.">
                            Educación y aprendizaje
                        </button>

                        <button class="categoria" type="button"
                            data-prompt="Quiero organizar mejor mis finanzas personales.">
                            Finanzas personales
                        </button>

                        <button class="categoria" type="button"
                            data-prompt="Quiero mejorar mi bienestar emocional.">
                            Bienestar emocional
                        </button>

                        <button class="categoria" type="button"
                            data-prompt="Necesito consejos para mi desarrollo profesional.">
                            Trabajo y crecimiento
                        </button>

                        <button class="categoria" type="button"
                            data-prompt="Quiero establecer mejores hábitos en mi vida.">
                            Estilo de vida
                        </button>

                    </div>
                </div>
            @endif

            @foreach($mensajes as $mensaje)

                <div class="mensaje {{ $mensaje->rol }}">

                    <div class="autor">
                        {{ $mensaje->rol === 'user' ? 'Tú' : 'NEXOIA' }}
                    </div>

                    <div class="burbuja">{{ $mensaje->contenido }}</div>

                </div>

            @endforeach

        </section>

        <div class="entrada">

            <div id="estado" role="status"></div>

            <div id="previewImagen" hidden>
    <img id="miniaturaImagen" alt="Imagen adjunta">
    <span id="nombreImagen"></span>
    <button type="button" id="quitarImagen">Quitar</button>
</div>

<form id="formulario">

                <textarea id="mensaje"
                    rows="2"
                    maxlength="4000"
                    placeholder="Escribe tu mensaje..."
                    aria-label="Mensaje"></textarea>

                <button type="button" id="btnMic" title="Dictar mensaje" aria-label="Dictar mensaje">
    &#127908;
</button>

<input type="file" id="imagenAdjunta"
    accept="image/jpeg,image/png,image/webp" hidden>

<button type="button" id="btnAdjuntar"
    title="Adjuntar imagen" aria-label="Adjuntar imagen">
    &#128206;
</button>

<button type="submit" id="enviar">
                    Enviar
                </button>

            </form>

            <p class="aviso">
                Tus mensajes se guardan en tu historial y se envían
                a Google Gemini para generar respuestas.
                NEXOIA puede cometer errores.
                Evita compartir información confidencial.
            </p>

            <p class="aviso">Powered by Google Gemini</p>

        </div>

    </main>

</div>

<script>
const chat = document.getElementById('chat');
const formulario = document.getElementById('formulario');
const input = document.getElementById('mensaje');
const boton = document.getElementById('enviar');
const estado = document.getElementById('estado');

let conversacionId = @json($conversacion?->id);
let enviando = false;

function bajarChat() {
    chat.scrollTop = chat.scrollHeight;
}

function agregarMensaje(rol, texto) {

    const mensaje = document.createElement('div');
    mensaje.className = 'mensaje ' + rol;

    const autor = document.createElement('div');
    autor.className = 'autor';
    autor.textContent = rol === 'user' ? 'Tú' : 'NEXOIA';

    const burbuja = document.createElement('div');
    burbuja.className = 'burbuja';
    burbuja.textContent = texto;

    mensaje.append(autor, burbuja);
    chat.appendChild(mensaje);

    bajarChat();

    return mensaje;
}

document.querySelectorAll('.categoria').forEach(elemento => {
    elemento.addEventListener('click', () => {
        input.value = elemento.dataset.prompt;
        input.focus();
    });
});

formulario.addEventListener('submit', async (e) => {

    e.preventDefault();

    const mensaje = input.value.trim();
        const imagenAdjunta = document.getElementById("imagenAdjunta").files[0];

    if ((!mensaje && !imagenAdjunta) || enviando) return;

    enviando = true;
    const inicioRespuesta = performance.now();
    boton.disabled = true;

    const bienvenida = document.getElementById('bienvenida');

    if (bienvenida) bienvenida.hidden = true;

    const temporal = agregarMensaje('user', (mensaje || 'Analiza esta imagen y explica lo que observas.') + (imagenAdjunta ? ' [Imagen adjunta]' : ''));

    input.value = '';
    estado.textContent = 'NEXOIA está preparando tu respuesta...';

    try {

        const datos = new FormData();
        datos.append('mensaje', mensaje);

        if (conversacionId) {
            datos.append('conversacion_id', conversacionId);
        }

        if (imagenAdjunta) {
            datos.append('imagen', imagenAdjunta);
        }

        const response = await fetch(
            @json(route('chatbot.send')),
            {
                method: 'POST',
                headers: {
                    
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content
                },
                body: datos
            }
        );

        const data = await response.json();

        if (!response.ok) {
            throw new Error(
                data.error || data.message || 'Error de conexión'
            );
        }

        const esNuevo = !conversacionId;

        conversacionId = data.conversacion_id;
        document.getElementById("imagenAdjunta").value = "";
        document.getElementById("imagenAdjunta").dispatchEvent(new Event("change"));

        agregarMensaje('model', data.respuesta);

        if (esNuevo) {

            const url = new URL(
                @json(route('dashboard'))
            );

            url.searchParams.set('chat', conversacionId);

            window.history.replaceState({}, '', url.href);

            const enlace = document.createElement('a');
            enlace.href = url.href;
            enlace.textContent = data.titulo;

            const historial = document.getElementById('historial');

            historial.querySelector('h4').after(enlace);
        }

        estado.textContent = 'Respuesta generada en ' + ((performance.now() - inicioRespuesta) / 1000).toFixed(2) + ' segundos';

    } catch (error) {

        temporal.remove();

        if (bienvenida && chat.querySelectorAll('.mensaje').length === 0) {
            bienvenida.hidden = false;
        }

        if (!input.value) input.value = mensaje;

        estado.textContent = error.message;

    } finally {

        enviando = false;
        boton.disabled = false;
        input.focus();

    }

});

input.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) {
        e.preventDefault();
        formulario.requestSubmit();
    }
});

bajarChat();
</script>

<div id="overlayNexoia" role="status" hidden>
    <div class="nexoia-carga">
        <img src="{{ asset('branding/logo-nexoia.webp') }}" alt="NEXOIA">
        <h2>NEXOIA</h2>
        <p>Preparando tu respuesta...</p>
        <div class="nexoia-spinner"></div>
    </div>
</div>

<script src="{{ asset('branding/nexoia-voz.js') }}" defer></script>
<script src="{{ asset('branding/nexoia-adjuntos.js') }}" defer></script>
<section id="pantallaVoz" hidden
         role="dialog" aria-modal="true"
         aria-label="NEXOIA Voice">

    <div class="voz-cabecera">
        <strong>NEXOIA <span>Voice</span></strong>
        <button type="button" id="cerrarVoz"
                aria-label="Cerrar modo voz">&#10005;</button>
    </div>

    <div class="voz-centro" id="vozCentro" data-estado="idle">

        <div class="orbita-robot">
            <div class="orbita orbita-uno"></div>
            <div class="orbita orbita-dos"></div>
            <img src="{{ asset('branding/logo-nexoia.webp') }}"
                 alt="Robot NEXOIA">
        </div>

        <h1 id="estadoVoz" aria-live="polite">Listo para escucharte</h1>

        <p id="textoVoz">Pulsa el microfono para comenzar.</p>

        <div class="ondas-voz" id="ondasVoz"></div>

        <div id="respuestaVoz"></div>

        <div class="controles-voz">

            <button type="button" id="audioVoz"
                    class="voz-secundario"
                    aria-label="Activar o silenciar audio"
                    aria-pressed="true">
                &#128266;
            </button>

            <button type="button" id="microfonoVoz"
                    class="voz-microfono"
                    aria-label="Hablar">
                &#127908;
            </button>

            <button type="button" id="finalizarVoz"
                    class="voz-finalizar"
                    aria-label="Finalizar modo voz">
                &#9742;
            </button>

        </div>

        <label class="voz-continuo">
            <input type="checkbox" id="vozContinua">
            Conversacion continua
        </label>

        <p class="voz-nota">
            Powered by Google Gemini
        </p>
    </div>
</section>

<script src="{{ asset('branding/nexoia-premium.js') }}?v=1" defer></script>
<script src="{{ asset('branding/nexoia-iconos.js') }}?v=1" defer></script></body>
</html>