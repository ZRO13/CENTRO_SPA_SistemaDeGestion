<?php

require_once "controllers/SesionHelper.php";
require_once "models/dao/Venta/VentaDAO.php";
require_once "models/dao/Venta/DetalleVentaDAO.php";

class VentaController
{
    private VentaDAO $ventaDAO;
    private DetalleVentaDAO $detalleVentaDAO;

    public function __construct()
    {
        // Consistente con el resto de módulos administrativos: solo el
        // Administrador puede consultar el reporte de ventas.
        SesionHelper::requerirRol(['Administrador']);
        $this->ventaDAO = new VentaDAO();
        $this->detalleVentaDAO = new DetalleVentaDAO();
    }

    public function index(): void
    {
        $ventas = $this->ventaDAO->listar();

        require_once "views/admin/ventas.php";
    }

    public function detalle(): void
    {
        $idVenta = filter_input(
            INPUT_GET,
            "id",
            FILTER_VALIDATE_INT
        );

        if (!$idVenta) {
            die("ID de venta inválido.");
        }

        $venta = $this->ventaDAO->buscarPorId($idVenta);

        if (!$venta) {
            die("La venta no existe.");
        }

        $detalles =
            $this->detalleVentaDAO->listarPorVenta($idVenta);

        require_once "views/admin/ventas_detalle.php";
    }

    public function buscarAjax(): void
    {
        $texto = trim($_GET["texto"] ?? "");

        $ventas = $texto === ""
            ? $this->ventaDAO->listar()
            : $this->ventaDAO->buscar($texto);

        header("Content-Type: application/json; charset=utf-8");

        echo json_encode(
            $ventas,
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }
}