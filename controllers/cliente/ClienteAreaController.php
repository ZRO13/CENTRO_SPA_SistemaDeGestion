<?php
// CONTROLLER - Área privada del Cliente autenticado. Expone el Inicio del
// cliente y el historial de compras (CompraDAO), reutilizando las vistas
// ya construidas en views/cliente/producto-compra/.


require_once "controllers/SesionHelper.php";
require_once "models/dao/compra/CompraDAO.php";

class ClienteAreaController
{
    private CompraDAO $compraDAO;

    public function __construct()
    {
        SesionHelper::requerirRol(['Cliente']);
        $this->compraDAO = new CompraDAO();
    }

    public function inicio()
    {
        $usuario = SesionHelper::usuarioActual();
        require_once "views/cliente/inicio.php";
    }

    public function compras()
    {
        $usuario = SesionHelper::usuarioActual();
        $compras = $this->compraDAO->listarPorUsuario($usuario['id_usuario']);
        require_once "views/cliente/producto-compra/compras.php";
    }

    public function detalleCompra()
    {
        $idCompra = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$idCompra) {
            header("Location: index.php?controller=area-cliente&action=compras");
            exit;
        }

        $compra = $this->compraDAO->buscarPorId($idCompra);
        $usuario = SesionHelper::usuarioActual();

        if (!$compra || (int)$compra['id_usuario'] !== (int)$usuario['id_usuario']) {
            header("Location: index.php?controller=area-cliente&action=compras");
            exit;
        }

        $detalle = $this->compraDAO->detalleCompra($idCompra);
        require_once "views/cliente/producto-compra/detalleCompra.php";
    }
}
