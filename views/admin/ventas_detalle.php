<?php

$tituloPagina = "Detalle de Venta";
$mostrarMenu = true;
$pageStyles = "assets/css/ventas.css";

require_once "views/layouts/header.php";
?>

<main class="content">

    <header class="ventas-header">

        <div>
            <h1>Detalle de Venta</h1>
            <p>Información completa de la compra realizada.</p>
        </div>

        <a
            href="index.php?controller=venta&action=index"
            class="btn btn-secondary"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Volver
        </a>

    </header>

    <section class="card venta-detalle-resumen">

        <div class="ventas-listado-titulo">
            <i class="fa-solid fa-receipt"></i>
            <h3>Información de la venta</h3>
        </div>

        <div class="venta-detalle-grid">

            <div class="venta-detalle-item">
                <span>Número de venta</span>

                <strong>
                    #<?= (int)$venta["id_venta"] ?>
                </strong>
            </div>

            <div class="venta-detalle-item">
                <span>Cliente</span>

                <strong>
                    <?= htmlspecialchars($venta["cliente"]) ?>
                </strong>
            </div>

            <div class="venta-detalle-item">
                <span>Correo</span>

                <strong>
                    <?= htmlspecialchars($venta["correo"]) ?>
                </strong>
            </div>

            <div class="venta-detalle-item">
                <span>Celular</span>

                <strong>
                    <?= htmlspecialchars($venta["celular"] ?? "No registrado") ?>
                </strong>
            </div>

            <div class="venta-detalle-item">
                <span>Fecha</span>

                <strong>
                    <?= date(
                        "d/m/Y H:i",
                        strtotime($venta["fecha_venta"])
                    ) ?>
                </strong>
            </div>

            <div class="venta-detalle-item">
                <span>Estado</span>

                <?php
                $estado = strtolower($venta["estado"]);

                $claseEstado = match ($estado) {
                    "pagada" => "badge-success",
                    "pendiente" => "badge-warning",
                    "cancelada" => "badge-danger",
                    default => "badge-info"
                };
                ?>

                <strong>
                    <span class="badge <?= $claseEstado ?>">
                        <?= htmlspecialchars($venta["estado"]) ?>
                    </span>
                </strong>
            </div>

        </div>

    </section>

    <section class="card ventas-listado-card">

    <div class="ventas-listado-titulo">
        <i class="fa-solid fa-box-open"></i>
        <h3>Productos comprados</h3>
    </div>

    <div class="ventas-tabla-contenedor">

        <table class="table ventas-tabla detalle-tabla">

            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Imagen</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($detalles)): ?>

                    <tr>
                        <td colspan="5" class="ventas-vacio">
                            No hay productos registrados en esta venta.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($detalles as $detalle): ?>

                        <tr>

                            <td>
                                <div class="detalle-producto-info">
                                    <strong>
                                        <?= htmlspecialchars($detalle["producto"]) ?>
                                    </strong>

                                    <?php if (!empty($detalle["descripcion"])): ?>
                                        <span>
                                            <?= htmlspecialchars($detalle["descripcion"]) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td>
                                <div class="detalle-imagen-contenedor">

                                    <?php if (!empty($detalle["imagen"])): ?>

                                        <img
                                            src="<?= htmlspecialchars($detalle["imagen"]) ?>"
                                            alt="<?= htmlspecialchars($detalle["producto"]) ?>"
                                            class="venta-producto-imagen"
                                        >

                                    <?php else: ?>

                                        <div class="detalle-sin-imagen">
                                            <i class="fa-solid fa-image"></i>
                                        </div>

                                    <?php endif; ?>

                                </div>
                            </td>

                            <td class="detalle-cantidad">
                                <?= (int)$detalle["cantidad"] ?>
                            </td>

                            <td>
                                $<?= number_format(
                                    (float)$detalle["precio_unitario"],
                                    2
                                ) ?>
                            </td>

                            <td class="ventas-total">
                                $<?= number_format(
                                    (float)$detalle["subtotal"],
                                    2
                                ) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

    <div class="venta-detalle-total">
        <span>Total pagado</span>

        <strong>
            $<?= number_format((float)$venta["total"], 2) ?>
        </strong>
    </div>

</section>

</main>

<?php
require_once "views/layouts/footer.php";
?>