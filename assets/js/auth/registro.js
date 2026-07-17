// Autor: Zhunaula Imbaquingo Kevin Leodan
// Validaciones del formulario de registro público de clientes.

document.addEventListener('DOMContentLoaded', function () {

    const formulario = document.getElementById('formRegistro');
    if (!formulario) return;

    const inputNombre = formulario.querySelector('input[name="nombre"]');
    const inputApellido = formulario.querySelector('input[name="apellido"]');
    const inputCelular = formulario.querySelector('input[name="celular"]');
    const inputCorreo = formulario.querySelector('input[name="correo"]');
    const inputPassword = formulario.querySelector('input[name="password"]');
    const inputConfirmar = formulario.querySelector('input[name="confirmar_password"]');

    // ==========================================
    // 1. RESTRICCIONES EN TIEMPO REAL
    // ==========================================
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

    // ==========================================
    // 2. VALIDACIÓN LÓGICA DE ENVÍO (SUBMIT)
    // ==========================================
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

        if (inputCorreo) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(inputCorreo.value.trim())) {
                mostrarError(inputCorreo, 'Ingresa un correo electrónico válido.');
                tieneErrores = true;
            }
        }

        if (inputCelular) {
            const telRegex = /^[0-9]{9,10}$/;
            if (!telRegex.test(inputCelular.value.trim())) {
                mostrarError(inputCelular, 'El celular debe tener entre 9 y 10 dígitos.');
                tieneErrores = true;
            }
        }

        if (inputPassword && inputPassword.value.length < 6) {
            mostrarError(inputPassword, 'La contraseña debe tener al menos 6 caracteres.');
            tieneErrores = true;
        }

        if (inputConfirmar && inputConfirmar.value !== inputPassword.value) {
            mostrarError(inputConfirmar, 'Las contraseñas no coinciden.');
            tieneErrores = true;
        }

        if (tieneErrores) {
            e.preventDefault();
            const primerError = formulario.querySelector('.error-input');
            if (primerError) primerError.focus();
        }
    });

    // ==========================================
    // 3. FUNCIONES AUXILIARES DE INTERFAZ (UI)
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
        formulario.querySelectorAll('.error-msg').forEach((msg) => msg.remove());
        formulario.querySelectorAll('.error-input').forEach((input) => {
            input.classList.remove('error-input');
            input.style.borderColor = '';
            input.style.boxShadow = '';
        });
    }
});
