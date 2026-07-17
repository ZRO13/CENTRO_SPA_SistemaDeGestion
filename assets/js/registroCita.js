// autor: Maria Belen Cassiaux Guerrero
document.addEventListener('DOMContentLoaded', () => {
    const formReserva = document.getElementById("formReserva");
    let enviando = false;

    if (formReserva) {
        formReserva.addEventListener("submit", async (e) => {
            e.preventDefault(); // Previene el comportamiento por defecto del formulario
            if (enviando) return;
            // 1. Validaciones de campos de texto
            const nombre = document.getElementById("nombre").value.trim();
            const telefono = document.getElementById("telefono").value.trim();
            const fecha = document.getElementById("fecha").value;
            const hora = document.getElementById("hora").value;
            const servicios = document.querySelectorAll('input[name="servicios[]"]:checked');

            // Validación de nombre (mínimo 3 caracteres)
            if (nombre.length < 3) {
                alert("Por favor, ingrese un nombre válido (mínimo 3 caracteres).");
                return;
            }

            // Validación de teléfono (solo números, 10 dígitos)
            const telRegex = /^[0-9]{10}$/;
            if (!telRegex.test(telefono)) {
                alert("Por favor, ingrese un número de teléfono válido de 10 dígitos.");
                return;
            }

            // 2. Validación de fecha y hora
            const hoy = new Date().toISOString().split('T')[0];
            if (fecha < hoy) {
                alert("La fecha seleccionada no puede ser anterior a hoy.");
                return;
            }

            if (!hora) {
                alert("Por favor, seleccione una hora.");
                return;
            }

            // 3. Validación de servicios seleccionados
            if (servicios.length === 0) {
                alert("Debe seleccionar al menos un servicio.");
                return;
            }

            // 2. Bloqueo de UI y activación de flag
            enviando = true; 
            const submitBtn = formReserva.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = "Procesando..."; // Feedback visual

            try {
                const response = await fetch('index.php?controller=citas&action=registrar', {
                    method: 'POST',
                    body: new FormData(formReserva)
                });

                const result = await response.json().catch(() => {
                    throw new Error("Respuesta inválida del servidor.");
                });

                if (result.success) {
                    alert("¡Reserva confirmada con éxito!");
                    window.location.href = 'index.php?controller=citas&action=miAgenda'; 
                    // No desbloqueamos porque redirigimos
                } else {
                    alert("Error: " + (result.message || "No se pudo registrar la cita."));
                    // Desbloqueamos en caso de error lógico
                    enviando = false;
                    submitBtn.disabled = false;
                    submitBtn.textContent = "Reservar Cita";
                }
            } catch (error) {
                console.error("Error:", error);
                alert("Ocurrió un error al conectar con el servidor.");
                // Desbloqueamos en caso de error de red
                enviando = false;
                submitBtn.disabled = false;
                submitBtn.textContent = "Reservar Cita";
            }
        });
    }
});