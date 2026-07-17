<?php
require_once "controllers/SesionHelper.php";


require_once "models/dao/producto/ProductoDAO.php";
require_once "models/dao/compra/CompraDAO.php";

class CarritoController
{
    private ProductoDAO $productoDAO;
    private CompraDAO $compraDAO;

    public function __construct()
    {
        $db = new Database();
        $conexionCompartida = $db->conectar();
        $this->productoDAO = new ProductoDAO($conexionCompartida);
        $this->compraDAO = new CompraDAO($conexionCompartida);
        if(!isset($_SESSION['carrito'])){
            $_SESSION['carrito'] = [];
        }
    }

    public function agregar()
    {
        header("Content-Type: application/json");
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if(!$id){
            echo json_encode(["error"=>"Producto inválido"]);
            exit;
        }
        $producto = $this->productoDAO->buscarPorId($id);
        if(!$producto){
            echo json_encode(["error"=>"Producto no encontrado"]);
            exit;
        }
        if(isset($_SESSION['carrito'][$id])){
            $_SESSION['carrito'][$id]['cantidad']++;
        } else {
            $_SESSION['carrito'][$id]=[
                "id_producto"=>$producto['id_producto'],
                "nombre"=>$producto['nombre'],
                "precio"=>$producto['precio'],
                "imagen"=>$producto['imagen'],
                "cantidad"=>1
            ];
        }
        echo json_encode([
            "success"=>true,
            "carrito"=>$_SESSION['carrito']
        ]);
        exit;
    }

    public function obtener()
    {
        header("Content-Type: application/json");
        echo json_encode(array_values($_SESSION['carrito']));
        exit;
    }

    public function actualizar()
    {
        header("Content-Type: application/json");
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $cantidad = filter_input(INPUT_GET, 'cantidad', FILTER_VALIDATE_INT);
        if (!$id || $cantidad === false) {
        echo json_encode(["error" => "Datos inválidos"]);
        exit;
        }
        if(isset($_SESSION['carrito'][$id])){
            if($cantidad <= 0){
                unset($_SESSION['carrito'][$id]);
            } else {
                $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
            }
        }
        echo json_encode(["success"=>true]);
        exit;
    }

    public function eliminar()
    {
        header("Content-Type: application/json");
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            echo json_encode(["error" => "Producto inválido"]);
            exit;
        }
        if(isset($_SESSION['carrito'][$id])){
            unset($_SESSION['carrito'][$id]);
        }
        echo json_encode(["success"=>true]);
        exit;
    }
    
    public function confirmar()
    {
        header("Content-Type: application/json");
        SesionHelper::requerirRol(['Cliente']);
        $carrito = $_SESSION['carrito'];
        if(empty($carrito)){
            echo json_encode(["error"=>"El carrito está vacío"]);
            exit;
        }
        // 1. VALIDAR STOCK DISPONIBLE ANTES DE PROCESAR
        foreach($carrito as $p){
            $productoActual = $this->productoDAO->buscarPorId($p['id_producto']);
            if(!$productoActual){
                echo json_encode(["error"=>"El producto '{$p['nombre']}' ya no existe"]);
                exit;
            }
            if($productoActual['stock'] < $p['cantidad']){
                echo json_encode([
                    "error"=>"Stock insuficiente de '{$p['nombre']}'. Disponible: {$productoActual['stock']}"
                ]);
                exit;
            }
        }
        // 2. CALCULAR TOTAL
        $total = 0;
        foreach($carrito as $p){
            $total += $p['precio'] * $p['cantidad'];
        }
        // 3. TRANSACCIÓN: compra + detalle + descuento de stock
        try {
            $this->compraDAO->iniciarTransaccion();
            $idUsuario = $_SESSION['usuario']['id_usuario'];
            $idCompra = $this->compraDAO->insertarCompra($idUsuario, $total);
            foreach($carrito as $p){
                $subtotal = $p['precio'] * $p['cantidad'];
                $this->compraDAO->insertarDetalle(
                    $idCompra, $p['id_producto'], $p['cantidad'], $p['precio'], $subtotal
                );
                $descontado = $this->productoDAO->descontarStock($p['id_producto'], $p['cantidad']);
                if(!$descontado){
                    throw new Exception("No se pudo descontar el stock de '{$p['nombre']}'");
                }
            }
            $this->compraDAO->confirmarTransaccion();
        } catch(Exception $e){
            $this->compraDAO->cancelarTransaccion();
            echo json_encode(["error"=>"No se pudo completar la compra: ".$e->getMessage()]);
            exit;
        }
        // 4. VACIAR CARRITO
        $_SESSION['carrito'] = [];
        echo json_encode(["success"=>true, "idCompra"=>$idCompra]);
        exit;
    }
}