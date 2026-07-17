<?php
$tituloPagina = "Crear cuenta";
$pageStyles = "assets/css/login.css";
require_once "views/layouts/header_publico.php";
?>




<main class="auth-page">
    <div class="card auth-card">
        <h1 class="auth-card-title">Crear cuenta</h1>
        <p class="auth-card-subtitle">Regístrate para agendar citas y comprar productos.</p>

        <?php if (isset($_GET['status'])): ?>
            <?php
            $mensajes = [
                'campos_vacios'         => 'Completa todos los campos obligatorios.',
                'correo_invalido'       => 'Ingresa un correo electrónico válido.',
                'password_corta'        => 'La contraseña debe tener al menos 6 caracteres.',
                'password_no_coincide'  => 'Las contraseñas no coinciden.',
                'correo_existente'      => 'Ese correo ya está registrado.',
                'username_existente'    => 'Ese nombre de usuario ya está en uso.',
                'error'                 => 'Ocurrió un error al crear tu cuenta. Inténtalo nuevamente.',
            ];
            $texto = $mensajes[$_GET['status']] ?? null;
            ?>
            <?php if ($texto): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($texto) ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=registro" method="POST" id="formRegistro" novalidate>
            <div class="auth-grid-2">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej. Ana" required
                        value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Ej. Torres" required
                        value="<?= htmlspecialchars($_POST['apellido'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required
                    value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
            </div>

            <div class="auth-grid-2">
                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input type="text" id="celular" name="celular" placeholder="Ej. 0999999999" required
                        value="<?= htmlspecialchars($_POST['celular'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" placeholder="Nombre de usuario" required
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
            </div>

            <div class="auth-grid-2">
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
                </div>

                <div class="form-group">
                    <label for="confirmar_password">Confirmar contraseña</label>
                    <input type="password" id="confirmar_password" name="confirmar_password" placeholder="Repite tu contraseña" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fa-solid fa-user-plus"></i> Crear mi cuenta
            </button>
        </form>

        <p class="auth-footer-link">
            ¿Ya tienes una cuenta? <a href="index.php?controller=auth&action=login">Inicia sesión</a>
        </p>
    </div>
</main>

<?php
$pageScript = "assets/js/auth/registro.js";
require_once "views/layouts/footer.php";
?>
