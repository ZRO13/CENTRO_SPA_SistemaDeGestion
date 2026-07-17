<?php



// CONTROLLER: gestiona el CRUD de servicios desde el panel administrativo.

require_once "controllers/SesionHelper.php";
require_once "models/dao/Servicios/ServicioDAO.php";
require_once "models/dao/Servicios/CategoriaServicioDAO.php";
require_once "models/dto/Servicios/servicio.php";

class ServicioController
{
    private ServicioDAO $servicioDAO;
    private CategoriaServicioDAO $categoriaDAO;

    public function __construct()
    {
        SesionHelper::requerirRol(['Administrador']);
        $this->servicioDAO = new ServicioDAO();
        $this->categoriaDAO = new CategoriaServicioDAO();
    }

    public function listar(): void
    {
        $this->cargarVista();
    }

    public function editar(): void
    {
        $idServicio = (int)($_GET['id'] ?? 0);
        $servicioEditar = $this->servicioDAO->buscarPorId($idServicio);

        if (!$servicioEditar) {
            $this->redirigir('no_encontrado');
        }

        $this->cargarVista($servicioEditar);
    }

    public function guardar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir();
        }

        $idServicio = (int)($_POST['id_servicio'] ?? 0);
        $servicioActual = $idServicio > 0
            ? $this->servicioDAO->buscarPorId($idServicio)
            : null;

        if ($idServicio > 0 && !$servicioActual) {
            $this->redirigir('no_encontrado');
        }

        $idCategoria = (int)($_POST['id_categoria_servicio'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precioTexto = trim($_POST['precio'] ?? '');
        $disponibilidad = ($_POST['disponibilidad'] ?? '1') === '1';

        if ($nombre === '' || $idCategoria <= 0 || $precioTexto === '') {
            $this->redirigir('campos_vacios', $idServicio);
        }

        if (strlen($nombre) > 100) {
            $this->redirigir('nombre_largo', $idServicio);
        }

        $precio = filter_var($precioTexto, FILTER_VALIDATE_FLOAT);
        if ($precio === false || $precio <= 0) {
            $this->redirigir('precio_invalido', $idServicio);
        }

        $categoria = $this->categoriaDAO->buscarPorId($idCategoria);
        if (!$categoria || !$categoria->getEstado()) {
            $this->redirigir('categoria_invalida', $idServicio);
        }

        $imagenAnterior = $servicioActual ? $servicioActual->getImagen() : null;
        $resultadoImagen = $this->procesarImagen($imagenAnterior);

        if ($resultadoImagen['error'] !== null) {
            $this->redirigir($resultadoImagen['error'], $idServicio);
        }

        $servicio = new Servicio(
            $idServicio > 0 ? $idServicio : null,
            $idCategoria,
            $nombre,
            $descripcion !== '' ? $descripcion : null,
            (float)$precio,
            $disponibilidad,
            $resultadoImagen['ruta']
        );

        $guardado = $idServicio > 0
            ? $this->servicioDAO->actualizar($servicio)
            : $this->servicioDAO->insertar($servicio);

        if (!$guardado) {
            if ($resultadoImagen['es_nueva']) {
                $this->eliminarArchivoImagen($resultadoImagen['ruta']);
            }
            $this->redirigir('error', $idServicio);
        }

        if ($resultadoImagen['es_nueva'] && $imagenAnterior !== null) {
            $this->eliminarArchivoImagen($imagenAnterior);
        }

        $this->redirigir($idServicio > 0 ? 'updated' : 'success');
    }

    // Baja lógica: mantiene el registro para no romper futuras relaciones con citas.
    public function eliminar(): void
    {
        $idServicio = (int)($_GET['id'] ?? 0);
        $servicio = $this->servicioDAO->buscarPorId($idServicio);

        $resultado = $servicio
            ? $this->servicioDAO->cambiarDisponibilidad($idServicio, false)
            : false;

        $this->redirigir($resultado ? 'deactivated' : 'error');
    }

    public function reactivar(): void
    {
        $idServicio = (int)($_GET['id'] ?? 0);
        $servicio = $this->servicioDAO->buscarPorId($idServicio);

        $resultado = $servicio
            ? $this->servicioDAO->cambiarDisponibilidad($idServicio, true)
            : false;

        $this->redirigir($resultado ? 'reactivated' : 'error');
    }

    private function cargarVista(?Servicio $servicioEditar = null): void
    {
        $mostrarMenu = true;
        $termino = trim($_GET['termino'] ?? '');
        $idCategoriaFiltro = (int)($_GET['categoria'] ?? 0);
        $disponibilidadTexto = $_GET['disponibilidad'] ?? '';
        $disponibilidadFiltro = in_array($disponibilidadTexto, ['0', '1'], true)
            ? (int)$disponibilidadTexto
            : null;

        $categorias = $this->categoriaDAO->listarActivas();
        $servicios = $this->servicioDAO->filtrar(
            $termino,
            $idCategoriaFiltro,
            $disponibilidadFiltro
        );

        require_once "views/admin/servicios_crud.php";
    }

    /**
     * Procesa una imagen opcional y devuelve la ruta relativa que se guardará
     * en la base de datos. Si no se selecciona archivo, conserva la ruta actual.
     */
    private function procesarImagen(?string $rutaActual): array
    {
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] === UPLOAD_ERR_NO_FILE) {
            return ['ruta' => $rutaActual, 'error' => null, 'es_nueva' => false];
        }

        if ($_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            return ['ruta' => $rutaActual, 'error' => 'imagen_error', 'es_nueva' => false];
        }

        if ((int)$_FILES['imagen']['size'] > 4 * 1024 * 1024) {
            return ['ruta' => $rutaActual, 'error' => 'imagen_grande', 'es_nueva' => false];
        }

        $archivoTemporal = $_FILES['imagen']['tmp_name'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($archivoTemporal);
        $extensionesPermitidas = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif'
        ];

        if (!isset($extensionesPermitidas[$mime])) {
            return ['ruta' => $rutaActual, 'error' => 'imagen_invalida', 'es_nueva' => false];
        }

        $directorioRelativo = 'assets/img/servicios/';
        $directorioAbsoluto = dirname(__DIR__, 2) . '/' . $directorioRelativo;

        if (!is_dir($directorioAbsoluto) && !mkdir($directorioAbsoluto, 0755, true)) {
            return ['ruta' => $rutaActual, 'error' => 'imagen_error', 'es_nueva' => false];
        }

        try {
            $nombreArchivo = 'servicio_' . bin2hex(random_bytes(8)) . '.' . $extensionesPermitidas[$mime];
        } catch (Exception $e) {
            $nombreArchivo = 'servicio_' . uniqid('', true) . '.' . $extensionesPermitidas[$mime];
        }

        $rutaAbsoluta = $directorioAbsoluto . $nombreArchivo;

        if (!move_uploaded_file($archivoTemporal, $rutaAbsoluta)) {
            return ['ruta' => $rutaActual, 'error' => 'imagen_error', 'es_nueva' => false];
        }

        return [
            'ruta' => $directorioRelativo . $nombreArchivo,
            'error' => null,
            'es_nueva' => true
        ];
    }

    private function eliminarArchivoImagen(?string $rutaRelativa): void
    {
        if ($rutaRelativa === null || !str_starts_with($rutaRelativa, 'assets/img/servicios/')) {
            return;
        }

        $raizProyecto = dirname(__DIR__, 2);
        $archivo = realpath($raizProyecto . '/' . ltrim($rutaRelativa, '/'));
        $directorioPermitido = realpath($raizProyecto . '/assets/img/servicios');

        if (
            $archivo !== false &&
            $directorioPermitido !== false &&
            str_starts_with($archivo, $directorioPermitido . DIRECTORY_SEPARATOR) &&
            is_file($archivo)
        ) {
            unlink($archivo);
        }
    }

    private function redirigir(?string $status = null, int $idServicio = 0): never
    {
        $url = 'index.php?controller=servicio&action=';
        $url .= $idServicio > 0 ? 'editar&id=' . $idServicio : 'listar';

        if ($status !== null) {
            $url .= '&status=' . urlencode($status);
        }

        header('Location: ' . $url);
        exit;
    }
}
