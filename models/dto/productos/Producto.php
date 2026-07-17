<?php
class Producto

{
    private ?int $idProducto;
    private int $idCategoriaProducto;
    private string $nombre;
    private string $descripcion;
    private float $precio;
    private int $stock;
    private bool $disponibilidad;
    private string $imagen;

    public function __construct(
        ?int $idProducto = null,
        int $idCategoriaProducto = 0,
        string $nombre = '',
        string $descripcion = '',
        float $precio = 0,
        int $stock = 0,
        bool $disponibilidad = true,
        string $imagen = ''
    ) {
        $this->idProducto = $idProducto;
        $this->idCategoriaProducto = $idCategoriaProducto;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->disponibilidad = $disponibilidad;
        $this->imagen = $imagen;
    }

    public function getIdProducto(): ?int
    {return $this->idProducto;}

    public function setIdProducto(?int $idProducto): void
    {$this->idProducto = $idProducto;}

    public function getIdCategoriaProducto(): int
    {return $this->idCategoriaProducto;}

    public function setIdCategoriaProducto(int $idCategoriaProducto): void
    {$this->idCategoriaProducto = $idCategoriaProducto;}

    public function getNombre(): string
    {return $this->nombre;}

    public function setNombre(string $nombre): void
    {$this->nombre = $nombre;}

    public function getDescripcion(): string
    {return $this->descripcion;}

    public function setDescripcion(string $descripcion): void
    {$this->descripcion = $descripcion;}

    public function getPrecio(): float
    {return $this->precio;}

    public function setPrecio(float $precio): void
    {$this->precio = $precio;}

    public function getStock(): int
    {return $this->stock;}

    public function setStock(int $stock): void
    {$this->stock = $stock;}

    public function getDisponibilidad(): bool
    {return $this->disponibilidad;}

    public function setDisponibilidad(bool $disponibilidad): void
    {$this->disponibilidad = $disponibilidad;}

    public function getImagen(): string
    {return $this->imagen;}

    public function setImagen(string $imagen): void
    {$this->imagen = $imagen;}
}