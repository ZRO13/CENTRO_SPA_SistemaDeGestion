<?php


// DTO (Model) - Capa de datos: representa un registro de la tabla 'roles'.
// Responsabilidad: únicamente contener atributos, constructor, getters y setters.

class Rol
{
    private ?int $idRol;
    private string $nombre;

    public function __construct(
        ?int $idRol = null,
        string $nombre = ''
    ) {
        $this->idRol = $idRol;
        $this->nombre = $nombre;
    }

    public function getIdRol(): ?int
    {
        return $this->idRol;
    }

    public function setIdRol(?int $idRol): void
    {
        $this->idRol = $idRol;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        $this->nombre = $nombre;
    }
}
