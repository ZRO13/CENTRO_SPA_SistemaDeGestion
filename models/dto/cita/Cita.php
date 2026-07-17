<?php
class Cita {
    
    public $id_cita;
    public $id_cliente;
    public $id_empleado;
    public $id_servicio;
    public $fecha;
    public $hora;
    public $estado;
    public $observacion;

    public function __construct($id_cliente = null, $id_servicio = null, $fecha = null, $hora = null) {
        $this->id_cliente = $id_cliente;
        $this->id_servicio = $id_servicio;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->estado = 'Pendiente'; // Valor por defecto según la base de datos
    }

    // Getters
    public function getIdCita() { return $this->id_cita; }
    public function getIdCliente() { return $this->id_cliente; }
    public function getIdEmpleado() { return $this->id_empleado; }
    public function getIdServicio() { return $this->id_servicio; }
    public function getFecha() { return $this->fecha; }
    public function getHora() { return $this->hora; }
    public function getEstado() { return $this->estado; }
    public function getObservacion() { return $this->observacion; }

    // Setters
    public function setIdCita($id_cita) { $this->id_cita = $id_cita; }
    public function setIdCliente($id_cliente) { $this->id_cliente = $id_cliente; }
    public function setIdEmpleado($id_empleado) { $this->id_empleado = $id_empleado; }
    public function setIdServicio($id_servicio) { $this->id_servicio = $id_servicio; }
    public function setFecha($fecha) { $this->fecha = $fecha; }
    public function setHora($hora) { $this->hora = $hora; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setObservacion($observacion) { $this->observacion = $observacion; }
}