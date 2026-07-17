<?php

    $tituloPagina = "Mis compras";
    require_once "views/layouts/header_publico.php";
?>
<main class="container section">
    <h2 class="section-title">
        <i class="fa-solid fa-bag-shopping"></i>
            Mis compras
    </h2>
    <?php if(empty($compras)): ?>
    <div class="card">
        <h3>No tienes compras realizadas todavía.</h3>
        <a href="index.php?controller=clienteProd&action=catalogo"
        class="btn btn-primary">Ir al catálogo</a>
    </div>
    <?php else: ?>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($compras as $c): ?>
                    <tr>
                        <td>#<?= $c['id_compra'] ?></td>
                        <td><?= htmlspecialchars($c['fecha']) ?></td>
                        <td>$<?= number_format($c['total'],2) ?></td>
                        <td>
                            <span class="badge badge-<?= $c['estado'] === 'Pagada' ? 'success' : ($c['estado'] === 'Cancelada' ? 'danger' : 'warning') ?>">
                                <?= htmlspecialchars($c['estado']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="index.php?controller=area-cliente&action=detalleCompra&id=<?= $c['id_compra'] ?>"
                            class="btn btn-secondary">
                                <i class="fa-solid fa-eye"></i>
                                Ver
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</main>
<?php require_once "views/layouts/footer.php"; ?>