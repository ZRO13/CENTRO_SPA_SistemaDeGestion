<?php
$tituloPagina = "Editar Usuario";
$pageStyles = "assets/css/usuarios.css";
require_once "views/layouts/header.php";

/** @var Usuario $usuario */
/** @var Rol $roles */
?>




<main class="content">

    <div class="page-header">
        <h1>Editar Usuario</h1>
        <a href="index.php?controller=usuario&action=listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card form-container">
        <form action="index.php?controller=usuario&action=editar&id=<?= $usuario->getIdUsuario() ?>" method="POST">

            <div class="usuarios-form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" required
                        value="<?= htmlspecialchars($usuario->getNombre()) ?>">
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" required
                        value="<?= htmlspecialchars($usuario->getApellido()) ?>">
                </div>
            </div>

            <div class="usuarios-form-grid">
                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                        value="<?= htmlspecialchars($usuario->getCorreo()) ?>">
                </div>

                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input type="text" id="celular" name="celular"
                        value="<?= htmlspecialchars($usuario->getCelular()) ?>">
                </div>
            </div>

            <div class="usuarios-form-grid">
                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" required
                        value="<?= htmlspecialchars($usuario->getUsername()) ?>">
                </div>

                <div class="form-group">
                    <label for="id_rol">Rol</label>
                    <select id="id_rol" name="id_rol" required>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol->getIdRol() ?>" <?= $rol->getIdRol() === $usuario->getIdRol() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($rol->getNombre()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Nueva contraseña (opcional)</label>
                <input type="password" id="password" name="password" placeholder="Dejar en blanco para no cambiarla">
            </div>

            <div class="form-group form-check">
                <label>
                    <input type="checkbox" name="estado" value="1" <?= $usuario->getEstado() ? 'checked' : '' ?>>
                    Cuenta activa (permite iniciar sesión)
                </label>
            </div>

            <div class="usuarios-form-actions">
                <a href="index.php?controller=usuario&action=listar" class="btn btn-secondary">Cancelar</a>
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
