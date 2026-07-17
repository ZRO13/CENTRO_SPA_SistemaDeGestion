<?php
// DTO: representa una categoría del catálogo de servicios.

class CategoriaServicio
{
    private ?int $idCategoriaServicio;
    private string $nombreCategoria;
    private ?string $descripcion;
    private bool $estado;

    public function __construct(
        ?int $idCategoriaServicio = null,
        string $nombreCategoria = '',
        ?string $descripcion = null,
        bool $estado = true
    ) {
        $this->idCategoriaServicio = $idCategoriaServicio;
        $this->nombreCategoria = $nombreCategoria;
        $this->descripcion = $descripcion;
        $this->estado = $estado;
    }

    public function getIdCategoriaServicio(): ?int
    {
        return $this->idCategoriaServicio;
    }

    public function setIdCategoriaServicio(?int $idCategoriaServicio): void
    {
        $this->idCategoriaServicio = $idCategoriaServicio;
    }

    public function getNombreCategoria(): string
    {
        return $this->nombreCategoria;
    }

    public function setNombreCategoria(string $nombreCategoria): void
    {
        $this->nombreCategoria = $nombreCategoria;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): void
    {
        $this->descripcion = $descripcion;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): void
    {
        $this->estado = $estado;
    }
}
