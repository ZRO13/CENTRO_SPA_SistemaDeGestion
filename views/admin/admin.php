<?php require_once "views/layouts/header.php"; ?>



<main class="content">

    <section class="welcome">
        <h2>Bienvenido al sistema de gestión</h2>
        <p>Seleccione el módulo que desea administrar.</p>
    </section>

    <!--Seccion CRUD USUARIOS-->
    <section class="module-section">
        <h3>Administración</h3>
        <div class="modules-grid">
            <article class="card module-card" data-url="?controller=usuario&action=listar">
                <i class="fa-solid fa-user-gear module-icon"></i>
                <h4>Usuarios</h4>
                <p>Administrar todas las cuentas del sistema.</p>
            </article>

            <article class="card module-card" data-url="?controller=cliente&action=listar">
                <i class="fa-solid fa-users module-icon"></i>
                <h4>Clientes</h4>
                <p>Gestionar información de los clientes.</p>
            </article>

            <article class="card module-card" data-url="?controller=empleado&action=listar">
                <i class="fa-solid fa-user-tie module-icon"></i>
                <h4>Empleados</h4>
                <p>Administrar la información del personal.</p>
            </article>
        </div>
    </section>

    <!--Seccion CRUD SPA-->
    <section class="module-section">
        <h3>Operación del Spa</h3>
        <div class="modules-grid">
            <article class="card module-card" data-url="?controller=servicio&action=listar">
                <i class="fa-solid fa-spa module-icon"></i>
                <h4>Servicios</h4>
                <p>Administrar tratamientos y servicios.</p>
            </article>

            <article class="card module-card" data-url="?controller=producto&action=listar">
                <i class="fa-solid fa-box-open module-icon"></i>
                <h4>Productos</h4>
                <p>Gestionar el catálogo de productos.</p>
            </article>

            <article class="card module-card" data-url="?controller=citas&action=index">
                <i class="fa-solid fa-calendar-check module-icon"></i>
                <h4>Citas</h4>
                <p>Consultar y administrar citas.</p>
            </article>

            <article class="card module-card" data-url="?controller=venta&action=index">
                <i class="fa-solid fa-cash-register module-icon"></i>
                <h4>Ventas</h4>
                <p>Consultar ventas realizadas.</p>
            </article>
        </div>
    </section>

</main>

</div>
</div> <?php 
$pageScript = "assets/js/admin/admin.js";
require_once "views/layouts/footer.php"; 
?>