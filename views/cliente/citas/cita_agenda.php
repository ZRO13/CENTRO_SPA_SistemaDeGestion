<?php 


//session_start();
/*if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?controller=auth&action=login");
    exit();
}*/
$tituloPagina = "Mis Citas";
$pageStyles = "assets/css/agend.css";
require_once "views/layouts/header_publico.php"; 
?>

<main class="content">
    <section class="welcome">
        <a href="index.php?controller=area-cliente&action=inicio" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Volver al Inicio
        </a>
    </section>
    
    <section class="page-header" style="text-align: center;">
        <h1>Agenda de Citas</h1>
        <p class="sub-titulo" style="color: black; text-align: center;">Consulta y gestiona tus citas programadas</p>
    </section>

    <!-- Filtros que ahora disparan funciones JS de consulta al servidor -->
    <div class="filtros">
        <label>Filtrar por fecha:</label>
        <input type="date" id="filtroFecha" onchange="actualizarTabla()">
        
        <label>Buscador:</label>
        <input type="text" id="buscadorGeneral" placeholder="Buscar..." oninput="actualizarTabla()">
        
        <label>Estado:</label>
        <select id="filtroEstado" onchange="actualizarTabla()">
                <option value="todos">Todos</option>
                <option value="Pendiente">Pendiente</option>
                <option value="Atendida">Atendida</option>
                <option value="Cancelada">Cancelada</option>
            </select>

        <button type="button" onclick="limpiarFiltros()" class="btn btn-secondary">Limpiar</button>
    </div>

    <table class="tabla-seccion">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Servicio</th>
                <th>Empleado</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Estado</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody id="cuerpoCita">
            <!-- Los datos se inyectarán aquí mediante JS -->
        </tbody>
    </table>
</main>
</div>
<?php
$pageScript = "assets/js/cita.js";
require_once "views/layouts/footer_cliente.php"; 
?>