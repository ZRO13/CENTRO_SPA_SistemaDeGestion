<?php
require_once "controllers/SesionHelper.php";


require_once "models/dao/producto/ProductoDAO.php";
require_once "models/dto/productos/Producto.php";

class ProductoController
{
    private ProductoDAO $productoDAO;
    public function __construct()
    {
        // Consistente con el resto de módulos administrativos: solo el
        // Administrador puede gestionar el catálogo de productos.
        SesionHelper::requerirRol(['Administrador']);
        $this->productoDAO = new ProductoDAO();
    }

    // Mostrar módulo
    public function listar()
    {
        echo "<script>console.log('ProductoController: listar()');</script>";
        $mostrarMenu = true;
        $productos = $this->productoDAO->listar();
        $categorias = $this->productoDAO->listarCategorias();
        require_once "views/admin/productos.php";
    }

    // Buscar
    public function buscar()
    {
        $texto = trim($_POST["buscar"] ?? "");
        $productos = $this->productoDAO->buscar($texto);
        $categorias = $this->productoDAO->listarCategorias();
        require_once "views/admin/productos.php";
    }
     public function buscarAjax()
    {
        $texto = $_GET["texto"] ?? "";
        $productos = $this->productoDAO->buscar($texto);
        header("Content-Type: application/json");
        echo json_encode($productos);
        exit;
    }

    // Guardar
    public function guardar()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            header("Location:index.php?controller=producto&action=listar");
            exit;
        }
        $nombre = trim($_POST["nombre"]);
        $descripcion = trim($_POST["descripcion"]);
        $precio = filter_input(INPUT_POST, "precio", FILTER_VALIDATE_FLOAT);
        $stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);
        $imagen = trim($_POST["imagen"]);
        $categoria = filter_input(INPUT_POST, "categoria", FILTER_VALIDATE_INT);
        if (
            empty($nombre) ||
            !$categoria ||
            $precio === false ||
            $precio <= 0 ||
            $stock === false ||
            $stock < 0
        ) {
            die("Datos inválidos.");
        }
        if (!empty($imagen) && !filter_var($imagen, FILTER_VALIDATE_URL)) {
            die("La URL de la imagen no es válida.");
        }
        $producto = new Producto();
        $producto->setIdCategoriaProducto($categoria);;
        $producto->setNombre(trim($_POST["nombre"]));
        $producto->setDescripcion(trim($_POST["descripcion"]));
        $producto->setPrecio($_POST["precio"]);
        $producto->setStock($_POST["stock"]);
        $disponibilidad = filter_input(INPUT_POST, "disponibilidad", FILTER_VALIDATE_INT);
        $producto->setDisponibilidad($disponibilidad ?? 1);;
        $producto->setImagen(trim($_POST["imagen"]));
        $this->productoDAO->insertar($producto);
        $_SESSION["success"] = "Producto registrado correctamente.";
        header("Location:index.php?controller=producto&action=listar");
        exit;
    }

    // Editar
    public function editar()
    {
        $id = $_GET["id"];
        if (!$id) {
            header("Location:index.php?controller=producto&action=listar");
            exit;
        }
        $productoEditar = $this->productoDAO->buscarPorId($id);
        $productos = $this->productoDAO->listar();
        $categorias = $this->productoDAO->listarCategorias();
        require_once "views/admin/productos.php";
    }

    // Actualizar
    public function actualizar()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
        header("Location:index.php?controller=producto&action=listar");
        exit;
        }
        $nombre = trim($_POST["nombre"]);
        $descripcion = trim($_POST["descripcion"]);
        $precio = filter_input(INPUT_POST, "precio", FILTER_VALIDATE_FLOAT);
        $stock = filter_input(INPUT_POST, "stock", FILTER_VALIDATE_INT);
        $imagen = trim($_POST["imagen"]);

        $categoria = filter_input(INPUT_POST, "categoria", FILTER_VALIDATE_INT);
        if (
            empty($nombre) ||
            !$categoria ||
            $precio === false ||
            $precio <= 0 ||
            $stock === false ||
            $stock < 0
        ) {
            die("Datos inválidos.");
        }
        if (!empty($imagen) && !filter_var($imagen, FILTER_VALIDATE_URL)) {
            die("La URL de la imagen no es válida.");
        }
        $producto = new Producto();
        $producto->setIdProducto($_POST["id"]);
        $producto->setIdCategoriaProducto($categoria);
        $producto->setNombre(trim($_POST["nombre"]));
        $producto->setDescripcion(trim($_POST["descripcion"]));
        $producto->setPrecio($_POST["precio"]);
        $producto->setStock($_POST["stock"]);
        $disponibilidad = filter_input(INPUT_POST, "disponibilidad", FILTER_VALIDATE_INT);
    $producto->setDisponibilidad($disponibilidad ?? 1);
        $producto->setImagen(trim($_POST["imagen"]));
        $this->productoDAO->actualizar($producto);
        $_SESSION["success"] = "Producto actualizado correctamente.";
        header("Location:index.php?controller=producto&action=listar");
        exit;
    }

    // Eliminar
    public function eliminar()
    {
        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
        if (!$id) {
            header("Location:index.php?controller=producto&action=listar");
            exit;
        }
        if ($this->productoDAO->tieneVentas($id)) {
            $_SESSION["error"] =
                "No se puede eliminar el producto porque ya registra ventas.";

            header("Location:index.php?controller=producto&action=listar");
            exit;
        }
        $this->productoDAO->eliminar($id);
        $_SESSION["success"] = "Producto eliminado correctamente.";
        header("Location:index.php?controller=producto&action=listar");
        exit;
    }
}