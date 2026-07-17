<?php
require_once "config/Database.php";

require_once "models/dto/productos/Producto.php";
class ProductoDAO
{
    private PDO $cn;
    public function __construct(?PDO $conexion = null)
    {
        if($conexion !== null){
        $this->cn = $conexion;
        } else {
            $db = new Database();
            $this->cn = $db->conectar();
        }
    }

    // Listar todos los productos
    public function listar()
    {
        $sql = "SELECT
                p.*,
                c.nombre_categoria
                FROM productos p
                INNER JOIN categorias_producto c
                ON p.id_categoria_producto = c.id_categoria_producto
                ORDER BY p.nombre";
                $stmt = $this->cn->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar productos
    public function buscar(string $texto)
    {
        $sql = "SELECT
                    p.*,
                    c.nombre_categoria
                FROM productos p
                INNER JOIN categorias_producto c
                ON p.id_categoria_producto=c.id_categoria_producto
                WHERE p.nombre LIKE ?
                ORDER BY p.nombre";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute(["%".$texto."%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorTexto(string $texto)
    {
        $sql = "SELECT p.id_producto,
                       c.nombre_categoria,
                       p.id_categoria_producto,
                       p.nombre,
                       p.descripcion,
                       p.precio,
                       p.stock,
                       p.disponibilidad,
                       p.imagen
                FROM productos p
                INNER JOIN categorias_producto c
                    ON p.id_categoria_producto = c.id_categoria_producto
                WHERE p.nombre LIKE ?
                   OR c.nombre_categoria LIKE ?
                ORDER BY p.nombre";
        $criterio = "%{$texto}%";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute([
            $criterio,
            $criterio
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un producto por ID
    public function buscarPorId(int $id)
    {
        $sql = "SELECT
                p.*,
                c.nombre_categoria
                FROM productos p
                INNER JOIN categorias_producto c
                    ON p.id_categoria_producto = c.id_categoria_producto
                WHERE p.id_producto = ?";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarCliente(string $texto = "", int $categoria = 0)
    {
        $sql = "SELECT
                    p.*,
                    c.nombre_categoria
                FROM productos p
                INNER JOIN categorias_producto c
                    ON p.id_categoria_producto = c.id_categoria_producto
                WHERE p.disponibilidad = 1";
        $parametros = [];
        if ($texto != "") {
            $sql .= " AND p.nombre LIKE ?";
            $parametros[] = "%".$texto."%";
        }
        if ($categoria > 0) {
            $sql .= " AND p.id_categoria_producto = ?";
            $parametros[] = $categoria;
        }
        $sql .= " ORDER BY p.nombre";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute($parametros);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Insertar producto
    public function insertar(Producto $producto)
    {
        $sql = "INSERT INTO productos
                (
                    id_categoria_producto,
                    nombre,
                    descripcion,
                    precio,
                    stock,
                    disponibilidad,
                    imagen
                )
                VALUES (?,?,?,?,?,?,?)";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute([
            $producto->getIdCategoriaProducto(),
            $producto->getNombre(),
            $producto->getDescripcion(),
            $producto->getPrecio(),
            $producto->getStock(),
            $producto->getDisponibilidad(),
            $producto->getImagen()
        ]);
        return $stmt->rowCount();
    }

    // Actualizar producto
    public function actualizar(Producto $producto)
    {
        $sql = "UPDATE productos
                SET id_categoria_producto=?,
                    nombre=?,
                    descripcion=?,
                    precio=?,
                    stock=?,
                    disponibilidad=?,
                    imagen=?
                WHERE id_producto=?";

        $stmt = $this->cn->prepare($sql);

        $stmt->execute([
            $producto->getIdCategoriaProducto(),
            $producto->getNombre(),
            $producto->getDescripcion(),
            $producto->getPrecio(),
            $producto->getStock(),
            $producto->getDisponibilidad(),
            $producto->getImagen(),
            $producto->getIdProducto()
        ]);
        return $stmt->rowCount();
    }

    // Descontar stock tras una venta (usado por CarritoController::confirmar)
    public function descontarStock(int $idProducto, int $cantidad): bool
    {
        $sql = "UPDATE productos
                SET stock = stock - ?
                WHERE id_producto = ?
                AND stock >= ?";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute([$cantidad, $idProducto, $cantidad]);
        return $stmt->rowCount() > 0;
    }

    public function tieneVentas(int $idProducto): bool
    {
        $sql = "SELECT COUNT(*) 
                FROM detalle_compra
                WHERE id_producto = ?";

        $stmt = $this->cn->prepare($sql);
        $stmt->execute([$idProducto]);

        return $stmt->fetchColumn() > 0;
    }
    
    // Eliminar producto
    public function eliminar(int $id)
    {
        $sql = "DELETE FROM productos
                WHERE id_producto=?";
        $stmt = $this->cn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    // Listar categorías para el SELECT
    public function listarCategorias(): array
    {
        $sql = "SELECT id_categoria_producto,
                nombre_categoria
                FROM categorias_producto
                WHERE estado=1
                ORDER BY nombre_categoria;";

        $stmt = $this->cn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}