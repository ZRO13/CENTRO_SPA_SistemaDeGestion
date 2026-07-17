<?php
// CONTROLLER (helper compartido) - No es un Controller de ruta por sí mismo:
// centraliza la lógica de sesión (login, logout, control de acceso por rol)
// que necesitan varios Controllers, evitando duplicar código.
// Responsabilidad: gestionar $_SESSION y redirigir cuando el acceso no está
// permitido. No accede a la base de datos ni contiene lógica de negocio.

class SesionHelper
{
    // Guarda en sesión los datos mínimos del usuario autenticado.
    public static function iniciarSesion(Usuario $usuario): void
    {
        $_SESSION['usuario'] = [
            'id_usuario' => $usuario->getIdUsuario(),
            'nombre'     => $usuario->getNombre(),
            'apellido'   => $usuario->getApellido(),
            'username'   => $usuario->getUsername(),
            'id_rol'     => $usuario->getIdRol(),
            'nombre_rol' => $usuario->getNombreRol(),
        ];
    }

    public static function cerrarSesion(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function estaAutenticado(): bool
    {
        return isset($_SESSION['usuario']);
    }

    public static function usuarioActual(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    // Corta la ejecución y redirige al login si no hay sesión activa.
    public static function requerirAutenticacion(): void
    {
        if (!self::estaAutenticado()) {
            header("Location: index.php?controller=auth&action=login");
            exit;
        }
    }

    // Corta la ejecución si el usuario autenticado no tiene uno de los
    // roles permitidos. Ejemplo: SesionHelper::requerirRol(['Administrador']);
    public static function requerirRol(array $rolesPermitidos): void
    {
        self::requerirAutenticacion();

        $rolActual = $_SESSION['usuario']['nombre_rol'] ?? '';

        if (!in_array($rolActual, $rolesPermitidos, true)) {
            http_response_code(403);
            die("Acceso denegado: no tiene permisos para acceder a este módulo.");
        }
    }
}
