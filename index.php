<?php


session_start();


$rutasControllers = [
    // Módulo de autenticación
    'auth'         => ['clase' => 'AuthController',        'ruta' => 'controllers/auth/AuthController.php'],

    // Panel administrativo
    'admin'        => ['clase' => 'AdminController',       'ruta' => 'controllers/admin/AdminController.php'],
    'usuario'      => ['clase' => 'UsuarioController',     'ruta' => 'controllers/admin/UsuarioController.php'],
    'empleado'     => ['clase' => 'EmpleadoController',    'ruta' => 'controllers/admin/EmpleadoController.php'],
    'cliente'      => ['clase' => 'ClienteController',     'ruta' => 'controllers/admin/ClienteController.php'],
    'citas'        => ['clase' => 'CitasController',       'ruta' => 'controllers/admin/CitasController.php'],
    'servicio'     => ['clase' => 'ServicioController',    'ruta' => 'controllers/admin/ServicioController.php'],
    'producto'     => ['clase' => 'ProductoController',    'ruta' => 'controllers/admin/ProductoController.php'],
    'venta'        => ['clase' => 'VentaController',       'ruta' => 'controllers/admin/VentaController.php'],

    // Sitio público (visitantes sin autenticar)
    'sitio'        => ['clase' => 'SitioController',       'ruta' => 'controllers/publico/SitioController.php'],

    // Área privada del cliente autenticado
    'area-cliente' => ['clase' => 'ClienteAreaController', 'ruta' => 'controllers/cliente/ClienteAreaController.php'],
    'clienteProd'  => ['clase' => 'ClienteProdController', 'ruta' => 'controllers/cliente/ClienteProdController.php'],
    'carrito'      => ['clase' => 'CarritoController',     'ruta' => 'controllers/cliente/CarritoController.php'],
];


$controllerKey = $_GET['controller'] ?? 'sitio';
$action = $_GET['action'] ?? 'inicio';

if (!isset($rutasControllers[$controllerKey])) {
    http_response_code(404);
    die("El controlador solicitado no existe.");
}

$definicion = $rutasControllers[$controllerKey];
$controllerPath = $definicion['ruta'];
$controllerName = $definicion['clase'];

if (!file_exists($controllerPath)) {
    http_response_code(500);
    die("El archivo del controlador no fue encontrado.");
}

require_once $controllerPath;
$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    die("La acción solicitada no existe.");
}

$controller->$action();
