<?php





// VIEW - Home público de Delux Spa: hero, presentación, servicios
// destacados (con acceso al flujo de citas), productos y CTA.
$tituloPagina = "Inicio";
$pageStyles = "assets/css/home.css";
require_once "views/layouts/header_publico.php";

$sesionActiva = isset($_SESSION['usuario']);

// "Agendar cita": clientes van al flujo de citas; visitantes, al login.
$urlAgendarCita = $sesionActiva
    ? "index.php?controller=citas&action=crear"
    : "index.php?controller=auth&action=login";
?>

<!-- HERO -->
<section class="home-hero">
    <div class="hero-content">
        <span class="hero-etiqueta">Spa · Belleza · Bienestar</span>
        <h1>Delux Spa</h1>
        <p>Bienestar y belleza en un solo lugar. Agenda tus tratamientos favoritos y descubre productos pensados para cuidar de ti.</p>
        <div class="hero-acciones">
            <a href="<?= $urlAgendarCita ?>" class="btn btn-primary">
                <i class="fa-solid fa-calendar-check"></i> Agendar cita
            </a>
            <a href="index.php?controller=sitio&action=servicios" class="btn btn-outline">
                Ver servicios
            </a>
        </div>
    </div>
</section>

<!-- PRESENTACIÓN -->
<section class="section home-about">
    <div class="home-about-inner">
        <h2 class="section-title">Bienvenido a Delux Spa</h2>
        <p>
            En Delux Spa combinamos técnicas profesionales de belleza y relajación
            para ofrecerte una experiencia integral de cuidado personal. Nuestro
            equipo te acompaña con servicios de spa, tratamientos especializados
            y una selección de productos de calidad, en un ambiente pensado para
            tu descanso y bienestar.
        </p>

        <div class="home-features">
            <div class="home-feature">
                <i class="fa-solid fa-calendar-check"></i>
                <h4>Citas en línea</h4>
                <p>Reserva tus tratamientos desde tu cuenta en segundos.</p>
            </div>
            <div class="home-feature">
                <i class="fa-solid fa-user-tie"></i>
                <h4>Equipo profesional</h4>
                <p>Especialistas dedicados a tu cuidado y bienestar.</p>
            </div>
            <div class="home-feature">
                <i class="fa-solid fa-bag-shopping"></i>
                <h4>Compra en línea</h4>
                <p>Productos seleccionados y tu historial de compras siempre a mano.</p>
            </div>
        </div>
    </div>
</section>

<!-- SERVICIOS -->
<section class="section container" id="servicios">
    <h2 class="section-title">Nuestros servicios</h2>
    <p class="home-seccion-sub">Tratamientos diseñados para tu relajación y cuidado personal.</p>

    <?php if (!empty($serviciosDestacados)): ?>
        <div class="grid grid-3">
            <?php foreach ($serviciosDestacados as $s): ?>
                <article class="card catalog-card home-card">
                    <?php if ($s->getImagen()): ?>
                        <img src="<?= htmlspecialchars($s->getImagen()) ?>"
                            alt="<?= htmlspecialchars($s->getNombre()) ?>">
                    <?php else: ?>
                        <!-- Sin imagen registrada para este servicio: se puede
                             agregar desde el CRUD de Servicios del panel admin
                             (se guarda en assets/img/servicios/). -->
                        <div class="home-service-placeholder">
                            <i class="fa-solid fa-spa"></i>
                        </div>
                    <?php endif; ?>
                    <div class="catalog-content">
                        <span class="home-card-categoria"><?= htmlspecialchars($s->getNombreCategoria()) ?></span>
                        <h3 class="catalog-title"><?= htmlspecialchars($s->getNombre()) ?></h3>
                        <p class="home-card-descripcion">
                            <?= htmlspecialchars($s->getDescripcion() ?? 'Tratamiento profesional para tu cuidado y bienestar.') ?>
                        </p>
                        <div class="home-card-footer">
                            <span class="catalog-price">$<?= number_format($s->getPrecio(), 2) ?></span>
                            <a href="<?= $urlAgendarCita ?>" class="btn btn-primary">
                                <i class="fa-solid fa-calendar-check"></i> Agendar cita
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <p class="home-seccion-enlace">
            <a href="index.php?controller=sitio&action=servicios" class="btn btn-outline">Ver todos los servicios</a>
        </p>
    <?php else: ?>
        <p class="text-center">Muy pronto podrás explorar nuestros tratamientos de spa y belleza.</p>
    <?php endif; ?>
</section>

<!-- PRODUCTOS -->
<section class="section container" id="productos">
    <h2 class="section-title">Productos destacados</h2>
    <p class="home-seccion-sub">Cuida tu piel y tu cabello en casa con nuestra selección.</p>

    <?php if (!empty($productosDestacados)): ?>
        <div class="grid grid-3">
            <?php foreach ($productosDestacados as $p): ?>
                <article class="card catalog-card home-card">
                    <img src="<?= !empty($p['imagen']) ? htmlspecialchars($p['imagen']) : 'assets/img/no-image.png' ?>"
                        alt="<?= htmlspecialchars($p['nombre']) ?>">
                    <div class="catalog-content">
                        <h3 class="catalog-title"><?= htmlspecialchars($p['nombre']) ?></h3>
                        <div class="home-card-footer">
                            <span class="catalog-price">$<?= number_format((float)$p['precio'], 2) ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="home-seccion-enlace">
            <a href="index.php?controller=clienteProd&action=catalogo" class="btn btn-outline">Ver catálogo completo</a>
        </p>
    <?php else: ?>
        <p class="text-center">Estamos preparando un catálogo de productos para el cuidado de tu piel y cabello.</p>
    <?php endif; ?>
</section>

<!-- CTA -->
<section class="cta">
    <?php if (!$sesionActiva): ?>
        <h2>¿Aún no tienes una cuenta?</h2>
        <p>Regístrate en unos segundos para agendar citas y comprar en línea.</p>
        <a href="index.php?controller=auth&action=registro" class="btn btn-outline">Registrarme ahora</a>
    <?php else: ?>
        <h2>Tu momento de bienestar te espera</h2>
        <p>Agenda tu próxima cita o revisa tu cuenta.</p>
        <a href="<?= $urlAgendarCita ?>" class="btn btn-outline">Agendar cita</a>
    <?php endif; ?>
</section>

<?php require_once "views/layouts/footer.php"; ?>
