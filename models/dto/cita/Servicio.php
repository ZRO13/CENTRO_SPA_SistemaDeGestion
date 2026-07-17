<?php
class Servicio {
    private $id_servicio;
    private $id_categoria_servicio;
    private $nombre;
    private $precio;
    private $disponibilidad;
    private $imagen;

    // Getters y Setters básicos
    public function getId() { return $this->id_servicio; }
    public function setId($id) { $this->id_servicio = $id; }
    public function getNombre() { return $this->nombre; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function getIdCategoria() { return $this->id_categoria_servicio; }
    public function setIdCategoria($id_categoria) { $this->id_categoria_servicio = $id_categoria; }
    public function getPrecio() { return $this->precio; }
    public function setPrecio($precio) { $this->precio = $precio; }
    public function getDisponibilidad() { return $this->disponibilidad; }
    public function setDisponibilidad($disponibilidad) { $this->disponibilidad = $disponibilidad; }
    public function getImagen() { return $this->imagen; }
    public function setImagen($imagen) { $this->imagen = $imagen; }

}