<?php
// DAO administrativo para consultar el detalle de ventas realizadas.


require_once "models/dao/BaseDAO.php";

class DetalleVentaDAO extends BaseDAO
{
    public function listarPorVenta(int $idVenta): array
    {
        $sql = "SELECT
                    dc.id_detalle AS id_detalle_venta,
                    dc.id_compra AS id_venta,
                    dc.id_producto,
                    dc.cantidad,
                    dc.precio_unitario,
                    dc.subtotal,
                    p.nombre AS producto,
                    p.imagen
                FROM detalle_compra dc
                INNER JOIN productos p
                    ON dc.id_producto = p.id_producto
                WHERE dc.id_compra = ?
                ORDER BY dc.id_detalle";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idVenta]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}