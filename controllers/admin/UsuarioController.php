<?php


// CONTROLLER - Gestiona el CRUD de la tabla 'usuarios' desde el panel
// administrativo: listado general de cuentas, búsqueda, edición de
// datos/rol/estado y baja lógica. Reservado al rol Administrador.

require_once "controllers/SesionHelper.php";
require_once "models/dao/Usuarios/UsuarioDAO.php";
require_once "models/dao/Usuarios/RolDAO.php";
require_once "models/dto/Usuarios/usuario.php";

class UsuarioController
{
    private UsuarioDAO $usuarioDAO;

    public function __construct()
    {
        SesionHelper::requerirRol(['Administrador']);
        $this->usuarioDAO = new UsuarioDAO();
    }

    // Lista todos los usuarios; si llega ?termino=... filtra la búsqueda.
    public function listar()
    {
        $mostrarMenu = true;
        $termino = trim($_GET['termino'] ?? '');

        $usuarios = $termino !== ''
            ? $this->usuarioDAO->buscar($termino)
            : $this->usuarioDAO->listar();

        $roles = (new RolDAO())->listar();

        require_once "views/admin/usuarios_crud.php";
    }

    public function editar()
    {
        $idUsuario = (int)($_GET['id'] ?? 0);
        $usuario = $this->usuarioDAO->buscarPorId($idUsuario);

        if (!$usuario) {
            header("Location: index.php?controller=usuario&action=listar&status=no_encontrado");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $celular = trim($_POST['celular'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $idRol = (int)($_POST['id_rol'] ?? 0);
            $estado = isset($_POST['estado']);

            if ($nombre === '' || $apellido === '' || $correo === '' || $username === '' || $idRol === 0) {
                header("Location: index.php?controller=usuario&action=editar&id={$idUsuario}&status=campos_vacios");
                exit;
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                header("Location: index.php?controller=usuario&action=editar&id={$idUsuario}&status=correo_invalido");
                exit;
            }

            if ($this->usuarioDAO->existeCorreo($correo, $idUsuario)) {
                header("Location: index.php?controller=usuario&action=editar&id={$idUsuario}&status=correo_existente");
                exit;
            }

            if ($this->usuarioDAO->existeUsername($username, $idUsuario)) {
                header("Location: index.php?controller=usuario&action=editar&id={$idUsuario}&status=username_existente");
                exit;
            }

            $usuario->setNombre($nombre);
            $usuario->setApellido($apellido);
            $usuario->setCorreo($correo);
            $usuario->setCelular($celular);
            $usuario->setUsername($username);
            $usuario->setIdRol($idRol);
            $usuario->setEstado($estado);

            $actualizado = $this->usuarioDAO->actualizar($usuario);

            // Si el formulario incluyó una nueva contraseña, se actualiza aparte.
            $nuevaPassword = $_POST['password'] ?? '';
            if ($actualizado && $nuevaPassword !== '') {
                if (strlen($nuevaPassword) < 6) {
                    header("Location: index.php?controller=usuario&action=editar&id={$idUsuario}&status=password_corta");
                    exit;
                }
                $this->usuarioDAO->actualizarPassword($idUsuario, password_hash($nuevaPassword, PASSWORD_BCRYPT));
            }

            $status = $actualizado ? 'updated' : 'error';
            header("Location: index.php?controller=usuario&action=listar&status={$status}");
            exit;
        }

        $roles = (new RolDAO())->listar();
        require_once "views/admin/usuario_editar.php";
    }

    // Baja lógica: desactiva la cuenta (también bloquea su acceso al sistema).
    public function eliminar()
    {
        $idUsuario = (int)($_GET['id'] ?? 0);
        $resultado = $this->usuarioDAO->eliminar($idUsuario);

        $status = $resultado ? 'deleted' : 'error';
        header("Location: index.php?controller=usuario&action=listar&status={$status}");
        exit;
    }

    public function reactivar()
    {
        $idUsuario = (int)($_GET['id'] ?? 0);
        $resultado = $this->usuarioDAO->reactivar($idUsuario);

        $status = $resultado ? 'reactivated' : 'error';
        header("Location: index.php?controller=usuario&action=listar&status={$status}");
        exit;
    }
}
