// Autor: Zhunaula Imbaquingo Kevin Leodan
// Comportamiento de interfaz para el listado de Usuarios.

document.addEventListener('DOMContentLoaded', function () {
    // Las alertas de éxito se ocultan solas después de unos segundos
    // para no saturar la pantalla en sesiones de uso prolongado.
    const alertaExito = document.querySelector('.alert-success');
    if (alertaExito) {
        setTimeout(() => {
            alertaExito.style.transition = 'opacity .4s ease';
            alertaExito.style.opacity = '0';
            setTimeout(() => alertaExito.remove(), 400);
        }, 4000);
    }
});
