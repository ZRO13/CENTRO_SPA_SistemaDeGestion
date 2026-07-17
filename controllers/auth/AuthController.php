<?php
// CONTROLLER - Gestiona el flujo de autenticación del sistema: login,
// logout y el registro público de nuevos clientes. Valida la información
// recibida, coordina con los DAO y administra la sesión del usuario.

require_once "controllers/SesionHelper.php";
require_once "models/dao/usuarios/UsuarioDAO.php";
require_once "models/dao/usuarios/ClienteDAO.php";
require_once "models/dao/usuarios/RolDAO.php";
require_once "models/dto/usuarios/usuario.php";
require_once "models/dto/usuarios/cliente.php";

class AuthController
{

    private const ROL_CLIENTE = 'Cliente';

    public function login()
    {
        if (SesionHelper::estaAutenticado()) {
            $this->redirigirSegunRol($_SESSION['usuario']['nombre_rol']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $credencial = trim($_POST['credencial'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($credencial === '' || $password === '') {
                header("Location: index.php?controller=auth&action=login&status=campos_vacios");
                exit;
            }

            $usuarioDAO = new UsuarioDAO();
            $usuario = $usuarioDAO->buscarPorCredencial($credencial);

            if (!$usuario || !password_verify($password, $usuario->getPassword())) {
                header("Location: index.php?controller=auth&action=login&status=credenciales_invalidas");
                exit;
            }

            if (!$usuario->getEstado()) {
                header("Location: index.php?controller=auth&action=login&status=cuenta_inactiva");
                exit;
            }

            SesionHelper::iniciarSesion($usuario);
            $this->redirigirSegunRol($usuario->getNombreRol());
            exit;
        }

        require_once "views/auth/login.php";
    }

    public function logout()
    {
        SesionHelper::cerrarSesion();
        header("Location: index.php?controller=auth&action=login&status=sesion_cerrada");
        exit;
    }

    // Registro público: cualquier visitante puede crear una cuenta de Cliente.
    public function registro()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $celular = trim($_POST['celular'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmarPassword = $_POST['confirmar_password'] ?? '';

            // --- Validaciones básicas del lado del servidor ---
            if ($nombre === '' || $apellido === '' || $correo === '' || $celular === '' || $username === '' || $password === '') {
                header("Location: index.php?controller=auth&action=registro&status=campos_vacios");
                exit;
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                header("Location: index.php?controller=auth&action=registro&status=correo_invalido");
                exit;
            }

            if (strlen($password) < 6) {
                header("Location: index.php?controller=auth&action=registro&status=password_corta");
                exit;
            }

            if ($password !== $confirmarPassword) {
                header("Location: index.php?controller=auth&action=registro&status=password_no_coincide");
                exit;
            }

            $usuarioDAO = new UsuarioDAO();

            if ($usuarioDAO->existeCorreo($correo)) {
                header("Location: index.php?controller=auth&action=registro&status=correo_existente");
                exit;
            }

            if ($usuarioDAO->existeUsername($username)) {
                header("Location: index.php?controller=auth&action=registro&status=username_existente");
                exit;
            }

            $rolCliente = (new RolDAO())->buscarPorNombre(self::ROL_CLIENTE);

            if (!$rolCliente) {
                // El rol semilla "Cliente" no existe: error de configuración de la BD.
                header("Location: index.php?controller=auth&action=registro&status=error");
                exit;
            }

            // Se hashea la contraseña con BCRYPT, nunca se guarda en texto plano.
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            $usuario = new Usuario(
                null,
                $rolCliente->getIdRol(),
                $nombre,
                $apellido,
                $correo,
                $celular,
                $username,
                $passwordHash,
                true
            );

            // La creación del usuario y su fila de 'clientes' deben ser atómicas:
            // si una falla, no debe quedar un usuario "huérfano" sin rol de cliente.
            $usuarioDAO->iniciarTransaccion();
            $conexionCompartida = $usuarioDAO->getConexion();

            $idUsuario = $usuarioDAO->insertar($usuario);

            if ($idUsuario === null) {
                $usuarioDAO->cancelarTransaccion();
                header("Location: index.php?controller=auth&action=registro&status=error");
                exit;
            }

            $clienteDAO = new ClienteDAO($conexionCompartida);
            $cliente = new Cliente(null, $idUsuario);
            $registrado = $clienteDAO->insertar($cliente);

            if (!$registrado) {
                $usuarioDAO->cancelarTransaccion();
                header("Location: index.php?controller=auth&action=registro&status=error");
                exit;
            }

            $usuarioDAO->confirmarTransaccion();

            header("Location: index.php?controller=auth&action=login&status=registro_exitoso");
            exit;
        }

        require_once "views/auth/registro.php";
    }

    // Envía a cada rol a su área correspondiente tras un login exitoso.
    private function redirigirSegunRol(string $nombreRol): void
    {
        if ($nombreRol === 'Administrador' || $nombreRol === 'Colaborador') {
            header("Location: index.php?controller=admin&action=dashboard");
        } else {
            header("Location: index.php?controller=area-cliente&action=inicio");
        }
        exit;
    }
}
