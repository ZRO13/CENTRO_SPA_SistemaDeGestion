<?php
$tituloPagina = "Editar Cliente";
$pageStyles = "assets/css/clientes.css";
require_once "views/layouts/header.php";

/** @var Cliente $cliente */
?>



<main class="content">

    <div class="page-header">
        <h1>Editar Cliente</h1>
        <a href="index.php?controller=cliente&action=listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card form-container">
        <form action="index.php?controller=cliente&action=editar&id=<?= $cliente->getIdCliente() ?>" method="POST">
            <div class="clientes-form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required
                        value="<?= htmlspecialchars($cliente->getNombre()) ?>">
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" required
                        value="<?= htmlspecialchars($cliente->getApellido()) ?>">
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                        value="<?= htmlspecialchars($cliente->getCorreo()) ?>">
                </div>

                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input type="text" id="celular" name="celular"
                        value="<?= htmlspecialchars($cliente->getCelular()) ?>">
                </div>

                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required
                        value="<?= htmlspecialchars($cliente->getUsername()) ?>">
                </div>
            </div>

            <div class="clientes-form-actions">
                <a href="index.php?controller=cliente&action=listar" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar cambios
                </button>
            </div>
        </form>
    </div>
</main>

</div>
</div>
<?php require_once "views/layouts/footer.php"; ?>
