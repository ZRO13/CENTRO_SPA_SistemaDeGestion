// Autor: Zhunaula Imbaquingo Kevin Leodan

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // 1. CONTROL DE DESPLIEGUE DEL FORMULARIO
    // ==========================================
    const btnToggle = document.getElementById('btnToggleFormulario');
    const btnCancelar = document.getElementById('btnCancelarFormulario');
    const seccionFormulario = document.getElementById('seccionFormulario');
    const btnText = document.getElementById('btnText');

    function toggleForm() {
        if (seccionFormulario.classList.contains('hidden')) {
            seccionFormulario.classList.remove('hidden');
            btnText.textContent = "Ocultar Formulario";
            btnToggle.style.background = "var(--color-secondary)";
        } else {
            seccionFormulario.classList.add('hidden');
            btnText.textContent = "Nuevo Cliente";
            btnToggle.style.background = "var(--color-primary)";
        }
    }

    if (btnToggle) btnToggle.addEventListener('click', toggleForm);
    if (btnCancelar) btnCancelar.addEventListener('click', toggleForm);

    // ==========================================
    // 2. FILTROS RESTRICCIONES EN TIEMPO REAL
    // ==========================================
    const formulario = document.querySelector('#seccionFormulario form');

    if (formulario) {
        const inputNombre = formulario.querySelector('input[name="nombre"]');
        const inputTelefono = formulario.querySelector('input[name="telefono"]');

        // Restricción para Nombre: Solo letras, espacios, acentos y la eñe (Ñ/ñ)
        if (inputNombre) {
            inputNombre.addEventListener('input', function (e) {
                // Reemplaza todo lo que NO sea letras de la A-Z (con acentos) o espacios
                e.target.value = e.target.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]/g, '');
            });
        }

        // Restricción para Teléfono: Solo dígitos del 0 al 9 al escribir
        if (inputTelefono) {
            inputTelefono.addEventListener('input', function (e) {
                // Reemplaza todo lo que NO sea un número
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
        }

        // ==========================================
        // 3. VALIDACIÓN LÓGICA DE ENVÍO (SUBMIT)
        // ==========================================
        formulario.addEventListener('submit', function (e) {
            // Limpiar estados de error previos en la interfaz
            limpiarErrores();

            let tieneErrores = false;

            // Validación: Nombre Completo (Mínimo 3 caracteres limpios)
            if (inputNombre) {
                if (!inputNombre.value || inputNombre.value.trim().length < 3) {
                    mostrarError(inputNombre, 'El nombre debe tener al menos 3 caracteres.');
                    tieneErrores = true;
                }
            }

            // Validación: Correo Electrónico
            const correo = formulario.querySelector('input[name="correo"]');
            if (correo) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(correo.value.trim())) {
                    mostrarError(correo, 'Por favor, ingresa un correo electrónico válido.');
                    tieneErrores = true;
                }
            }

            // Validación: Contraseña
            const contrasena = formulario.querySelector('input[name="contrasena"]');
            if (contrasena) {
                if (!contrasena.value || contrasena.value.length < 6) {
                    mostrarError(contrasena, 'La contraseña debe tener al menos 6 caracteres.');
                    tieneErrores = true;
                }
            }

            // Validación: Teléfono (Formato de longitud final)
            if (inputTelefono && inputTelefono.value.trim() !== '') {
                const telRegex = /^[0-9]{9,10}$/;
                if (!telRegex.test(inputTelefono.value.trim())) {
                    mostrarError(inputTelefono, 'El teléfono debe contener entre 9 y 10 dígitos numéricos.');
                    tieneErrores = true;
                }
            }

            // Validación: Fecha de Nacimiento
            const fechaNac = formulario.querySelector('input[name="fecha_nacimiento"]');
            if (fechaNac && fechaNac.value) {
                const fechaSeleccionada = new Date(fechaNac.value);
                const fechaActual = new Date();
                if (fechaSeleccionada > fechaActual) {
                    mostrarError(fechaNac, 'La fecha de nacimiento no puede ser posterior a la fecha actual.');
                    tieneErrores = true;
                }
            }

            // Bloquear envío en caso de fallos
            if (tieneErrores) {
                e.preventDefault();
                const primerError = formulario.querySelector('.error-input');
                if (primerError) primerError.focus();
            }
        });
    }

    // ==========================================
    // 4. FUNCIONES AUXILIARES DE INTERFAZ (UI)
    // ==========================================
    function mostrarError(elemento, mensaje) {
        if (elemento.classList.contains('error-input')) return;

        elemento.classList.add('error-input');
        elemento.style.borderColor = 'var(--color-danger)';
        elemento.style.boxShadow = '0 0 0 3px rgba(220, 53, 69, .15)';

        const errorTxt = document.createElement('small');
        errorTxt.className = 'error-msg';
        errorTxt.textContent = mensaje;
        errorTxt.style.color = 'var(--color-danger)';
        errorTxt.style.fontSize = 'var(--font-size-xs)';
        errorTxt.style.display = 'block';
        errorTxt.style.marginTop = '4px';

        elemento.parentElement.appendChild(errorTxt);
    }

    function limpiarErrores() {
        if (!formulario) return;

        const erroresTxt = formulario.querySelectorAll('.error-msg');
        erroresTxt.forEach(msg => msg.remove());

        const inputsConError = formulario.querySelectorAll('.error-input');
        inputsConError.forEach(input => {
            input.classList.remove('error-input');
            input.style.borderColor = '';
            input.style.boxShadow = '';
        });
    }
});