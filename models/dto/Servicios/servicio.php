<?php
// DTO: representa un registro de la tabla 'servicios'.
// nombreCategoria se completa mediante el JOIN del DAO y sirve para la vista.

class Servicio
{
    private ?int $idServicio;
    private int $idCategoriaServicio;
    private string $nombre;
    private ?string $descripcion;
    private float $precio;
    private bool $disponibilidad;
    private ?string $imagen;
    private string $nombreCategoria;

    public function __construct(
        ?int $idServicio = null,
        int $idCategoriaServicio = 0,
        string $nombre = '',
        ?string $descripcion = null,
        float $precio = 0.0,
        bool $disponibilidad = true,
        ?string $imagen = null,
        string $nombreCategoria = ''
    ) {
        $this->idServicio = $idServicio;
        $this->idCategoriaServicio = $idCategoriaServicio;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->disponibilidad = $disponibilidad;
        $this->imagen = $imagen;
        $this->nombreCategoria = $nombreCategoria;
    }

    public function getIdServicio(): ?int
    {
        return $this->idServicio;
    }

    public function setIdServicio(?int $idServicio): void
    {
        $this->idServicio = $idServicio;
    }

    public function getIdCategoriaServicio(): int
    {
        return $this->idCategoriaServicio;
    }

    public function setIdCategoriaServicio(int $idCategoriaServicio): void
    {
        $this->idCategoriaServicio = $idCategoriaServicio;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getPrecio(): float
    {
        return $this->precio;
    }

    public function setPrecio(float $precio): void
    {
        $this->precio = $precio;
    }

    public function getDisponibilidad(): bool
    {
        return $this->disponibilidad;
    }

    public function setDisponibilidad(bool $disponibilidad): void
    {
        $this->disponibilidad = $disponibilidad;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): void
    {
        $this->imagen = $imagen;
    }

    public function getNombreCategoria(): string
    {
        return $this->nombreCategoria;
    }

    public function setNombreCategoria(string $nombreCategoria): void
    {
        $this->nombreCategoria = $nombreCategoria;
    }
}
