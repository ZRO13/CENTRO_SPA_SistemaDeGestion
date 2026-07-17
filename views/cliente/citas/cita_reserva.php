<?php 


$tituloPagina = "Reservar Citas";
$pageStyles = "assets/css/agend.css";
require_once 'views/layouts/header_publico.php'; 
// Obtener datos de la sesión para autocompletar
$usuarioLogueado = SesionHelper::usuarioActual();
$nombreCompleto = $usuarioLogueado ? ($usuarioLogueado['nombre'] . ' ' . $usuarioLogueado['apellido']) : '';

$servicios = $servicios ?? [];
$tratamientos = $tratamientos ?? [];
?>

<main class="content">
    <section class="welcome">
        <a href="index.php?controller=area-cliente&action=inicio" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
        </a>
    </section>
    
    <section class="page-header" style="text-align: center;">
        <h1>Registrar Reservación</h1>
        <p class="sub-titulo">Complete el formulario para registrar su cita</p>
    </section>

    <div class="container">
        <form id="formReserva" class="form-panel" method="POST">
            
            <div class="form-grid-layout">
                <div class="form-group">
                    <label for="nombre">Nombre completo</label>
                    <input type="text" name="nombre" id="nombre" 
                            value="<?php echo htmlspecialchars($nombreCompleto); ?>" 
                            readonly required>
                </div>
                
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" name="telefono" id="telefono" placeholder="Ej: 0999999999" required>
                </div> 
            </div> 
                
            <div class="form-group">
                <label>Seleccione sus servicios y tratamientos</label>
                <div id="contenedorServicios" class="checkbox-grid">
                    
                    <!-- Mostrar Servicios (Categoría 1) -->
                    <?php if (!empty($servicios)): ?>
                        <?php foreach ($servicios as $s): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="servicios[]" value="<?php echo $s['id_servicio']; ?>">
                                <span><?php echo htmlspecialchars($s['nombre']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Mostrar Tratamientos (Categoría 2) -->
                    <?php if (!empty($tratamientos)): ?>
                        <?php foreach ($tratamientos as $t): ?>
                            <label class="checkbox-item">
                                <input type="checkbox" name="servicios[]" value="<?php echo $t['id_servicio']; ?>">
                                <span><?php echo htmlspecialchars($t['nombre']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (empty($servicios) && empty($tratamientos)): ?>
                        <p class="empty-msg">No hay servicios disponibles actualmente.</p>
                    <?php endif; ?>
                </div>
            </div>  

            <div class="form-grid-layout">
                <div class="form-group">
                    <label for="fecha">Fecha</label>
                    <input type="date" name="fecha" id="fecha" required>
                </div>
                
                <div class="form-group">
                    <label for="hora">Hora</label>
                    <input type="time" name="hora" id="hora" required>
                </div>
            </div>
            <div class="acciones-form">
                <button type="submit" class="btn btn-success" >
                    Confirmar Reserva
                </button>

                <a href="index.php?controller=area-cliente&action=inicio" class="btn btn-secondary">
                    Cancelar
                </a>
            </div>            
        </form>
    </div>
</main>
</div>
</div>

<?php
$pageScript = "assets/js/registroCita.js";
require_once "views/layouts/footer_cliente.php"; 
?>