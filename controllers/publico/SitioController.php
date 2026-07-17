<?php
// CONTROLLER - Sirve las páginas públicas del sitio (visitantes sin
// autenticar): el Home y el catálogo público de Servicios. Reutiliza los
// DAO de los módulos existentes sin duplicar datos.

require_once "models/dao/Servicios/ServicioDAO.php";
require_once "models/dao/Servicios/CategoriaServicioDAO.php";
require_once "models/dao/producto/ProductoDAO.php";

class SitioController
{
    private ServicioDAO $servicioDAO;

    public function __construct()
    {
        $this->servicioDAO = new ServicioDAO();
    }

    // Home: hero, presentación, servicios y productos destacados.
    public function inicio()
    {
        $productoDAO = new ProductoDAO();

        $serviciosDestacados = $this->servicioDAO->listarDisponibles(3);

        // Reutiliza ProductoDAO::listar() (ya usado por el CRUD de Productos)
        // y se queda solo con los primeros para la vitrina de "destacados".
        $productosDestacados = array_slice($productoDAO->listar(), 0, 3);

        require_once "views/publico/inicio.php";
    }

    // Catálogo público de servicios con búsqueda y filtro por categoría.
    public function servicios()
    {
        $categoriaDAO = new CategoriaServicioDAO();

        $termino = trim($_GET['termino'] ?? '');
        $idCategoria = (int)($_GET['categoria'] ?? 0);

        $categorias = $categoriaDAO->listarActivas();
        $servicios = $this->servicioDAO->filtrarDisponibles($termino, $idCategoria);

        require_once "views/publico/servicios.php";
    }
}
