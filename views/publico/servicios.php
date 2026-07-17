<?php

$tituloPagina = "Servicios";
$pageStyles = "assets/css/servicios-publico.css";
require_once "views/layouts/header_publico.php";
?>

<main>
    <section class="services-hero">
        <div class="container services-hero-content">
            <h1>Servicios de Delux Spa</h1>
            <p>Explora tratamientos diseñados para tu cuidado, relajación y bienestar.</p>
            <a href="#catalogo-servicios" class="btn btn-primary">Explorar servicios</a>
        </div>
    </section>

    <section class="services-catalog" id="catalogo-servicios">
        <div class="container">
            <div class="services-catalog-header">
                <h2>Catálogo de Servicios</h2>
                <p>Busca por nombre o selecciona una categoría.</p>
            </div>

            <form action="index.php" method="GET" class="services-filters">
                <input type="hidden" name="controller" value="sitio">
                <input type="hidden" name="action" value="servicios">

                <input
                    type="search"
                    name="termino"
                    placeholder="Buscar servicios..."
                    value="<?= htmlspecialchars($termino) ?>"
                >

                <select name="categoria">
                    <option value="0">Todas las categorías</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option
                            value="<?= $categoria->getIdCategoriaServicio() ?>"
                            <?= $idCategoria === $categoria->getIdCategoriaServicio() ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($categoria->getNombreCategoria()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i> Buscar
                </button>

                <?php if ($termino !== '' || $idCategoria > 0): ?>
                    <a href="index.php?controller=sitio&action=servicios" class="btn btn-secondary">Limpiar</a>
                <?php endif; ?>
            </form>

            <p class="services-results">
                <?= count($servicios) ?> servicio<?= count($servicios) === 1 ? '' : 's' ?> disponible<?= count($servicios) === 1 ? '' : 's' ?>
            </p>

            <div class="services-grid">
                <?php if (!empty($servicios)): ?>
                    <?php foreach ($servicios as $servicio): ?>
                        <article class="service-card">
                            <?php if ($servicio->getImagen()): ?>
                                <img
                                    src="<?= htmlspecialchars($servicio->getImagen()) ?>"
                                    alt="<?= htmlspecialchars($servicio->getNombre()) ?>"
                                >
                            <?php else: ?>
                                <div class="service-placeholder">
                                    <i class="fa-solid fa-spa"></i>
                                </div>
                            <?php endif; ?>

                            <div class="service-info">
                                <span class="service-category"><?= htmlspecialchars($servicio->getNombreCategoria()) ?></span>
                                <h3><?= htmlspecialchars($servicio->getNombre()) ?></h3>
                                <p><?= htmlspecialchars($servicio->getDescripcion() ?? 'Tratamiento profesional para tu cuidado y bienestar.') ?></p>
                                <div class="service-footer">
                                    <strong>$<?= number_format($servicio->getPrecio(), 2) ?></strong>
                                    <span class="service-status">Disponible</span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="services-empty">
                        <i class="fa-solid fa-spa"></i>
                        <h3>No se encontraron servicios</h3>
                        <p>Prueba con otro nombre o categoría.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>Encuentra tu momento de bienestar</h2>
        <p>Consulta nuestros servicios y crea tu cuenta para acceder a las funciones disponibles para clientes.</p>
        <?php if (!isset($_SESSION['usuario'])): ?>
            <a href="index.php?controller=auth&action=registro" class="btn btn-outline">Crear mi cuenta</a>
        <?php else: ?>
            <a href="index.php?controller=area-cliente&action=inicio" class="btn btn-outline">Ir a mi cuenta</a>
        <?php endif; ?>
    </section>
</main>

<?php require_once "views/layouts/footer.php"; ?>
