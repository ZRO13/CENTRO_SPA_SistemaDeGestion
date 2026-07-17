<?php


$tituloPagina = "Servicios";
$pageStyles = "assets/css/servicios.css";
require_once "views/layouts/header.php";

/** @var Servicio|null $servicioEditar */
$editando = isset($servicioEditar) && $servicioEditar instanceof Servicio;
$valorId = $editando ? $servicioEditar->getIdServicio() : '';
$valorNombre = $editando ? $servicioEditar->getNombre() : '';
$valorDescripcion = $editando ? ($servicioEditar->getDescripcion() ?? '') : '';
$valorPrecio = $editando ? number_format($servicioEditar->getPrecio(), 2, '.', '') : '';
$valorCategoria = $editando ? $servicioEditar->getIdCategoriaServicio() : 0;
$valorDisponibilidad = $editando ? ($servicioEditar->getDisponibilidad() ? 1 : 0) : 1;
$valorImagen = $editando ? $servicioEditar->getImagen() : null;

// Asegurar que $categorias exista y sea iterable para evitar "undefined variable"
if (!isset($categorias) || !is_array($categorias)) {
    $categorias = [];
}
?>

<main class="content">
    <div class="page-header">
        <h1>Gestión de Servicios</h1>
        <button type="button" class="btn btn-primary" id="btnToggleFormulario">
            <i class="fas fa-spa"></i>
            <span id="btnText"><?= $editando ? 'Ocultar formulario' : 'Nuevo servicio' ?></span>
        </button>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php
        $mensajes = [
            'success' => ['tipo' => 'success', 'texto' => 'Servicio registrado correctamente.'],
            'updated' => ['tipo' => 'success', 'texto' => 'Servicio actualizado correctamente.'],
            'deactivated' => ['tipo' => 'success', 'texto' => 'Servicio desactivado correctamente.'],
            'reactivated' => ['tipo' => 'success', 'texto' => 'Servicio reactivado correctamente.'],
            'campos_vacios' => ['tipo' => 'danger', 'texto' => 'Completa el nombre, la categoría y el precio.'],
            'nombre_largo' => ['tipo' => 'danger', 'texto' => 'El nombre no puede superar los 100 caracteres.'],
            'precio_invalido' => ['tipo' => 'danger', 'texto' => 'Ingresa un precio mayor que cero.'],
            'categoria_invalida' => ['tipo' => 'danger', 'texto' => 'Selecciona una categoría válida y activa.'],
            'imagen_invalida' => ['tipo' => 'danger', 'texto' => 'La imagen debe ser JPG, PNG, WEBP o GIF.'],
            'imagen_grande' => ['tipo' => 'danger', 'texto' => 'La imagen no puede superar los 4 MB.'],
            'imagen_error' => ['tipo' => 'danger', 'texto' => 'No se pudo guardar la imagen seleccionada.'],
            'no_encontrado' => ['tipo' => 'danger', 'texto' => 'El servicio solicitado no existe.'],
            'error' => ['tipo' => 'danger', 'texto' => 'Ocurrió un error al procesar la solicitud.'],
        ];
        $estadoMensaje = $mensajes[$_GET['status']] ?? null;
        ?>

        <?php if ($estadoMensaje): ?>
            <div class="alert alert-<?= $estadoMensaje['tipo'] ?>">
                <?= htmlspecialchars($estadoMensaje['texto']) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <section
        id="seccionFormulario"
        class="card servicios-form-card <?= $editando ? '' : 'hidden' ?>"
        data-editando="<?= $editando ? '1' : '0' ?>"
    >
        <h3 class="card-title">
            <i class="fas <?= $editando ? 'fa-pen-to-square' : 'fa-circle-plus' ?>"></i>
            <?= $editando ? 'Editar servicio' : 'Registrar nuevo servicio' ?>
        </h3>

        <form
            action="index.php?controller=servicio&action=guardar"
            method="POST"
            enctype="multipart/form-data"
            id="formServicio"
        >
            <input type="hidden" name="id_servicio" value="<?= htmlspecialchars((string)$valorId) ?>">

            <div class="servicios-form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre del servicio</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        maxlength="100"
                        placeholder="Ej. Limpieza facial profunda"
                        value="<?= htmlspecialchars($valorNombre) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="id_categoria_servicio">Categoría</label>
                    <select id="id_categoria_servicio" name="id_categoria_servicio" required>
                        <option value="">Seleccione una categoría</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option
                                value="<?= $categoria->getIdCategoriaServicio() ?>"
                                <?= $valorCategoria === $categoria->getIdCategoriaServicio() ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($categoria->getNombreCategoria()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="precio">Precio</label>
                    <input
                        type="number"
                        id="precio"
                        name="precio"
                        min="0.01"
                        step="0.01"
                        placeholder="Ej. 25.35"
                        value="<?= htmlspecialchars($valorPrecio) ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="disponibilidad">Disponibilidad</label>
                    <select id="disponibilidad" name="disponibilidad" required>
                        <option value="1" <?= $valorDisponibilidad === 1 ? 'selected' : '' ?>>Disponible</option>
                        <option value="0" <?= $valorDisponibilidad === 0 ? 'selected' : '' ?>>No disponible</option>
                    </select>
                </div>

                <div class="form-group servicios-descripcion">
                    <label for="descripcion">Descripción</label>
                    <textarea
                        id="descripcion"
                        name="descripcion"
                        placeholder="Describe brevemente el tratamiento o servicio"
                    ><?= htmlspecialchars($valorDescripcion) ?></textarea>
                </div>

                <div class="form-group servicios-imagen-campo">
                    <label for="imagen">Imagen del servicio</label>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp,image/gif">
                    <small>Formatos permitidos: JPG, PNG, WEBP o GIF. Máximo 4 MB.</small>
                </div>

                <div class="servicios-preview-contenedor">
                    <?php if ($valorImagen): ?>
                        <img
                            id="previewImagen"
                            class="servicios-preview"
                            src="<?= htmlspecialchars($valorImagen) ?>"
                            alt="Vista previa del servicio"
                        >
                    <?php else: ?>
                        <img id="previewImagen" class="servicios-preview hidden" alt="Vista previa del servicio">
                        <div id="sinPreview" class="servicios-sin-preview">
                            <i class="fas fa-image"></i>
                            <span>Sin imagen seleccionada</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="servicios-form-actions">
                <?php if ($editando): ?>
                    <a href="index.php?controller=servicio&action=listar" class="btn btn-secondary">Cancelar</a>
                <?php else: ?>
                    <button type="button" class="btn btn-secondary" id="btnCancelarFormulario">Cancelar</button>
                <?php endif; ?>

                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    <?= $editando ? 'Guardar cambios' : 'Guardar servicio' ?>
                </button>
            </div>
        </form>
    </section>

    <section class="card servicios-filtros-card">
        <form action="index.php" method="GET" class="servicios-filtros">
            <input type="hidden" name="controller" value="servicio">
            <input type="hidden" name="action" value="listar">

            <input
                type="text"
                name="termino"
                placeholder="Buscar por nombre, descripción o categoría..."
                value="<?= htmlspecialchars($_GET['termino'] ?? '') ?>"
            >

            <select name="categoria">
                <option value="0">Todas las categorías</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option
                        value="<?= $categoria->getIdCategoriaServicio() ?>"
                        <?= (int)($_GET['categoria'] ?? 0) === $categoria->getIdCategoriaServicio() ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($categoria->getNombreCategoria()) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="disponibilidad">
                <option value="">Todos los estados</option>
                <option value="1" <?= ($_GET['disponibilidad'] ?? '') === '1' ? 'selected' : '' ?>>Disponibles</option>
                <option value="0" <?= ($_GET['disponibilidad'] ?? '') === '0' ? 'selected' : '' ?>>No disponibles</option>
            </select>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>

            <?php if (!empty($_GET['termino']) || !empty($_GET['categoria']) || isset($_GET['disponibilidad']) && $_GET['disponibilidad'] !== ''): ?>
                <a href="index.php?controller=servicio&action=listar" class="btn btn-secondary">Limpiar</a>
            <?php endif; ?>
        </form>
    </section>

    <section class="card">
        <h3 class="card-title"><i class="fas fa-table"></i> Servicios registrados</h3>

        <div class="table-container">
            <table class="table servicios-tabla">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($servicios)): ?>
                        <?php foreach ($servicios as $servicio): ?>
                            <tr>
                                <td>
                                    <?php if ($servicio->getImagen()): ?>
                                        <img
                                            class="servicios-tabla-imagen"
                                            src="<?= htmlspecialchars($servicio->getImagen()) ?>"
                                            alt="<?= htmlspecialchars($servicio->getNombre()) ?>"
                                        >
                                    <?php else: ?>
                                        <div class="servicios-tabla-sin-imagen" title="Sin imagen">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= htmlspecialchars($servicio->getNombre()) ?></strong></td>
                                <td><?= htmlspecialchars($servicio->getNombreCategoria()) ?></td>
                                <td class="servicios-descripcion-tabla">
                                    <?= htmlspecialchars($servicio->getDescripcion() ?? 'Sin descripción') ?>
                                </td>
                                <td>$<?= number_format($servicio->getPrecio(), 2) ?></td>
                                <td>
                                    <?php if ($servicio->getDisponibilidad()): ?>
                                        <span class="badge badge-success">Disponible</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">No disponible</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="servicios-acciones">
                                        <a
                                            href="index.php?controller=servicio&action=editar&id=<?= $servicio->getIdServicio() ?>"
                                            class="btn btn-warning-soft"
                                            title="Editar servicio"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <?php if ($servicio->getDisponibilidad()): ?>
                                            <a
                                                href="index.php?controller=servicio&action=eliminar&id=<?= $servicio->getIdServicio() ?>"
                                                class="btn btn-danger"
                                                title="Desactivar servicio"
                                                onclick="return confirm('¿Deseas desactivar este servicio?');"
                                            >
                                                <i class="fas fa-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <a
                                                href="index.php?controller=servicio&action=reactivar&id=<?= $servicio->getIdServicio() ?>"
                                                class="btn btn-success"
                                                title="Reactivar servicio"
                                                onclick="return confirm('¿Deseas reactivar este servicio?');"
                                            >
                                                <i class="fas fa-rotate-left"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron servicios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

</div>
</div>
<?php
$pageScript = "assets/js/admin/servicios.js?v=" . filemtime("assets/js/admin/servicios.js");
require_once "views/layouts/footer.php";
?>
