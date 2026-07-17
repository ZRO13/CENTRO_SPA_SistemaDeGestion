<?php



// DAO - Capa de acceso a datos: consultas de solo lectura sobre 'roles'.
// La tabla roles no tiene CRUD (ver sql/spa_belleza_db.sql), por lo que
// este DAO únicamente expone métodos de consulta.

require_once "models/dao/BaseDAO.php";
require_once "models/dto/usuarios/rol.php";

class RolDAO extends BaseDAO
{
    public function __construct()
    {
        parent::__construct();
    }

    public function listar(): array
    {
        $lista = [];
        try {
            $sql = "SELECT * FROM roles ORDER BY nombre";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            while ($fila = $stmt->fetch()) {
                $lista[] = new Rol((int)$fila['id_rol'], $fila['nombre']);
            }
        } catch (PDOException $e) {
            error_log("Error en RolDAO::listar -> " . $e->getMessage());
        }
        return $lista;
    }

    public function buscarPorId(int $idRol): ?Rol
    {
        try {
            $sql = "SELECT * FROM roles WHERE id_rol = :id_rol";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_rol' => $idRol]);

            $fila = $stmt->fetch();

            if (!$fila) {
                return null;
            }

            return new Rol((int)$fila['id_rol'], $fila['nombre']);
        } catch (PDOException $e) {
            error_log("Error en RolDAO::buscarPorId -> " . $e->getMessage());
            return null;
        }
    }

    // Utilizado por el registro público (rol "Cliente") y por los
    // formularios de creación de empleados (rol "Colaborador").
    public function buscarPorNombre(string $nombre): ?Rol
    {
        try {
            $sql = "SELECT * FROM roles WHERE nombre = :nombre";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':nombre' => $nombre]);

            $fila = $stmt->fetch();

            if (!$fila) {
                return null;
            }

            return new Rol((int)$fila['id_rol'], $fila['nombre']);
        } catch (PDOException $e) {
            error_log("Error en RolDAO::buscarPorNombre -> " . $e->getMessage());
            return null;
        }
    }
}
