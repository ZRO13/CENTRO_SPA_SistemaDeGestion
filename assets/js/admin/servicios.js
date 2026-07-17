// Comportamiento de interfaz del CRUD de Servicios.

document.addEventListener('DOMContentLoaded', () => {
    const btnToggle = document.getElementById('btnToggleFormulario');
    const btnCancelar = document.getElementById('btnCancelarFormulario');
    const seccionFormulario = document.getElementById('seccionFormulario');
    const btnText = document.getElementById('btnText');
    const inputImagen = document.getElementById('imagen');
    const preview = document.getElementById('previewImagen');
    const sinPreview = document.getElementById('sinPreview');

    const alternarFormulario = () => {
        if (!seccionFormulario) return;

        const oculto = seccionFormulario.classList.toggle('hidden');
        if (btnText) {
            btnText.textContent = oculto ? 'Nuevo servicio' : 'Ocultar formulario';
        }

        if (!oculto) {
            seccionFormulario.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    if (btnToggle) btnToggle.addEventListener('click', alternarFormulario);
    if (btnCancelar) btnCancelar.addEventListener('click', alternarFormulario);

    if (inputImagen && preview) {
        inputImagen.addEventListener('change', () => {
            const archivo = inputImagen.files && inputImagen.files[0];

            if (!archivo) {
                preview.removeAttribute('src');
                preview.classList.add('hidden');

                if (sinPreview) {
                    sinPreview.classList.remove('hidden');
                }
                return;
            }

            const urlTemporal = URL.createObjectURL(archivo);
            preview.src = urlTemporal;
            preview.classList.remove('hidden');

            if (sinPreview) {
                sinPreview.classList.add('hidden');
            }

            preview.onload = () => URL.revokeObjectURL(urlTemporal);
        });
    }

    if (seccionFormulario && seccionFormulario.dataset.editando === '1') {
        seccionFormulario.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
});
