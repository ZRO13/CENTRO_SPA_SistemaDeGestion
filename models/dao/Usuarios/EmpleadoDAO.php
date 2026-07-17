<?php
// DAO - Capa de acceso a datos: ejecuta las consultas PDO sobre la tabla
// 'empleados'. Los datos personales viven en 'usuarios', por lo que todas
// las consultas de lectura se resuelven mediante INNER JOIN.




require_once "models/dao/BaseDAO.php";
require_once "models/dto/usuarios/empleado.php";

class EmpleadoDAO extends BaseDAO
{
    public function __construct(?PDO $conexionCompartida = null)
    {
        parent::__construct();
        if ($conexionCompartida !== null) {
            $this->setConexion($conexionCompartida);
        }
    }

    private function mapearFila(array $fila): Empleado
    {
        return new Empleado(
            (int)$fila['id_empleado'],
            (int)$fila['id_usuario'],
            $fila['cargo'],
            $fila['fecha_ingreso'],
            $fila['nombre'],
            $fila['apellido'],
            $fila['correo'],
            $fila['celular'],
            $fila['username'],
            (bool)$fila['estado']
        );
    }

    private const SELECT_BASE = "SELECT e.*, u.nombre, u.apellido, u.correo, u.celular, u.username, u.estado
                                  FROM empleados e
                                  INNER JOIN usuarios u ON e.id_usuario = u.id_usuario";

    public function listar(): array
    {
        $lista = [];
        try {
            $sql = self::SELECT_BASE . " ORDER BY u.nombre, u.apellido";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            while ($fila = $stmt->fetch()) {
                $lista[] = $this->mapearFila($fila);
            }
        } catch (PDOException $e) {
            error_log("Error en EmpleadoDAO::listar -> " . $e->getMessage());
        }
        return $lista;
    }

    public function buscar(string $termino): array
    {
        $lista = [];
        try {
            $sql = self::SELECT_BASE . " WHERE u.nombre LIKE :termino1
                       OR u.apellido LIKE :termino2
                       OR u.correo LIKE :termino3
                       OR e.cargo LIKE :termino4
                    ORDER BY u.nombre, u.apellido";

            $stmt = $this->conexion->prepare($sql);
            $comodin = "%{$termino}%";
            $stmt->execute([
                ':termino1' => $comodin,
                ':termino2' => $comodin,
                ':termino3' => $comodin,
                ':termino4' => $comodin,
            ]);

            while ($fila = $stmt->fetch()) {
                $lista[] = $this->mapearFila($fila);
            }
        } catch (PDOException $e) {
            error_log("Error en EmpleadoDAO::buscar -> " . $e->getMessage());
        }
        return $lista;
    }

    public function buscarPorId(int $idEmpleado): ?Empleado
    {
        try {
            $sql = self::SELECT_BASE . " WHERE e.id_empleado = :id_empleado";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_empleado' => $idEmpleado]);

            $fila = $stmt->fetch();
            return $fila ? $this->mapearFila($fila) : null;
        } catch (PDOException $e) {
            error_log("Error en EmpleadoDAO::buscarPorId -> " . $e->getMessage());
            return null;
        }
    }

    public function buscarPorIdUsuario(int $idUsuario): ?Empleado
    {
        try {
            $sql = self::SELECT_BASE . " WHERE e.id_usuario = :id_usuario";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);

            $fila = $stmt->fetch();
            return $fila ? $this->mapearFila($fila) : null;
        } catch (PDOException $e) {
            error_log("Error en EmpleadoDAO::buscarPorIdUsuario -> " . $e->getMessage());
            return null;
        }
    }

    // Inserta la fila de 'empleados' asociada a un usuario ya creado
    // (el registro en 'usuarios' se realiza previamente desde el Controller).
    public function insertar(Empleado $empleado): bool
    {
        try {
            $sql = "INSERT INTO empleados (id_usuario, cargo, fecha_ingreso)
                    VALUES (:id_usuario, :cargo, :fecha_ingreso)";

            $stmt = $this->conexion->prepare($sql);

            return $stmt->execute([
                ':id_usuario'    => $empleado->getIdUsuario(),
                ':cargo'         => $empleado->getCargo(),
                ':fecha_ingreso' => $empleado->getFechaIngreso()
            ]);
        } catch (PDOException $e) {
            error_log("Error en EmpleadoDAO::insertar -> " . $e->getMessage());
            return false;
        }
    }

    public function actualizar(Empleado $empleado): bool
    {
        try {
            $sql = "UPDATE empleados SET
                        cargo = :cargo,
                        fecha_ingreso = :fecha_ingreso
                    WHERE id_empleado = :id_empleado";

            $stmt = $this->conexion->prepare($sql);

            return $stmt->execute([
                ':cargo'         => $empleado->getCargo(),
                ':fecha_ingreso' => $empleado->getFechaIngreso(),
                ':id_empleado'   => $empleado->getIdEmpleado()
            ]);
        } catch (PDOException $e) {
            error_log("Error en EmpleadoDAO::actualizar -> " . $e->getMessage());
            return false;
        }
    }

    // La eliminación real del registro se maneja desactivando el usuario
    // asociado (ver UsuarioDAO::eliminar). Este método queda disponible
    // por si se requiere una baja física del registro de 'empleados'.
    public function eliminar(int $idEmpleado): bool
    {
        try {
            $sql = "DELETE FROM empleados WHERE id_empleado = :id_empleado";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([':id_empleado' => $idEmpleado]);
        } catch (PDOException $e) {
            error_log("Error en EmpleadoDAO::eliminar -> " . $e->getMessage());
            return false;
        }
    }
}
