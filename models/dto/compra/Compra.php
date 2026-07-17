<?php


class Compra
{
    private ?int $idCompra;
    private $idUsuario;
    private $fecha;
    private float $total;
    private string $estado;
    public function getIdCompra(){return $this->idCompra;}
    public function setIdCompra($idCompra){$this->idCompra = $idCompra;}
    public function getIdUsuario(){return $this->idUsuario;}
    public function setIdUsuario($idUsuario){$this->idUsuario = $idUsuario;}
    public function getFecha(){return $this->fecha;}
    public function setFecha($fecha){$this->fecha = $fecha;}
    public function getTotal(){return $this->total;}
    public function setTotal($total){$this->total = $total;}
    public function getEstado(){return $this->estado;}
    public function setEstado($estado){$this->estado = $estado;}
}