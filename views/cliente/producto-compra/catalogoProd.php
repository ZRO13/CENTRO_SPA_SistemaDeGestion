

<?php
$pageStyles = "assets/css/catalogo.css";
require_once "views/layouts/header_publico.php";
?>
<main>
    <!--HERO-->
    <section class="hero">
        <div class="hero-content">
            <h1>
                Delux Spa
            </h1>
            <p>
                Encuentra productos profesionales para el cuidado
                de tu piel, cabello y bienestar.
            </p>
            <a 
            href="#catalogo"
            class="btn btn-primary">
                Explorar productos
            </a>
        </div>
    </section>
    <!-- BOTON FLOTANTE CARRITO-->
    <button
    id="btnCarrito"
    class="carrito-flotante"
    type="button">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>
            Carrito
        </span>
        <span 
        id="contadorCarrito"
        class="contador">
            0
        </span>
    </button>
    <!--TITULO Y BUSQUEDA-->
    <section 
    class="catalogo-header"
    id="catalogo">
        <h2>
            Nuestro Catálogo
        </h2>
        <input
        type="text"
        id="buscarProducto"
        placeholder="Buscar productos...">
    </section>
    <!--FILTROS-->
    <section class="filtros">
        <button
        class="btnCategoria activo"
        data-id="0">
            Todos
        </button>
        <?php foreach($categorias as $c): ?>
            <button
            class="btnCategoria"
            data-id="<?= $c["id_categoria_producto"] ?>">
                <?= htmlspecialchars($c["nombre_categoria"]) ?>
            </button>
        <?php endforeach; ?>
    </section>

    <!-- PRODUCTOS-->
    <section
    class="catalogo-grid"
    id="catalogoGrid">
    <?php foreach($productos as $p): ?>
        <article class="producto-card">
            <img
            src="<?= !empty($p["imagen"])
                ? htmlspecialchars($p["imagen"])
                : "assets/img/no-image.png" ?>"
            alt="<?= htmlspecialchars($p["nombre"]) ?>">
            <div class="producto-info">
                <span class="categoria">
                    <?= htmlspecialchars($p["nombre_categoria"]) ?>
                </span>
                <h3>
                    <?= htmlspecialchars($p["nombre"]) ?>
                </h3>
                <p>

                    <?= htmlspecialchars($p["descripcion"]) ?>
                </p>
                <div class="precio">
                    $<?= number_format($p["precio"],2) ?>
                </div>
                <div class="acciones-producto">
                    <a
                    class="btn btn-secondary"
                    href="index.php?controller=clienteProd&action=detalle&id=<?= $p["id_producto"] ?>">
                        <i class="fa-solid fa-eye"></i>
                        Detalles
                    </a>
                    <button
                    class="btn btn-primary btnAgregarCarrito"
                    data-id="<?= $p["id_producto"] ?>"
                    type="button">
                        <i class="fa-solid fa-cart-plus"></i>
                        Agregar
                    </button>
                </div>
            </div>
        </article>
    <?php endforeach; ?>
    </section>

    <!-- CARRITO LATERAL-->
    <aside id="carritoPanel" class="carrito-panel">
        <div class="carrito-header">
            <h3>
                <i class="fa-solid fa-cart-shopping"></i>
                Mi carrito
            </h3>
            <button id="cerrarCarrito">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div id="contenidoCarrito">
            <p>Tu carrito está vacío.</p>
        </div>
        <div class="carrito-footer">
            <h4>
                Total:
                <span id="totalCarrito">$0.00</span>
            </h4>
            <button id="btnFinalizarCompra" class="btn btn-primary">
                <i class="fa-solid fa-credit-card"></i>
                Finalizar compra
            </button>
        </div>
    </aside>
    <!--  MODAL CONFIRMACIÓN DE COMPRA-->
    <div id="modalCompraExitosa" class="modal-overlay">
        <div class="modal-compra">
            <div class="modal-icono">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h3>¡Compra realizada!</h3>
            <p>
                Tu pedido fue procesado correctamente.
                Pronto recibirás la confirmación.
            </p>
            <button id="btnCerrarModalCompra" class="btn btn-primary">
                Ir a mi cuenta
            </button>
        </div>
    </div>
</main>
<script src="assets/js/catalogo.js"></script>
<?php
require_once "views/layouts/footer.php";
?>