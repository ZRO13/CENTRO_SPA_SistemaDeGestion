<?php
require_once "models/dao/BaseDAO.php";


require_once "models/dto/cita/Cita.php";

class CitaDAO extends BaseDAO {
    
    public function __construct(?PDO $conexionCompartida = null)
    {
        parent::__construct();

        if ($conexionCompartida !== null) {
            $this->setConexion($conexionCompartida);
        }
    }

    public function listar($fecha = null, $buscar = null, $estado = 'todos') {
        $sql = "SELECT c.*, CONCAT(u.nombre, ' ', u.apellido) as nombre_cliente, 
                    s.nombre as nombre_servicio, 
                    e.nombre as nombre_empleado, 
                    e.apellido as apellido_empleado, 
                    emp.cargo as cargo_empleado
                FROM citas c 
                JOIN clientes cl ON c.id_cliente = cl.id_cliente
                JOIN usuarios u ON cl.id_usuario = u.id_usuario
                JOIN servicios s ON c.id_servicio = s.id_servicio
                LEFT JOIN empleados emp ON c.id_empleado = emp.id_empleado
                LEFT JOIN usuarios e ON emp.id_usuario = e.id_usuario
                WHERE 1=1";

        if ($fecha) $sql .= " AND c.fecha = :fecha";
        if ($estado && $estado !== 'todos') $sql .= " AND c.estado = :estado";
        if ($buscar) {
            $sql .= " AND (u.nombre LIKE :buscar OR u.apellido LIKE :buscar OR s.nombre LIKE :buscar OR e.nombre LIKE :buscar)";
        }

        $sql .= " ORDER BY c.fecha ASC, c.hora ASC";
        
        $stmt = $this->conexion->prepare($sql);
        
        if ($fecha) $stmt->bindValue(':fecha', $fecha);
        if ($estado && $estado !== 'todos') $stmt->bindValue(':estado', $estado);
        if ($buscar) $stmt->bindValue(':buscar', "%$buscar%");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function listarPorCliente($id_cliente, $fecha = null, $buscar = null, $estado = 'todos') {
        $sql = "SELECT c.*, CONCAT(u.nombre, ' ', u.apellido) as nombre_cliente, 
                    s.nombre as nombre_servicio, 
                    e.nombre as nombre_empleado, 
                    e.apellido as apellido_empleado, 
                    emp.cargo as cargo_empleado
                FROM citas c 
                JOIN clientes cl ON c.id_cliente = cl.id_cliente
                JOIN usuarios u ON cl.id_usuario = u.id_usuario
                JOIN servicios s ON c.id_servicio = s.id_servicio
                LEFT JOIN empleados emp ON c.id_empleado = emp.id_empleado
                LEFT JOIN usuarios e ON emp.id_usuario = e.id_usuario
                WHERE c.id_cliente = :id_cliente";

        // Agregar filtros dinámicos
        if ($fecha) $sql .= " AND c.fecha = :fecha";
        if ($estado && $estado !== 'todos') $sql .= " AND c.estado = :estado";
        if ($buscar) {
            $sql .= " AND (s.nombre LIKE :buscar OR e.nombre LIKE :buscar)";
        }

        $sql .= " ORDER BY c.fecha ASC, c.hora ASC";
        
        $stmt = $this->conexion->prepare($sql);
        
        // Bind obligatorio
        $stmt->bindValue(':id_cliente', $id_cliente);
        
        // Binds condicionales
        if ($fecha) $stmt->bindValue(':fecha', $fecha);
        if ($estado && $estado !== 'todos') $stmt->bindValue(':estado', $estado);
        if ($buscar) $stmt->bindValue(':buscar', "%$buscar%");
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function actualizarCita(Cita $c) 
    {
        $sql = "UPDATE citas 
                SET estado = :estado, 
                    id_empleado = :id_empleado, 
                    observacion = :observacion 
                WHERE id_cita = :id_cita";
                
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            ':estado' => $c->estado,
            ':id_empleado' => $c->id_empleado,
            ':observacion' => $c->observacion, 
            ':id_cita' => $c->id_cita
        ]);
    }
    
    public function obtenerEmpleados() {
        $sql = "SELECT e.id_empleado, u.nombre, u.apellido, e.cargo 
                FROM empleados e 
                JOIN usuarios u ON e.id_usuario = u.id_usuario";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerServicios() {
        $sql = "SELECT id_servicio, nombre FROM servicios";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function eliminarCita($id) {
        $sql = "DELETE FROM citas WHERE id_cita = :id";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function insertar(Cita $c) {
        $sql = "INSERT INTO citas (id_cliente, id_servicio, fecha, hora, estado) 
                VALUES (?, ?, ?, ?, 'Pendiente')";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            $c->getIdCliente(), 
            $c->getIdServicio(), 
            $c->getFecha(), 
            $c->getHora()
        ]);
    }
}