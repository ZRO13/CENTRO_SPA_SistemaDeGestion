// Autor: Zhunaula Imbaquingo Kevin Leodan
// Comportamiento de interfaz del CRUD de Empleados.

document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // 1. CONTROL DE DESPLIEGUE DEL FORMULARIO
    // ==========================================
    const btnToggle = document.getElementById('btnToggleFormulario');
    const btnCancelar = document.getElementById('btnCancelarFormulario');
    const seccionFormulario = document.getElementById('seccionFormulario');
    const btnText = document.getElementById('btnText');

    function toggleForm() {
        if (!seccionFormulario) return;
        const oculto = seccionFormulario.classList.toggle('hidden');
        btnText.textContent = oculto ? 'Nuevo Empleado' : 'Ocultar Formulario';
    }

    if (btnToggle) btnToggle.addEventListener('click', toggleForm);
    if (btnCancelar) btnCancelar.addEventListener('click', toggleForm);

    // ==========================================
    // 2. FILTROS Y VALIDACIÓN DEL FORMULARIO DE REGISTRO
    // ==========================================
    const formulario = document.querySelector('#seccionFormulario form');
    if (!formulario) return;

    const inputNombre = formulario.querySelector('input[name="nombre"]');
    const inputApellido = formulario.querySelector('input[name="apellido"]');
    const inputCelular = formulario.querySelector('input[name="celular"]');

    const soloLetras = (input) => {
        if (!input) return;
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑüÜ ]/g, '');
        });
    };
    soloLetras(inputNombre);
    soloLetras(inputApellido);

    if (inputCelular) {
        inputCelular.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    }

    formulario.addEventListener('submit', function (e) {
        limpiarErrores();
        let tieneErrores = false;

        if (inputNombre && inputNombre.value.trim().length < 2) {
            mostrarError(inputNombre, 'Ingresa un nombre válido.');
            tieneErrores = true;
        }

        if (inputApellido && inputApellido.value.trim().length < 2) {
            mostrarError(inputApellido, 'Ingresa un apellido válido.');
            tieneErrores = true;
        }

        const correo = formulario.querySelector('input[name="correo"]');
        if (correo) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(correo.value.trim())) {
                mostrarError(correo, 'Ingresa un correo electrónico válido.');
                tieneErrores = true;
            }
        }

        const password = formulario.querySelector('input[name="password"]');
        if (password && password.value.length < 6) {
            mostrarError(password, 'La contraseña debe tener al menos 6 caracteres.');
            tieneErrores = true;
        }

        if (inputCelular) {
            const telRegex = /^[0-9]{9,10}$/;
            if (!telRegex.test(inputCelular.value.trim())) {
                mostrarError(inputCelular, 'El celular debe tener entre 9 y 10 dígitos.');
                tieneErrores = true;
            }
        }

        if (tieneErrores) {
            e.preventDefault();
            const primerError = formulario.querySelector('.error-input');
            if (primerError) primerError.focus();
        }
    });

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
        formulario.querySelectorAll('.error-msg').forEach((msg) => msg.remove());
        formulario.querySelectorAll('.error-input').forEach((input) => {
            input.classList.remove('error-input');
            input.style.borderColor = '';
            input.style.boxShadow = '';
        });
    }
});
