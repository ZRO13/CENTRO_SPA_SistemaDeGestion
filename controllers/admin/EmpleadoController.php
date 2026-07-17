<?php
// CONTROLLER - Gestiona el CRUD de empleados desde el panel administrativo.
// Coordina la creación simultánea del usuario (datos personales + acceso)
// y de su fila en 'empleados' (datos laborales) dentro de una transacción.


require_once "controllers/SesionHelper.php";
require_once "models/dao/Usuarios/EmpleadoDAO.php";
require_once "models/dao/Usuarios/UsuarioDAO.php";
require_once "models/dao/Usuarios/RolDAO.php";
require_once "models/dto/Usuarios/empleado.php";
require_once "models/dto/Usuarios/usuario.php";

class EmpleadoController
{
    private const ROL_COLABORADOR = 'Colaborador';

    private EmpleadoDAO $empleadoDAO;
    private UsuarioDAO $usuarioDAO;

    public function __construct()
    {
        SesionHelper::requerirRol(['Administrador']);
        $this->empleadoDAO = new EmpleadoDAO();
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function listar()
    {
        $mostrarMenu = true;
        $termino = trim($_GET['termino'] ?? '');

        $empleados = $termino !== ''
            ? $this->empleadoDAO->buscar($termino)
            : $this->empleadoDAO->listar();

        require_once "views/admin/empleados.php";
    }

    public function registrar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->leerDatosPersonales();
            $cargo = trim($_POST['cargo'] ?? '');
            $fechaIngreso = trim($_POST['fecha_ingreso'] ?? '');

            $error = $this->validarDatosPersonales($datos, null)
                ?? ($cargo === '' || $fechaIngreso === '' ? 'campos_vacios' : null);

            if ($error !== null) {
                header("Location: index.php?controller=empleado&action=listar&status={$error}");
                exit;
            }

            $rolColaborador = (new RolDAO())->buscarPorNombre(self::ROL_COLABORADOR);

            if (!$rolColaborador) {
                header("Location: index.php?controller=empleado&action=listar&status=error");
                exit;
            }

            $usuario = new Usuario(
                null,
                $rolColaborador->getIdRol(),
                $datos['nombre'],
                $datos['apellido'],
                $datos['correo'],
                $datos['celular'],
                $datos['username'],
                password_hash($datos['password'], PASSWORD_BCRYPT),
                true
            );

            // Transacción: si falla la inserción del empleado, no debe quedar
            // un usuario con rol Colaborador sin su fila laboral asociada.
            $this->usuarioDAO->iniciarTransaccion();
            $conexionCompartida = $this->usuarioDAO->getConexion();

            $idUsuario = $this->usuarioDAO->insertar($usuario);

            if ($idUsuario === null) {
                $this->usuarioDAO->cancelarTransaccion();
                header("Location: index.php?controller=empleado&action=listar&status=error");
                exit;
            }

            $empleadoDAOTransaccional = new EmpleadoDAO($conexionCompartida);
            $empleado = new Empleado(null, $idUsuario, $cargo, $fechaIngreso);
            $registrado = $empleadoDAOTransaccional->insertar($empleado);

            if (!$registrado) {
                $this->usuarioDAO->cancelarTransaccion();
                header("Location: index.php?controller=empleado&action=listar&status=error");
                exit;
            }

            $this->usuarioDAO->confirmarTransaccion();
            header("Location: index.php?controller=empleado&action=listar&status=success");
            exit;
        }

        header("Location: index.php?controller=empleado&action=listar");
        exit;
    }

    public function editar()
    {
        $idEmpleado = (int)($_GET['id'] ?? 0);
        $empleado = $this->empleadoDAO->buscarPorId($idEmpleado);

        if (!$empleado) {
            header("Location: index.php?controller=empleado&action=listar&status=no_encontrado");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->leerDatosPersonales();
            $cargo = trim($_POST['cargo'] ?? '');
            $fechaIngreso = trim($_POST['fecha_ingreso'] ?? '');

            $error = $this->validarDatosPersonales($datos, $empleado->getIdUsuario())
                ?? ($cargo === '' || $fechaIngreso === '' ? 'campos_vacios' : null);

            if ($error !== null) {
                header("Location: index.php?controller=empleado&action=editar&id={$idEmpleado}&status={$error}");
                exit;
            }

            $usuario = $this->usuarioDAO->buscarPorId($empleado->getIdUsuario());
            $usuario->setNombre($datos['nombre']);
            $usuario->setApellido($datos['apellido']);
            $usuario->setCorreo($datos['correo']);
            $usuario->setCelular($datos['celular']);
            $usuario->setUsername($datos['username']);

            $this->usuarioDAO->actualizar($usuario);

            $empleado->setCargo($cargo);
            $empleado->setFechaIngreso($fechaIngreso);
            $actualizado = $this->empleadoDAO->actualizar($empleado);

            $status = $actualizado ? 'updated' : 'error';
            header("Location: index.php?controller=empleado&action=listar&status={$status}");
            exit;
        }

        // GET: muestra el formulario de edición precargado con los datos actuales.
        require_once "views/admin/empleado_editar.php";
    }

    // Baja lógica: se desactiva el usuario asociado (bloquea también su acceso).
    public function eliminar()
    {
        $idEmpleado = (int)($_GET['id'] ?? 0);
        $empleado = $this->empleadoDAO->buscarPorId($idEmpleado);

        $resultado = $empleado ? $this->usuarioDAO->eliminar($empleado->getIdUsuario()) : false;

        $status = $resultado ? 'deleted' : 'error';
        header("Location: index.php?controller=empleado&action=listar&status={$status}");
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

    // Devuelve un código de error (string) o null si los datos son válidos.
    // $idUsuarioActual permite excluir al propio usuario al validar unicidad en edición.
    private function validarDatosPersonales(array $datos, ?int $idUsuarioActual): ?string
    {
        if ($datos['nombre'] === '' || $datos['apellido'] === '' || $datos['correo'] === '' || $datos['username'] === '') {
            return 'campos_vacios';
        }

        if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            return 'correo_invalido';
        }

        // En registro (idUsuarioActual === null) la contraseña es obligatoria.
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
