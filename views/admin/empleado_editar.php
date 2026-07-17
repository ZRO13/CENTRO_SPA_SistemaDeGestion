<?php
$tituloPagina = "Editar Empleado";
$pageStyles = "assets/css/empleados.css";
require_once "views/layouts/header.php";
?>



<main class="content">

    <div class="page-header">
        <h1>Editar Empleado</h1>
        <a href="index.php?controller=empleado&action=listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card form-container">
        <form action="index.php?controller=empleado&action=editar&id=<?= $empleado->getIdEmpleado() ?>" method="POST">
            <div class="empleados-form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required
                        value="<?= htmlspecialchars($empleado->getNombre()) ?>">
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" required
                        value="<?= htmlspecialchars($empleado->getApellido()) ?>">
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                        value="<?= htmlspecialchars($empleado->getCorreo()) ?>">
                </div>

                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input type="text" id="celular" name="celular"
                        value="<?= htmlspecialchars($empleado->getCelular()) ?>">
                </div>

                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required
                        value="<?= htmlspecialchars($empleado->getUsername()) ?>">
                </div>

                <div class="form-group">
                    <label for="cargo">Cargo</label>
                    <input type="text" id="cargo" name="cargo" required
                        value="<?= htmlspecialchars($empleado->getCargo()) ?>">
                </div>

                <div class="form-group">
                    <label for="fecha_ingreso">Fecha de ingreso</label>
                    <input type="date" id="fecha_ingreso" name="fecha_ingreso" required
                        value="<?= htmlspecialchars($empleado->getFechaIngreso()) ?>">
                </div>
            </div>

            <div class="empleados-form-actions">
                <a href="index.php?controller=empleado&action=listar" class="btn btn-secondary">Cancelar</a>
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
