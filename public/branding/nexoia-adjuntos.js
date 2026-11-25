(function () {

    const archivo = document.getElementById('imagenAdjunta');
    const adjuntar = document.getElementById('btnAdjuntar');
    const preview = document.getElementById('previewImagen');
    const miniatura = document.getElementById('miniaturaImagen');
    const nombre = document.getElementById('nombreImagen');
    const quitar = document.getElementById('quitarImagen');
    const historial = document.getElementById('historial');
    const estado = document.getElementById('estado');
    const enviar = document.getElementById('enviar');

    let urlTemporal = null;

    adjuntar.addEventListener('click', function () {
        if (!enviar.disabled) archivo.click();
    });

    archivo.addEventListener('change', function () {

        if (urlTemporal) {
            URL.revokeObjectURL(urlTemporal);
            urlTemporal = null;
        }

        preview.hidden = true;
        miniatura.removeAttribute('src');

        const imagen = archivo.files[0];

        if (!imagen) return;

        const formatos = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!formatos.includes(imagen.type)) {
            estado.textContent = 'Selecciona una imagen JPG, PNG o WEBP.';
            archivo.value = '';
            return;
        }

        if (imagen.size > 5 * 1024 * 1024) {
            estado.textContent = 'La imagen supera el limite de 5 MB.';
            archivo.value = '';
            return;
        }

        urlTemporal = URL.createObjectURL(imagen);

        miniatura.src = urlTemporal;
        nombre.textContent = imagen.name;
        preview.hidden = false;

        estado.textContent = 'Imagen lista para analizar.';

    });

    quitar.addEventListener('click', function () {
        if (enviar.disabled) return;

        archivo.value = '';
        archivo.dispatchEvent(new Event('change'));
        estado.textContent = '';
    });

    function actualizarControles() {
        archivo.disabled = enviar.disabled;
        adjuntar.disabled = enviar.disabled;
        quitar.disabled = enviar.disabled;
    }

    new MutationObserver(actualizarControles).observe(enviar, {
        attributes: true,
        attributeFilter: ['disabled']
    });

    actualizarControles();

    function agregarBotonesEliminar() {

        historial.querySelectorAll(':scope > a').forEach(function (enlace) {

            const url = new URL(enlace.href);
            const id = url.searchParams.get('chat');

            if (!id || !/^\d+$/.test(id)) return;

            const fila = document.createElement('div');
            fila.className = 'fila-chat';

            const formulario = document.createElement('form');
            formulario.method = 'POST';
            formulario.action = '/chatbot/conversaciones/' + id;

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = document.querySelector(
                'meta[name="csrf-token"]'
            ).content;

            const metodo = document.createElement('input');
            metodo.type = 'hidden';
            metodo.name = '_method';
            metodo.value = 'DELETE';

            const boton = document.createElement('button');

            boton.type = 'submit';
            boton.className = 'borrar-chat';
            boton.title = 'Eliminar conversacion';
            boton.setAttribute('aria-label', 'Eliminar conversacion');

            boton.innerHTML = `
                <svg viewBox="0 0 24 24"
                     fill="none" stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M3 6h18"/>
                    <path d="M8 6V3h8v3"/>
                    <path d="M5 6l1 15h12l1-15"/>
                    <path d="M10 10v7M14 10v7"/>
                </svg>
            `;

            formulario.addEventListener('submit', function (e) {

                if (enviar.disabled) {
                    e.preventDefault();
                    return;
                }

                const aceptar = confirm(
                    'Eliminar esta conversacion y todos sus mensajes? Esta accion no se puede deshacer.'
                );

                if (!aceptar) e.preventDefault();

            });

            formulario.append(token, metodo, boton);

            enlace.replaceWith(fila);
            fila.append(enlace, formulario);

        });

    }

    new MutationObserver(agregarBotonesEliminar).observe(
        historial,
        { childList: true }
    );

    agregarBotonesEliminar();

})();