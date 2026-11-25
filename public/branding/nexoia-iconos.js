(function () {
    const icons = {
        mic: '<rect x="9" y="2" width="6" height="12" rx="3"/><path d="M5 10v2a7 7 0 0 0 14 0v-2M12 19v3M8 22h8"/>',

        clip: '<path d="m21 11-9 9a6 6 0 0 1-8-8l10-10a4 4 0 0 1 6 6L10 18a2 2 0 0 1-3-3l9-9"/>',

        send: '<path d="M12 19V5M5 12l7-7 7 7"/>',

        voice: '<path d="M3 10v4M7 6v12M12 3v18M17 6v12M21 10v4"/>',

        heart: '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l8.9 8.9 8.8-8.9a5.5 5.5 0 0 0 0-7.8Z"/>',

        education: '<path d="m2 10 10-5 10 5-10 5-10-5ZM6 12v5c3 3 9 3 12 0v-5M22 10v7"/>',

        wallet: '<rect x="3" y="5" width="18" height="15" rx="3"/><path d="M3 9V5l15-3v3M21 10h-6v6h6M17 13h.01"/>',

        brain: '<path d="M12 18V5M12 5C9 0 4 4 5 8c-5 1-5 8 0 9-1 5 7 6 7 1M12 5c3-5 8-1 7 3 5 1 5 8 0 9 1 5-7 6-7 1M5 8l3 2M19 8l-3 2M5 17l3-2M19 17l-3-2"/>',

        growth: '<path d="m3 17 6-6 4 4 8-9M15 6h6v6"/>',

        sparkles: '<path d="m12 2 3 7 7 3-7 3-3 7-3-7-7-3 7-3 3-7ZM5 2v6M2 5h6"/>',

        search: '<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/>',

        volume: '<path d="m11 5-6 4H2v6h3l6 4V5ZM15 9a4 4 0 0 1 0 6M18 5a9 9 0 0 1 0 14"/>',

        mute: '<path d="m11 5-6 4H2v6h3l6 4V5ZM16 9l6 6M22 9l-6 6"/>',

        phone: '<path d="M3 16v-3c5-5 13-5 18 0v3l-5 1-1-4H9l-1 4-5-1Z"/>',

        close: '<path d="m6 6 12 12M18 6 6 18"/>'
    };

    function svg(name) {
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' +
            icons[name] + '</svg>';
    }

    function setIcon(id, name, label) {
        const boton = document.getElementById(id);

        if (!boton) return;

        boton.innerHTML = svg(name);
        boton.title = label;
        boton.setAttribute('aria-label', label);
    }

    setIcon('btnMic', 'mic', 'Dictar mensaje');
    setIcon('btnAdjuntar', 'clip', 'Adjuntar imagen');
    setIcon('enviar', 'send', 'Enviar mensaje');
    setIcon('microfonoVoz', 'mic', 'Hablar');
    setIcon('finalizarVoz', 'phone', 'Finalizar chat de voz');
    setIcon('cerrarVoz', 'close', 'Cerrar modo voz');
    setIcon('audioVoz', 'volume', 'Silenciar o activar audio');

    const audio = document.getElementById('audioVoz');

    if (audio) {
        audio.addEventListener('click', function () {
            this.innerHTML = svg(
                this.getAttribute('aria-pressed') === 'true'
                    ? 'volume' : 'mute'
            );
        });
    }

    const buscador = document.querySelector('.buscar-chats span');

    if (buscador) {
        buscador.innerHTML = svg('search');
    }

    const categorias = [
        ['heart', '#f472b6', 'Conecta con los demas'],
        ['education', '#60a5fa', 'Desarrolla tus conocimientos'],
        ['wallet', '#34d399', 'Organiza tus recursos'],
        ['brain', '#a78bfa', 'Encuentra tu equilibrio'],
        ['growth', '#fbbf24', 'Alcanza tus objetivos'],
        ['sparkles', '#22d3ee', 'Mejora tus habitos']
    ];

    document.querySelectorAll('.categoria').forEach((boton, i) => {

        if (!categorias[i]) return;

        const [icono, color, descripcion] = categorias[i];
        const titulo = boton.textContent.trim();

        boton.textContent = '';

        const insignia = document.createElement('span');
        insignia.className = 'categoria-icono';
        insignia.style.color = color;
        insignia.style.backgroundColor = color + '16';
        insignia.innerHTML = svg(icono);

        const nombre = document.createElement('strong');
        nombre.textContent = titulo;

        const detalle = document.createElement('small');
        detalle.textContent = descripcion;

        boton.append(insignia, nombre, detalle);
    });

    const enviar = document.getElementById('enviar');
    const abrirVoz = document.getElementById('abrirVoz');

    if (enviar && abrirVoz && !document.getElementById('vozDirecta')) {

        const boton = document.createElement('button');

        boton.type = 'button';
        boton.id = 'vozDirecta';
        boton.title = 'Abrir chat de voz';
        boton.setAttribute('aria-label', 'Abrir chat de voz');

        boton.innerHTML = svg('voice') +
            '<span>Chat de voz</span>';

        boton.addEventListener('click', function () {
            abrirVoz.click();
        });

        enviar.before(boton);

        function actualizar() {
            boton.disabled = enviar.disabled;
        }

        new MutationObserver(actualizar).observe(enviar, {
            attributes: true,
            attributeFilter: ['disabled']
        });

        actualizar();
    }

    const bienvenida = document.getElementById('bienvenida');
    const chat = document.getElementById('chat');

    if (bienvenida && chat && !bienvenida.hidden) {
        chat.scrollTop = 0;
    }

})();