<?php

// VIEW - "Mi Cuenta": perfil del cliente autenticado con accesos rápidos
// a citas, compras y catálogo.
$tituloPagina = "Mi Cuenta";
$pageStyles = "assets/css/perfil.css";
require_once "views/layouts/header_publico.php";

if (!isset($usuario) || !is_array($usuario)) {
    $usuario = [
        'nombre' => '',
        'apellido' => '',
        'username' => '',
        'nombre_rol' => ''
    ];
}
?>

<section class="section container">

    <!-- Cabecera del perfil -->
    <div class="perfil-header">
        <div class="perfil-avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <div class="perfil-header-info">
            <h2>Hola, <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?> 👋</h2>
            <p>Nos alegra verte de nuevo. ¿Qué te gustaría hacer hoy?</p>
            <span class="perfil-rol">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($usuario['nombre_rol']) ?>
            </span>
        </div>
    </div>

    <!-- Accesos rápidos -->
    <div class="grid grid-3">
        <article class="card perfil-card">
            <div class="perfil-card-icono">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <h3>Mis citas</h3>
            <p>Consulta tu agenda o reserva una nueva cita.</p>
            <div class="cliente-inicio-acciones">
                <a href="index.php?controller=citas&action=miAgenda" class="btn btn-secondary">Ver mi agenda</a>
                <a href="index.php?controller=citas&action=crear" class="btn btn-primary">Reservar cita</a>
            </div>
        </article>

        <article class="card perfil-card">
            <div class="perfil-card-icono">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <h3>Mis compras</h3>
            <p>Revisa tu historial de compras o explora el catálogo.</p>
            <div class="cliente-inicio-acciones">
                <a href="index.php?controller=area-cliente&action=compras" class="btn btn-secondary">Ver mis compras</a>
                <a href="index.php?controller=clienteProd&action=catalogo" class="btn btn-primary">Ir al catálogo</a>
            </div>
        </article>

        <article class="card perfil-card">
            <div class="perfil-card-icono">
                <i class="fa-solid fa-id-card"></i>
            </div>
            <h3>Mi perfil</h3>
            <p>Estos son los datos de tu cuenta.</p>
            <div>
                <div class="perfil-dato">
                    <span><i class="fa-solid fa-user"></i> Nombre</span>
                    <strong><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></strong>
                </div>
                <div class="perfil-dato">
                    <span><i class="fa-solid fa-at"></i> Usuario</span>
                    <strong><?= htmlspecialchars($usuario['username']) ?></strong>
                </div>
                <div class="perfil-dato">
                    <span><i class="fa-solid fa-shield-halved"></i> Rol</span>
                    <strong><?= htmlspecialchars($usuario['nombre_rol']) ?></strong>
                </div>
            </div>
        </article>
    </div>
</section>

<?php require_once "views/layouts/footer.php"; ?>
