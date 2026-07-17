<?php

    $tituloPagina = "Detalle de compra #" . $idCompra;
    require_once "views/layouts/header_publico.php";
?>
<main class="container section">
    <section class="section-header">
        <h2 class="section-title">
            <i class="fa-solid fa-receipt"></i>
            Compra #<?= $compra['id_compra'] ?>
        </h2>
        <p>
            Realizada el <?= htmlspecialchars($compra['fecha']) ?>
            &mdash;
            <span class="badge badge-<?= $compra['estado'] === 'Pagada' ? 'success' : ($compra['estado'] === 'Cancelada' ? 'danger' : 'warning') ?>">
                <?= htmlspecialchars($compra['estado']) ?>
            </span>
        </p>
    </section>

    <section class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($detalle as $d): ?>
                    <tr>
                        <td>
                            <img
                            src="<?= htmlspecialchars($d['imagen']) ?>"
                            width="60"
                            height="60"
                            style="object-fit:cover;border-radius:8px;">
                        </td>
                        <td><?= htmlspecialchars($d['nombre']) ?></td>
                        <td><?= $d['cantidad'] ?></td>
                        <td>$<?= number_format($d['precio_unitario'],2) ?></td>
                        <td>$<?= number_format($d['subtotal'],2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <section class="card">
        <h3>
            Total de la compra:
            <strong>$<?= number_format($compra['total'],2) ?></strong>
        </h3>
        <a href="index.php?controller=area-cliente&action=compras"
        class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Volver a mis compras
        </a>
    </section>
</main>
<?php require_once "views/layouts/footer.php"; ?>