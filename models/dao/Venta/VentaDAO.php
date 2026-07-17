<?php
// DAO administrativo para consultar las compras como ventas realizadas.


require_once "models/dao/BaseDAO.php";

class VentaDAO extends BaseDAO
{
    public function listar(): array
    {
        $sql = "SELECT
                    co.id_compra AS id_venta,
                    co.id_usuario,
                    co.fecha AS fecha_venta,
                    co.total,
                    co.estado,
                    CONCAT(u.nombre, ' ', u.apellido) AS cliente,
                    u.correo,
                    u.celular
                FROM compras co
                INNER JOIN usuarios u
                    ON co.id_usuario = u.id_usuario
                ORDER BY co.fecha DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $idVenta): array|false
    {
        $sql = "SELECT
                    co.id_compra AS id_venta,
                    co.id_usuario,
                    co.fecha AS fecha_venta,
                    co.total,
                    co.estado,
                    CONCAT(u.nombre, ' ', u.apellido) AS cliente,
                    u.correo,
                    u.celular
                FROM compras co
                INNER JOIN usuarios u
                    ON co.id_usuario = u.id_usuario
                WHERE co.id_compra = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idVenta]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscar(string $texto): array
    {
        $sql = "SELECT
                    co.id_compra AS id_venta,
                    co.fecha AS fecha_venta,
                    co.total,
                    co.estado,
                    CONCAT(u.nombre, ' ', u.apellido) AS cliente,
                    u.correo
                FROM compras co
                INNER JOIN usuarios u
                    ON co.id_usuario = u.id_usuario
                WHERE CONCAT(u.nombre, ' ', u.apellido) LIKE ?
                   OR u.correo LIKE ?
                   OR co.id_compra LIKE ?
                ORDER BY co.fecha DESC";

        $criterio = "%" . $texto . "%";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([
            $criterio,
            $criterio,
            $criterio
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}