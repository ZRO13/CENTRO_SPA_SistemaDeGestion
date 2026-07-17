<?php


class Venta
{
    private $idVenta;
    private $idCliente;
    private $fechaVenta;
    private $total;
    private $metodoPago;
    private $estado;
    private $observacion;

    
    public function getIdVenta()
    {
        return $this->idVenta;
    }

    public function setIdVenta($idVenta)
    {
        $this->idVenta = $idVenta;
    }

    public function getIdCliente()
    {
        return $this->idCliente;
    }

    public function setIdCliente($idCliente)
    {
        $this->idCliente = $idCliente;
    }

    public function getFechaVenta()
    {
        return $this->fechaVenta;
    }

    public function setFechaVenta($fechaVenta)
    {
        $this->fechaVenta = $fechaVenta;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;
    }

    public function getMetodoPago()
    {
        return $this->metodoPago;
    }

    public function setMetodoPago($metodoPago)
    {
        $this->metodoPago = $metodoPago;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getObservacion()
    {
        return $this->observacion;
    }

    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;
    }
}