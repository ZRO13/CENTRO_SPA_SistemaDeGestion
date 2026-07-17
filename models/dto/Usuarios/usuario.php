<?php


// DTO (Model) - Capa de datos: representa un registro de la tabla 'usuarios'.
// Responsabilidad: únicamente contener atributos, constructor, getters y setters.

class Usuario
{
    private ?int $idUsuario;
    private int $idRol;
    private string $nombre;
    private string $apellido;
    private string $correo;
    private string $celular;
    private string $username;
    private string $password;
    private bool $estado;
    private string $fechaCreacion;

    // Campo de solo lectura, útil para mostrar el rol en listados sin
    // necesidad de una consulta adicional (se llena mediante JOIN en el DAO).
    private string $nombreRol;

    public function __construct(
        ?int $idUsuario = null,
        int $idRol = 0,
        string $nombre = '',
        string $apellido = '',
        string $correo = '',
        string $celular = '',
        string $username = '',
        string $password = '',
        bool $estado = true,
        string $fechaCreacion = '',
        string $nombreRol = ''
    ) {
        $this->idUsuario = $idUsuario;
        $this->idRol = $idRol;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->correo = $correo;
        $this->celular = $celular;
        $this->username = $username;
        $this->password = $password;
        $this->estado = $estado;
        $this->fechaCreacion = $fechaCreacion;
        $this->nombreRol = $nombreRol;
    }

    public function getIdUsuario(): ?int
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(?int $idUsuario): void
    {
        $this->idUsuario = $idUsuario;
    }

    public function getIdRol(): int
    {
        return $this->idRol;
    }

    public function setIdRol(int $idRol): void
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

    public function getApellido(): string
    {
        return $this->apellido;
    }

    public function setApellido(string $apellido): void
    {
        $this->apellido = $apellido;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): void
    {
        $this->correo = $correo;
    }

    public function getCelular(): string
    {
        return $this->celular;
    }

    public function setCelular(string $celular): void
    {
        $this->celular = $celular;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): void
    {
        $this->estado = $estado;
    }

    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(string $fechaCreacion): void
    {
        $this->fechaCreacion = $fechaCreacion;
    }

    public function getNombreRol(): string
    {
        return $this->nombreRol;
    }

    public function setNombreRol(string $nombreRol): void
    {
        $this->nombreRol = $nombreRol;
    }
}
