<?php


class DetalleCompra
{
    private $idDetalle;
    private $idCompra;
    private $idProducto;
    private int $cantidad;
    private $precioUnitario;
    private float $subtotal;
    public function getIdDetalle(){return $this->idDetalle;}
    public function setIdDetalle($idDetalle){$this->idDetalle = $idDetalle;}
    public function getIdCompra(){return $this->idCompra;}
    public function setIdCompra($idCompra){$this->idCompra = $idCompra;}
    public function getIdProducto(){return $this->idProducto;}
    public function setIdProducto($idProducto){$this->idProducto = $idProducto;}
    public function getCantidad(){return $this->cantidad;}
    public function setCantidad($cantidad){$this->cantidad = $cantidad;}
    public function getPrecioUnitario(){return $this->precioUnitario;}
    public function setPrecioUnitario($precioUnitario){$this->precioUnitario = $precioUnitario;}
    public function getSubtotal(){return $this->subtotal;}
    public function setSubtotal($subtotal){$this->subtotal = $subtotal;}
}