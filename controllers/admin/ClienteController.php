<?php


// CONTROLLER - Gestiona el CRUD de clientes desde el panel administrativo.
// Coordina la creación simultánea del usuario (datos personales + acceso)
// y de su fila en 'clientes' dentro de una transacción.

require_once "controllers/SesionHelper.php";
require_once "models/dao/usuarios/ClienteDAO.php";
require_once "models/dao/usuarios/UsuarioDAO.php";
require_once "models/dao/usuarios/RolDAO.php";
require_once "models/dto/usuarios/cliente.php";
require_once "models/dto/usuarios/usuario.php";

class ClienteController
{
    private const ROL_CLIENTE = 'Cliente';

    private ClienteDAO $clienteDAO;
    private UsuarioDAO $usuarioDAO;

    public function __construct()
    {
        SesionHelper::requerirRol(['Administrador']);
        $this->clienteDAO = new ClienteDAO();
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function listar()
    {
        $mostrarMenu = true;
        $termino = trim($_GET['termino'] ?? '');

        $clientes = $termino !== ''
            ? $this->clienteDAO->buscar($termino)
            : $this->clienteDAO->listar();

        require_once "views/admin/clientes_crud.php";
    }

    public function registrarCliente()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->leerDatosPersonales();

            $error = $this->validarDatosPersonales($datos, null);

            if ($error !== null) {
                header("Location: index.php?controller=cliente&action=listar&status={$error}");
                exit;
            }

            $rolCliente = (new RolDAO())->buscarPorNombre(self::ROL_CLIENTE);

            if (!$rolCliente) {
                header("Location: index.php?controller=cliente&action=listar&status=error");
                exit;
            }

            $usuario = new Usuario(
                null,
                $rolCliente->getIdRol(),
                $datos['nombre'],
                $datos['apellido'],
                $datos['correo'],
                $datos['celular'],
                $datos['username'],
                password_hash($datos['password'], PASSWORD_BCRYPT),
                true
            );

            // Transacción: usuario + cliente deben crearse de forma atómica.
            $this->usuarioDAO->iniciarTransaccion();
            $conexionCompartida = $this->usuarioDAO->getConexion();

            $idUsuario = $this->usuarioDAO->insertar($usuario);

            if ($idUsuario === null) {
                $this->usuarioDAO->cancelarTransaccion();
                header("Location: index.php?controller=cliente&action=listar&status=error");
                exit;
            }

            $clienteDAOTransaccional = new ClienteDAO($conexionCompartida);
            $cliente = new Cliente(null, $idUsuario);
            $registrado = $clienteDAOTransaccional->insertar($cliente);

            if (!$registrado) {
                $this->usuarioDAO->cancelarTransaccion();
                header("Location: index.php?controller=cliente&action=listar&status=error");
                exit;
            }

            $this->usuarioDAO->confirmarTransaccion();
            header("Location: index.php?controller=cliente&action=listar&status=success");
            exit;
        }

        header("Location: index.php?controller=cliente&action=listar");
        exit;
    }

    public function editar()
    {
        $idCliente = (int)($_GET['id'] ?? 0);
        $cliente = $this->clienteDAO->buscarPorId($idCliente);

        if (!$cliente) {
            header("Location: index.php?controller=cliente&action=listar&status=no_encontrado");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->leerDatosPersonales();

            $error = $this->validarDatosPersonales($datos, $cliente->getIdUsuario());

            if ($error !== null) {
                header("Location: index.php?controller=cliente&action=editar&id={$idCliente}&status={$error}");
                exit;
            }

            $usuario = $this->usuarioDAO->buscarPorId($cliente->getIdUsuario());
            $usuario->setNombre($datos['nombre']);
            $usuario->setApellido($datos['apellido']);
            $usuario->setCorreo($datos['correo']);
            $usuario->setCelular($datos['celular']);
            $usuario->setUsername($datos['username']);

            $actualizado = $this->usuarioDAO->actualizar($usuario);

            $status = $actualizado ? 'updated' : 'error';
            header("Location: index.php?controller=cliente&action=listar&status={$status}");
            exit;
        }

        // GET: muestra el formulario de edición precargado con los datos actuales.
        require_once "views/admin/cliente_editar.php";
    }

    // Baja lógica: se desactiva el usuario asociado (bloquea también su acceso).
    public function eliminar()
    {
        $idCliente = (int)($_GET['id'] ?? 0);
        $cliente = $this->clienteDAO->buscarPorId($idCliente);

        $resultado = $cliente ? $this->usuarioDAO->eliminar($cliente->getIdUsuario()) : false;

        $status = $resultado ? 'deleted' : 'error';
        header("Location: index.php?controller=cliente&action=listar&status={$status}");
        exit;
    }

    // --- MÉTODOS PRIVADOS DE APOYO (evitan duplicar validación entre registrar/editar) ---

    private function leerDatosPersonales(): array
    {
        return [
            'nombre'   => trim($_POST['nombre'] ?? ''),
            'apellido' => trim($_POST['apellido'] ?? ''),
            'correo'   => trim($_POST['correo'] ?? ''),
            'celular'  => trim($_POST['celular'] ?? ''),
            'username' => trim($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
        ];
    }

    private function validarDatosPersonales(array $datos, ?int $idUsuarioActual): ?string
    {
        if ($datos['nombre'] === '' || $datos['apellido'] === '' || $datos['correo'] === '' || $datos['username'] === '') {
            return 'campos_vacios';
        }

        if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            return 'correo_invalido';
        }

        if ($idUsuarioActual === null && strlen($datos['password']) < 6) {
            return 'password_corta';
        }

        if ($this->usuarioDAO->existeCorreo($datos['correo'], $idUsuarioActual)) {
            return 'correo_existente';
        }

        if ($this->usuarioDAO->existeUsername($datos['username'], $idUsuarioActual)) {
            return 'username_existente';
        }

        return null;
    }
}
