(function () {

    const mic = document.getElementById('btnMic');
    const input = document.getElementById('mensaje');
    const enviar = document.getElementById('enviar');
    const estado = document.getElementById('estado');
    const overlay = document.getElementById('overlayNexoia');
    const chat = document.getElementById('chat');

    if (!mic || !input || !enviar || !overlay || !chat) return;

    const SpeechRecognition =
        window.SpeechRecognition ||
        window.webkitSpeechRecognition;

    let recognition = null;
    let grabando = false;

    if (SpeechRecognition) {

        recognition = new SpeechRecognition();
        recognition.lang = 'es-MX';
        recognition.interimResults = true;
        recognition.continuous = false;

        recognition.onstart = function () {
            grabando = true;
            mic.classList.add('grabando');
            estado.textContent = 'Escuchando...';
        };

        recognition.onresult = function (event) {
            if (enviar.disabled) return;

            let texto = '';

            for (let i = 0; i < event.results.length; i++) {
                texto += event.results[i][0].transcript;
            }

            input.value = texto.trim();
        };

        recognition.onend = function () {
            grabando = false;
            mic.classList.remove('grabando');

            if (!enviar.disabled &&
                estado.textContent === 'Escuchando...') {
                estado.textContent = 'Dictado finalizado.';
            }
        };

        recognition.onerror = function () {
            grabando = false;
            mic.classList.remove('grabando');

            if (!enviar.disabled) {
                estado.textContent = 'No se pudo acceder al microfono.';
            }
        };

        mic.addEventListener('click', function () {

            if (grabando) {
                recognition.stop();
            } else {
                try {
                    recognition.start();
                } catch (error) {
                    estado.textContent = 'Intenta activar el microfono nuevamente.';
                }
            }

        });

    } else {
        mic.disabled = true;
        mic.title = 'Reconocimiento de voz no disponible';
    }

    function actualizarCarga() {

        overlay.hidden = !enviar.disabled;

        mic.disabled = enviar.disabled || !recognition;

        if (enviar.disabled && grabando && recognition) {
            recognition.abort();
        }

    }

    const observadorCarga = new MutationObserver(actualizarCarga);

    observadorCarga.observe(enviar, {
        attributes: true,
        attributeFilter: ['disabled']
    });

    actualizarCarga();

    function agregarBotonesVoz() {

        if (!('speechSynthesis' in window)) return;

        chat.querySelectorAll('.mensaje.model').forEach(function (mensaje) {

            if (mensaje.querySelector('.nexoia-escuchar')) return;

            const burbuja = mensaje.querySelector('.burbuja');

            if (!burbuja) return;

            const boton = document.createElement('button');

            boton.type = 'button';
            boton.className = 'nexoia-escuchar';
            boton.textContent = '\uD83D\uDD0A Escuchar respuesta';

            boton.addEventListener('click', function () {

                const estabaHablando =
                    window.speechSynthesis.speaking &&
                    boton.dataset.activo === '1';

                window.speechSynthesis.cancel();

                document.querySelectorAll('.nexoia-escuchar')
                    .forEach(function (b) {
                        b.dataset.activo = '0';
                        b.textContent = '\uD83D\uDD0A Escuchar respuesta';
                    });

                if (estabaHablando) return;

                const texto = burbuja.textContent
                    .replace(/[*#`]/g, '');

                const voz = new SpeechSynthesisUtterance(texto);

                voz.lang = 'es-MX';
                voz.rate = 1;
                voz.pitch = 1;

                boton.dataset.activo = '1';
                boton.textContent = 'Detener lectura';

                voz.onend = voz.onerror = function () {
                    boton.dataset.activo = '0';
                    boton.textContent = '\uD83D\uDD0A Escuchar respuesta';
                };

                window.speechSynthesis.speak(voz);
            });

            mensaje.appendChild(boton);
        });
    }

    const observadorChat = new MutationObserver(agregarBotonesVoz);

    observadorChat.observe(chat, {
        childList: true,
        subtree: true
    });

    agregarBotonesVoz();

})();