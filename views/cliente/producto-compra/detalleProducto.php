<?php
$pageStyles = "assets/css/detalleProducto.css";

require_once "views/layouts/header_publico.php";
?>
<main class="detalle-container">
    <div class="detalle-card">
        <div class="detalle-imagen">
            <img src="<?= htmlspecialchars($producto["imagen"]) ?>" 
                alt="<?= htmlspecialchars($producto["nombre"]) ?>">
        </div>
        <div class="detalle-info">
            <span class="categoria">
                <?= htmlspecialchars($producto["nombre_categoria"]) ?>
            </span>
            <h1><?= htmlspecialchars($producto["nombre"]) ?></h1>
            <p class="descripcion">
                <?= htmlspecialchars($producto["descripcion"]) ?>
            </p>
            <h2 class="precio">
                $<?= number_format($producto["precio"],2) ?>
            </h2>
            <p>
                <strong>Disponibles:</strong>
                <?= (int)$producto["stock"] ?>
            </p>
            <div class="acciones">
                <a
                    class="btn btn-secondary"
                    href="index.php?controller=clienteProd&action=index">
                    Volver
                </a>
            </div>
        </div>
    </div>
</main>
<?php require_once "views/layouts/footer.php"; ?>