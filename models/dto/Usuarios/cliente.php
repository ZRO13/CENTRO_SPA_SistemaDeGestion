<?php


// DTO (Model) - Capa de datos: representa un registro de la tabla 'clientes'.
// Responsabilidad: únicamente contener atributos, constructor, getters y setters.
// Los datos personales (nombre, apellido, correo, celular) no pertenecen a esta
// tabla; se exponen aquí como atributos de solo lectura para facilitar su uso
// en las vistas, y se completan mediante el JOIN realizado en ClienteDAO.

class Cliente
{
    // Atributos propios de la tabla 'clientes'
    private ?int $idCliente;
    private int $idUsuario;

    // Atributos heredados de 'usuarios' (solo para lectura/visualización)
    private string $nombre;
    private string $apellido;
    private string $correo;
    private string $celular;
    private string $username;
    private bool $estado;

    public function __construct(
        ?int $idCliente = null,
        int $idUsuario = 0,
        string $nombre = '',
        string $apellido = '',
        string $correo = '',
        string $celular = '',
        string $username = '',
        bool $estado = true
    ) {
        $this->idCliente = $idCliente;
        $this->idUsuario = $idUsuario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->correo = $correo;
        $this->celular = $celular;
        $this->username = $username;
        $this->estado = $estado;
    }

    // --- GETTERS Y SETTERS PROPIOS ---
    public function getIdCliente(): ?int
    {
        return $this->idCliente;
    }

    public function setIdCliente(?int $idCliente): void
    {
        $this->idCliente = $idCliente;
    }

    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(int $idUsuario): void
    {
        $this->idUsuario = $idUsuario;
    }

    // --- GETTERS Y SETTERS DE RELACIÓN (USUARIOS) ---
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

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): void
    {
        $this->estado = $estado;
    }
}
