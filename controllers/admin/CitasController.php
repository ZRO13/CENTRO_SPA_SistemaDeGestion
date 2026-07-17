<?php
require_once "models/dao/Citas/CitaDAO.php";

require_once "models/dto/cita/Cita.php";
require_once "models/dao/Servicios/ServicioDAO.php";
require_once "controllers/SesionHelper.php";
require_once "models/dao/usuarios/ClienteDAO.php";

class CitasController 
{
    private CitaDAO $citaDAO;
    private ServicioDAO $servicioDAO;

    public function __construct() 
    {
        $this->citaDAO = new CitaDAO();
        $this->servicioDAO = new ServicioDAO();
    }

    public function index() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        SesionHelper::requerirRol(['Administrador']);

        $mostrarMenu = true;

        $citas = $this->citaDAO->listar();
        require_once "views/admin/citas_gestion.php";
    }

    public function crear() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        SesionHelper::requerirRol(['Cliente']);

        $servicios = $this->servicioDAO->listarPorCategoria(1);
        $tratamientos = $this->servicioDAO->listarPorCategoria(2);
        require_once "views/cliente/citas/cita_reserva.php";
    }

    public function miAgenda() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        SesionHelper::requerirAutenticacion();
        
        $usuarioLogueado = SesionHelper::usuarioActual();
        $clienteDAO = new ClienteDAO();
        $cliente = $clienteDAO->buscarPorIdUsuario($usuarioLogueado['id_usuario']);
        
            if (!$cliente) {
                echo "Error: No se encontró un perfil de cliente asociado a este usuario.";
                exit(); 
            }
        $id_cliente = $cliente->getIdCliente();
        
        $citasRaw = $this->citaDAO->listarPorCliente($id_cliente);
        
        $citasAgrupadas = [];
        
        foreach ($citasRaw as $cita) {
            $key = $cita['fecha'] . "_" . $cita['hora'];
            
            if (!isset($citasAgrupadas[$key])) {
                $citasAgrupadas[$key] = $cita;
                $citasAgrupadas[$key]['nombres_servicios'] = [$cita['nombre_servicio']];
            } else {
                $citasAgrupadas[$key]['nombres_servicios'][] = $cita['nombre_servicio'];
            }
        }
        
        require_once "views/cliente/citas/cita_agenda.php";
    }

    public function obtenerCitas() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');
        SesionHelper::requerirRol(['Administrador']);

        $fecha = filter_input(INPUT_GET, 'fecha', FILTER_SANITIZE_SPECIAL_CHARS);
        $buscar = filter_input(INPUT_GET, 'buscar', FILTER_SANITIZE_SPECIAL_CHARS);
        $estado = filter_input(INPUT_GET, 'estado', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'todos';

        $datos = $this->citaDAO->listar($fecha, $buscar, $estado);
        echo json_encode($datos);
        exit(); 
    }

    public function obtenerCitasCliente() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        SesionHelper::requerirAutenticacion();

        $usuarioLogueado = SesionHelper::usuarioActual();
        $clienteDAO = new ClienteDAO();
        $cliente = $clienteDAO->buscarPorIdUsuario($usuarioLogueado['id_usuario']);

        if (!$cliente) {
            echo json_encode([]);
            exit;
        }

        $id_cliente = $cliente->getIdCliente();

        $fecha = filter_input(INPUT_GET, 'fecha', FILTER_SANITIZE_SPECIAL_CHARS);
        $buscar = filter_input(INPUT_GET, 'buscar', FILTER_SANITIZE_SPECIAL_CHARS);
        $estado = filter_input(INPUT_GET, 'estado', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'todos';

        $datos = $this->citaDAO->listarPorCliente($id_cliente, $fecha, $buscar, $estado);

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($datos);
        exit(); 
    }

    public function actualizar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json; charset=utf-8');
        SesionHelper::requerirRol(['Administrador']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $id_cita = filter_input(INPUT_POST, 'id_cita', FILTER_VALIDATE_INT);
                $estado = filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS);
                $id_empleado = filter_input(INPUT_POST, 'id_empleado', FILTER_VALIDATE_INT);
                $observacion = filter_input(INPUT_POST, 'observacion', FILTER_SANITIZE_SPECIAL_CHARS);

                if (!$id_cita) {
                    throw new Exception("ID de cita inválido.");
                }

                $c = new Cita();
                $c->setIdCita($id_cita);
                $c->estado = $estado;

                $c->id_empleado = ($id_empleado > 0) ? $id_empleado : null;
                $c->observacion = $observacion;

                $resultado = $this->citaDAO->actualizarCita($c);

                if ($resultado) {
                    echo json_encode(['success' => true, 'message' => 'Cita actualizada correctamente.']);
                } else {
                    throw new Exception("No se pudo actualizar la cita.");
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
        exit();
    }
    
    public function obtenerEmpleados() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');
        SesionHelper::requerirRol(['Administrador']);

        $empleados = $this->citaDAO->obtenerEmpleados();
        echo json_encode($empleados);
        exit();
    }


    public function eliminar() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');
        SesionHelper::requerirRol(['Administrador']);

        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        
        if (!$id || intval($id) <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de cita inválido.']);
            return;
        }

        try {
            $resultado = $this->citaDAO->eliminarCita(intval($id));
            
            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Cita eliminada correctamente.']);
            } else {
                throw new Exception("No se pudo encontrar la cita para eliminar.");
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }    
        exit();
        
    }

    public function registrar() {
        if (ob_get_length()) ob_end_clean();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json');
        SesionHelper::requerirRol(['Cliente']);
        $servicios = $_POST['servicios'] ?? []; 

        if (empty($servicios)) {
            throw new Exception("Seleccione al menos un servicio.");
        }
        $usuarioLogueado = SesionHelper::usuarioActual();
        $id_usuario = $usuarioLogueado['id_usuario'];

        $clienteDAO = new ClienteDAO();
        $cliente = $clienteDAO->buscarPorIdUsuario($id_usuario);

        if (!$cliente) {
            echo json_encode(['success' => false, 'message' => 'Perfil de cliente no encontrado.']);
            exit;
        }

        $id_cliente_real = $cliente->getIdCliente();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $fecha = filter_input(INPUT_POST, 'fecha', FILTER_SANITIZE_SPECIAL_CHARS);
                $hora = filter_input(INPUT_POST, 'hora', FILTER_SANITIZE_SPECIAL_CHARS);
                $servicios = $_POST['servicios'] ?? [];

                if (empty($fecha) || empty($hora) || empty($servicios)) {
                    throw new Exception("Datos incompletos. Por favor, llene todos los campos.");
                }

                if (strtotime($fecha) < strtotime(date('Y-m-d'))) {
                    throw new Exception("La fecha no puede ser anterior a hoy.");
                }

               foreach ($servicios as $id_servicio_seleccionado) {
                $cita = new Cita();
                $cita->setIdCliente($id_cliente_real);
                $cita->setIdServicio((int)$id_servicio_seleccionado);
                $cita->setFecha($fecha);
                $cita->setHora($hora);
                $cita->setEstado('Pendiente');
                
                $this->citaDAO->insertar($cita);
               }
                echo json_encode(['success' => true, 'message' => 'Cita reservada correctamente.']);
                exit; 
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }        
        }
    }
   
}