<?php
$tituloPagina = "Ventas";
$mostrarMenu = true;
$pageStyles = "assets/css/ventas.css";

require_once "views/layouts/header.php";
?>

<main class="content">

    <header class="ventas-header">
        <h1>Gestión de Ventas</h1>
        <p>Consulta las compras realizadas por los clientes.</p>
    </header>

    <section class="card ventas-buscador-card">

        <div class="ventas-buscador">

            <input
                type="text"
                id="buscarVenta"
                placeholder="Buscar por cliente, correo o número de venta..."
                autocomplete="off"
            >

            <button
                type="button"
                id="btnBuscarVenta"
                class="btn btn-primary ventas-btn-buscar"
            >
                <i class="fa-solid fa-magnifying-glass"></i>
                Buscar
            </button>

        </div>

    </section>

    <section class="card ventas-listado-card">

        <div class="ventas-listado-titulo">
            <i class="fa-solid fa-table-list"></i>
            <h3>Lista de Ventas Realizadas</h3>
        </div>

        <div class="ventas-tabla-contenedor">

            <table class="table ventas-tabla">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Correo</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaVentas">

                    <?php if (empty($ventas)): ?>

                        <tr>
                            <td colspan="7" class="ventas-vacio">
                                No existen ventas registradas.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($ventas as $venta): ?>

                            <?php
                            $estado = strtolower($venta["estado"]);

                            $claseEstado = match ($estado) {
                                "pagada" => "badge-success ventas-estado-pagada",
                                "pendiente" => "badge-warning ventas-estado-pendiente",
                                "cancelada" => "badge-danger ventas-estado-cancelada",
                                default => "badge-info"
                            };
                            ?>

                            <tr>

                                <td class="ventas-id">
                                    #<?= (int)$venta["id_venta"] ?>
                                </td>

                                <td class="ventas-cliente">
                                    <?= htmlspecialchars($venta["cliente"]) ?>
                                </td>

                                <td class="ventas-correo">
                                    <?= htmlspecialchars($venta["correo"]) ?>
                                </td>

                                <td class="ventas-fecha">
                                    <?= date(
                                        "d/m/Y H:i",
                                        strtotime($venta["fecha_venta"])
                                    ) ?>
                                </td>

                                <td class="ventas-total">
                                    $<?= number_format(
                                        (float)$venta["total"],
                                        2
                                    ) ?>
                                </td>

                                <td>
                                    <span class="badge ventas-estado <?= $claseEstado ?>">
                                        <?= htmlspecialchars($venta["estado"]) ?>
                                    </span>
                                </td>

                                <td>
                                    <div class="ventas-acciones">

                                        <a
                                            href="index.php?controller=venta&action=detalle&id=<?= (int)$venta["id_venta"] ?>"
                                            class="btn btn-primary"
                                            title="Ver detalle"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                        </a>

                                    </div>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

<?php
$pageScript = "assets/js/admin/ventas.js";
require_once "views/layouts/footer.php";
?>