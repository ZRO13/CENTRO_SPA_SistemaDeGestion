<?php
$tituloPagina = "Iniciar sesión";
$pageStyles = "assets/css/login.css";
require_once "views/layouts/header_publico.php";
?>



<main class="auth-page">
    <div class="card auth-card">
        <h1 class="auth-card-title">Iniciar sesión</h1>
        <p class="auth-card-subtitle">Ingresa con tu usuario o correo electrónico.</p>

        <?php if (isset($_GET['status'])): ?>
            <?php
            $mensajes = [
                'campos_vacios'          => ['tipo' => 'danger',  'texto' => 'Completa tu usuario/correo y contraseña.'],
                'credenciales_invalidas' => ['tipo' => 'danger',  'texto' => 'Usuario/correo o contraseña incorrectos.'],
                'cuenta_inactiva'        => ['tipo' => 'danger',  'texto' => 'Tu cuenta se encuentra inactiva. Contacta al administrador.'],
                'sesion_cerrada'         => ['tipo' => 'info',    'texto' => 'Sesión cerrada correctamente.'],
                'registro_exitoso'       => ['tipo' => 'success', 'texto' => '¡Cuenta creada! Ahora puedes iniciar sesión.'],
            ];
            $estado = $mensajes[$_GET['status']] ?? null;
            ?>
            <?php if ($estado): ?>
                <div class="alert alert-<?= $estado['tipo'] ?>"><?= htmlspecialchars($estado['texto']) ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <form action="index.php?controller=auth&action=login" method="POST" novalidate>
            <div class="form-group">
                <label for="credencial">Usuario o correo electrónico</label>
                <input type="text" id="credencial" name="credencial" placeholder="usuario o correo@ejemplo.com" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Tu contraseña" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fa-solid fa-right-to-bracket"></i> Ingresar
            </button>
        </form>

        <p class="auth-footer-link">
            ¿No tienes una cuenta? <a href="index.php?controller=auth&action=registro">Regístrate aquí</a>
        </p>
    </div>
</main>

<?php require_once "views/layouts/footer.php"; ?>
