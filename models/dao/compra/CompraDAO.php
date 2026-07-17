<?php
require_once "config/Database.php";

class CompraDAO
{
    private PDO $cn;
    public function __construct(?PDO $conexion = null)
    {
        if($conexion !== null){
            $this->cn = $conexion;
        } else {
            $db = new Database();
            $this->cn = $db->conectar();
        }
    }
    // Buscar una compra específica por su id (para validar pertenencia)
    public function buscarPorId($idCompra)
    {
        $sql = "
        SELECT *
        FROM compras
        WHERE id_compra = ?
        ";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute([$idCompra]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Crear compra y devolver ID generado
    public function insertarCompra($idUsuario, $total)
    {
        $sql = "
        INSERT INTO compras
        (
            id_usuario,
            total,
            estado
        )
        VALUES
        (
            ?,
            ?,
            'Pagada'
        )
        ";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute([
            $idUsuario,
            $total
        ]);
        return $this->cn->lastInsertId();
    }

    // Insertar detalle
    public function insertarDetalle(
        $idCompra,
        $idProducto,
        $cantidad,
        $precio,
        $subtotal
    )
    {
        $sql = "
        INSERT INTO detalle_compra
        (
            id_compra,
            id_producto,
            cantidad,
            precio_unitario,
            subtotal
        )
        VALUES
        (?,?,?,?,?)
        ";
        $stmt = $this->cn->prepare($sql);
        return $stmt->execute([
            $idCompra,
            $idProducto,
            $cantidad,
            $precio,
            $subtotal
        ]);
    }

    // Historial del cliente
    public function listarPorUsuario($idUsuario)
    {
        $sql = "
        SELECT *
        FROM compras
        WHERE id_usuario = ?
        ORDER BY fecha DESC
        ";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute([
            $idUsuario
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Detalle de una compra
    public function detalleCompra($idCompra)
    {
        $sql = "
        SELECT 
            dc.*,
            p.nombre,
            p.imagen
        FROM detalle_compra dc
        INNER JOIN productos p
        ON p.id_producto = dc.id_producto
        WHERE dc.id_compra = ?
        ";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute([
            $idCompra
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // TRANSACCIONES
    public function iniciarTransaccion()
    {
        $this->cn->beginTransaction();
    }
    public function confirmarTransaccion()
    {
        $this->cn->commit();
    }
    public function cancelarTransaccion()
    {
        if($this->cn->inTransaction()){
            $this->cn->rollBack();
        }
    }
}