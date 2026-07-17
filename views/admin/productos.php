<?php


$pageStyles="assets/css/productos.css";
$pageScript = "assets/js/productos.js";
$mostrarMenu = true;
require_once "views/layouts/header.php";
?>
<main class="content">
    <section class="page-header">
        <h2>
            <i class="fa-solid fa-box-open"></i>
            Administración de Productos
        </h2>
        <p>
            Administre el catálogo de productos disponibles para el Spa.
        </p>
    </section>
    <section class="card">
    <h3>
        <i class="fa-solid fa-plus"></i>
        <?= isset($productoEditar) ? "Editar Producto" : "Nuevo Producto"; ?>
    </h3>
    <form
        action="index.php?controller=producto&action=<?= isset($productoEditar) ? "actualizar" : "guardar"; ?>"
        method="POST">
        <div class="form-grid">
        <input
            type="hidden"
            name="id"
            value="<?= $productoEditar["id_producto"] ?? "" ?>">
        <div class="form-group">
            <label>Categoría</label>
            <select name="categoria" required>
                <option value="">Seleccione...</option>
                <?php foreach($categorias as $c): ?>
                    <option
                        value="<?= $c["id_categoria_producto"] ?>"
                        <?= isset($productoEditar)
                        && $productoEditar["id_categoria_producto"] == $c["id_categoria_producto"]
                        ? "selected" : "" ?>>
                        <?= htmlspecialchars($c["nombre_categoria"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Nombre</label>
            <input
                type="text"
                name="nombre"
                required
                maxlength="100"
                value="<?= $productoEditar["nombre"] ?? "" ?>">
        </div>
        <div class="form-group full">
            <label>Descripción</label>
            <textarea
                name="descripcion"
                rows="3"><?= $productoEditar["descripcion"] ?? "" ?></textarea>
        </div>
        <div class="form-group">
            <label>Precio</label>
            <input
                type="number"
                name="precio"
                step="0.01"
                min="0"
                required
                value="<?= $productoEditar["precio"] ?? "" ?>">
        </div>
        <div class="form-group">
            <label>Stock</label>
            <input
                type="number"
                name="stock"
                min="0"
                required
                value="<?= $productoEditar["stock"] ?? "" ?>">
        </div>
        <div class="form-group">
            <label>Imagen (URL)</label>
            <input
                type="url"
                id="imagen"
                name="imagen"
                value="<?= htmlspecialchars($productoEditar["imagen"] ?? "") ?>">
        </div>
        <div class="form-group">
            <img
                id="preview"
                src="<?= htmlspecialchars($productoEditar["imagen"] ?? "") ?>"
                style="
                width:170px;
                height:170px;
                object-fit:cover;
                border-radius:12px;
                border:1px solid #ddd;">
        </div>
        <div class="form-group">
            <label>Disponibilidad</label>
            <select name="disponibilidad">
                <option value="1"
                    <?= (!isset($productoEditar) || ($productoEditar["disponibilidad"]==1)) ? "selected" : "" ?>>
                    Disponible
                </option>
                <option value="0"
                    <?= (isset($productoEditar) && $productoEditar["disponibilidad"]==0) ? "selected" : "" ?>>
                    No disponible
                </option>
            </select>
        </div>
        </div>
        <div class="botones">
            <button class="btn btn-primary" type="submit">
                <i class="fa-solid fa-floppy-disk"></i>
                <?= isset($productoEditar) ? "Actualizar" : "Guardar" ?>
            </button>
            <?php if(isset($productoEditar)): ?>
                <a
                    href="index.php?controller=producto&action=listar"
                    class="btn btn-secondary">
                    Cancelar
                </a>
            <?php endif; ?>
        </div>
    </form>
    </section>

    <section class="card">
    <h3>
        <i class="fa-solid fa-table"></i>
        Productos Registrados
    </h3>

    <div class="search-container">
        <input
            type="text"
            id="buscarProducto"
            class="form-control"
            placeholder="Buscar producto por nombre...">
    </div>

    <br>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Imagen</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="tablaProductos">
        <?php if(count($productos)>0): ?>
            <?php foreach($productos as $p): ?>
                <tr>
                    <td><?= (int)$p["id_producto"] ?></td>
                    <td>
                        <?php if(!empty($p["imagen"])): ?>
                            <img
                                src="<?= $p["imagen"] ?>"
                                width="70"
                                height="70"
                                style="object-fit:cover;border-radius:8px;">
                        <?php else: ?>
                            Sin imagen
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($p["nombre"]) ?></td>
                    <td><?= htmlspecialchars($p["nombre_categoria"]) ?></td>
                    <td>$<?= number_format($p["precio"],2) ?></td>
                    <td><?= (int)$p["stock"] ?></td>
                    <td>
                        <?php if($p["disponibilidad"]): ?>
                            <span class="badge bg-success">
                                Disponible
                            </span>
                        <?php else: ?>
                            <span class="badge bg-danger">
                                No disponible
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a
                            class="btn btn-warning"
                            href="index.php?controller=producto&action=editar&id=<?= $p["id_producto"] ?>">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <a
                            class="btn btn-danger btnEliminar"
                            data-id="<?= $p["id_producto"] ?>"
                        >
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align:center">

                    No existen productos registrados.
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>
    <?php if(isset($_SESSION["success"])): ?>
    <script>
    Swal.fire({
        icon:'success',
        title:'Éxito',
        text:'<?= htmlspecialchars($_SESSION["success"]) ?>',
        confirmButtonColor:'#8b5e83'
    });
    </script>
    <?php unset($_SESSION["success"]); ?>
    <?php endif; ?>
    <?php if(isset($_SESSION["error"])): ?>
        
    <script>
    Swal.fire({
        icon:'error',
        title:'No se puede eliminar',
        text:'<?= htmlspecialchars($_SESSION["error"]) ?>',
        confirmButtonColor:'#8b5e83'
    });
    </script>
    <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>
</main>
<?php
require_once "views/layouts/footer.php";
?>