<?php 

$tituloPagina = "Gestión de Reservas";
$pageStyles = "assets/css/crudReserv.css";
require_once 'views/layouts/header.php'; 
?>

<main class="content">

    <section class="page-header">
        <h1>Gestión de Reservas</h1>
        <p class="sub-titulo">Administra las citas y asigna personal</p>
    </section>

    <div class="filtros">
        <label>Filtrar por fecha:</label>
        <input type="date" id="filtroFecha" onchange="renderTabla()">
        
        <label>Buscador:</label>
        <input type="text" id="buscadorGeneral" placeholder="Buscar..." oninput="renderTabla()">
        
        <label>Estado:</label>
        <select id="filtroEstado" onchange="renderTabla()">
            <option value="todos">Todos</option>
            <option value="Pendiente">Pendiente</option>
            <option value="Atendida">Atendida</option>
            <option value="Cancelada">Cancelada</option>
        </select>
        <button onclick="limpiarFiltros()" class="btn btn-secondary">Limpiar</button>
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
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="cuerpoTabla"></tbody>
    </table>
</main>


<div id="modalEdicion" class="modal">
    <form id="formEdicion" style="position:fixed; top:20%; left:30%; background:white; padding:20px; border:1px solid #ccc; z-index:1000;">
        <input type="hidden" name="id_cita" id="editId">
        <label>Estado:</label>
        <select name="estado" id="editEstado">
            <option value="Pendiente">Pendiente</option>
            <option value="Atendida">Atendida</option>
            <option value="Cancelada">Cancelada</option>
        </select>
    
    <label>Empleado:</label>
    <select name="id_empleado" id="editEmpleado">
        <option value="">-- Cargando empleados... --</option>
    </select>

    <label>Observación:</label>
    <textarea name="observacion" id="editObservacion" style="width:100%"></textarea>
    
    <button type="button" class="btn btn-success" onclick="guardarEdicion()">Guardar Cambios</button>
    <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
    </form>
</div>
</div>
<?php 
$pageScript = "assets/js/admin/crudReserva.js";
require_once "views/layouts/footer.php"; 
?>