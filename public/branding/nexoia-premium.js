(function () {
    const $ = id => document.getElementById(id);

    const panel = $('pantallaVoz');
    const centro = $('vozCentro');
    const micro = $('microfonoVoz');
    const input = $('mensaje');
    const form = $('formulario');
    const enviar = $('enviar');
    const chat = $('chat');

    if (!panel || !centro || !input || !form || !enviar || !chat) {
        return;
    }

    let abierto = false;
    let escuchando = false;
    let iniciando = false;
    let esperando = false;
    let sonido = true;
    let anterior = 0;
    let transcripcion = '';
    let falloVoz = false;
    let locucion = null;

    const Recognition =
        window.SpeechRecognition ||
        window.webkitSpeechRecognition;

    const reconocimiento = Recognition ? new Recognition() : null;

    function estado(tipo, titulo, detalle) {
        centro.dataset.estado = tipo;
        $('estadoVoz').textContent = titulo;
        $('textoVoz').textContent = detalle || '';
        micro.disabled = tipo === 'thinking' || !reconocimiento;
        micro.setAttribute(
            'aria-label',
            tipo === 'listening' ? 'Terminar de hablar' : 'Hablar'
        );
    }

    for (let i = 0; i < 31; i++) {
        const barra = document.createElement('span');

        barra.style.setProperty(
            '--altura',
            (12 + Math.abs(Math.sin(i * 1.7)) * 42) + 'px'
        );

        barra.style.animationDelay = (i * .065) + 's';

        $('ondasVoz').appendChild(barra);
    }

    function detenerAudio() {
        locucion = null;

        if ('speechSynthesis' in window) {
            window.speechSynthesis.cancel();
        }
    }

    function escuchar() {
        if (!abierto || escuchando || iniciando ||
            esperando || enviar.disabled) return;

        if (!reconocimiento) {
            estado('idle', 'Microfono no disponible',
                'Utiliza un navegador compatible, como Chrome.');
            return;
        }

        detenerAudio();

        falloVoz = false;
        transcripcion = '';

        try {
            iniciando = true;
            reconocimiento.start();
        } catch (e) {
            iniciando = false;
            estado('idle', 'Intenta nuevamente',
                'No se pudo iniciar el microfono.');
        }
    }

    function abrir() {
        if (enviar.disabled) return;

        const adjunto = $('imagenAdjunta');

        if (input.value.trim() || (adjunto && adjunto.files.length)) {
            $('estado').textContent =
                'Envia o limpia el mensaje pendiente antes de iniciar el modo voz.';
            return;
        }

        abierto = true;
        panel.hidden = false;

        document.body.classList.add('voz-abierta');

        $('respuestaVoz').textContent = '';

        estado('idle', 'Listo para escucharte',
            'Pulsa el microfono para comenzar.');

        micro.focus();
    }

    function cerrar() {
        abierto = false;
        esperando = false;
        transcripcion = '';

        if (reconocimiento) {
            reconocimiento.abort();
        }

        escuchando = false;
        iniciando = false;

        detenerAudio();

        panel.hidden = true;

        document.body.classList.remove('voz-abierta');

        $('abrirVoz').focus();
    }

    function enviarVoz(texto) {
        if (!abierto || !texto || enviar.disabled) return;

        anterior = chat.querySelectorAll('.mensaje.model').length;
        esperando = true;

        input.value = texto;

        $('respuestaVoz').textContent = '';

        estado('thinking', 'Preparando tu respuesta...', texto);

        form.requestSubmit();

        if (!enviar.disabled) {
            esperando = false;
            estado('idle', 'No se pudo enviar',
                'Intenta nuevamente.');
        }
    }

    function reproducir(texto) {
        if (!abierto) return;

        $('respuestaVoz').textContent = texto;

        if (!sonido || !('speechSynthesis' in window)) {
            estado('idle', 'Respuesta recibida',
                'Pulsa el microfono para continuar.');

            if ($('vozContinua').checked) escuchar();
            return;
        }

        detenerAudio();

        const voz = new SpeechSynthesisUtterance(
            texto.replace(/[*#`]/g, '')
        );

        voz.lang = 'es-MX';
        voz.rate = 1.05;
        voz.pitch = 1;

        const voces = window.speechSynthesis.getVoices();

        const preferida =
            voces.find(v => v.lang === 'es-MX') ||
            voces.find(v => v.lang.startsWith('es'));

        if (preferida) voz.voice = preferida;

        locucion = voz;

        estado('speaking', 'NEXOIA esta hablando...',
            'Escucha la respuesta.');

        voz.onend = function () {
            if (!abierto || locucion !== voz) return;

            locucion = null;

            estado('idle', 'Listo para escucharte',
                'Puedes continuar la conversacion.');

            if ($('vozContinua').checked) escuchar();
        };

        voz.onerror = function () {
            if (!abierto || locucion !== voz) return;

            locucion = null;

            estado('idle', 'Respuesta recibida',
                'No fue posible reproducir el audio.');
        };

        window.speechSynthesis.speak(voz);
    }

    if (reconocimiento) {
        reconocimiento.lang = 'es-MX';
        reconocimiento.continuous = false;
        reconocimiento.interimResults = false;

        reconocimiento.onstart = function () {
            iniciando = false;

            if (!abierto) {
                reconocimiento.abort();
                return;
            }

            escuchando = true;

            estado('listening', 'Te escucho...',
                'Habla naturalmente con NEXOIA.');
        };

        reconocimiento.onresult = function (evento) {
            let resultado = '';

            for (let i = 0; i < evento.results.length; i++) {
                if (evento.results[i].isFinal) {
                    resultado += evento.results[i][0].transcript + ' ';
                }
            }

            transcripcion = resultado.trim();

            $('textoVoz').textContent = transcripcion;
        };

        reconocimiento.onend = function () {
            iniciando = false;
            escuchando = false;

            if (!abierto || falloVoz) return;

            const texto = transcripcion;
            transcripcion = '';

            if (texto) {
                enviarVoz(texto);
            } else {
                estado('idle', 'Listo para escucharte',
                    'Pulsa el microfono e intenta nuevamente.');
            }
        };

        reconocimiento.onerror = function (evento) {
            falloVoz = true;
            iniciando = false;
            escuchando = false;

            if (abierto) {
                estado('idle', 'Microfono no disponible',
                    'Comprueba los permisos o intenta nuevamente.');
            }
        };
    }

    new MutationObserver(function () {
        if (!abierto || !esperando || enviar.disabled) return;

        esperando = false;

        const respuestas = chat.querySelectorAll('.mensaje.model');

        if (respuestas.length > anterior) {
            const ultima = respuestas[respuestas.length - 1];

            const contenido = ultima.querySelector('.burbuja');

            if (contenido) {
                reproducir(contenido.textContent.trim());
                return;
            }
        }

        estado('idle', 'No se pudo obtener una respuesta',
            $('estado').textContent || 'Intenta nuevamente.');

    }).observe(enviar, {
        attributes: true,
        attributeFilter: ['disabled']
    });

    $('abrirVoz').addEventListener('click', abrir);
    $('cerrarVoz').addEventListener('click', cerrar);
    $('finalizarVoz').addEventListener('click', cerrar);

    micro.addEventListener('click', function () {
        if (escuchando) {
            reconocimiento.stop();
        } else {
            escuchar();
        }
    });

    $('audioVoz').addEventListener('click', function () {
        sonido = !sonido;

        this.textContent = sonido
            ? '\uD83D\uDD0A'
            : '\uD83D\uDD07';

        this.setAttribute('aria-pressed', String(sonido));

        if (!sonido && locucion) {
            detenerAudio();

            estado('idle', 'Audio silenciado',
                'Pulsa el microfono para continuar.');
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && abierto) cerrar();
    });

    /* BUSCADOR DE CONVERSACIONES */

    const buscador = $('buscarChats');
    const historial = $('historial');

    if (buscador && historial) {

        function filtrar() {
            const texto = buscador.value.toLowerCase().trim();

            historial.querySelectorAll('a').forEach(function (enlace) {
                const fila = enlace.closest('.fila-chat') || enlace;

                fila.hidden = !enlace.textContent
                    .toLowerCase()
                    .includes(texto);
            });
        }

        buscador.addEventListener('input', filtrar);

        new MutationObserver(filtrar).observe(historial, {
            childList: true,
            subtree: true
        });
    }

})();