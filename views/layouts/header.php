<!DOCTYPE html>



<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= isset($tituloPagina) ? htmlspecialchars($tituloPagina) . ' | ' : '' ?>Panel Administrativo | Delux Spa</title>

    <!-- Google Fonts -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- CSS -->

    <link rel="stylesheet" href="assets/css/design-system/variables.css">

    <link rel="stylesheet" href="assets/css/design-system/components.css">

    <link rel="stylesheet" href="assets/css/admin.css">

    <?php
    if (isset($pageStyles)) {
        echo '<link rel="stylesheet" href="' . $pageStyles . '">';
    }
    ?>
</head>

<body>
    <div class="admin-layout">
        <div class="main-content">

            <header class="admin-navbar">

                <div class="navbar-title">
                    <span>Panel Administrativo</span>
                </div>

                <?php if (isset($mostrarMenu) && $mostrarMenu === true): ?>
                    <nav class="navbar-menu-internal">
                        <a href="?controller=admin&action=dashboard" class="nav-link-item">
                            <i class="fa-solid fa-house"></i> Inicio
                        </a>
                        <a href="?controller=usuario&action=listar" class="nav-link-item">
                            <i class="fa-solid fa-user-gear"></i> Usuarios
                        </a>
                        <a href="?controller=citas&action=index" class="nav-link-item">
                            <i class="fa-solid fa-calendar-check"></i> Citas
                        </a>
                        <a href="?controller=servicio&action=listar" class="nav-link-item">
                            <i class="fa-solid fa-spa"></i> Servicios
                        </a>
                        <a href="?controller=producto&action=listar" class="nav-link-item">
                            <i class="fa-solid fa-box-open"></i> Productos
                        </a>
                        <a href="?controller=venta&action=index" class="nav-link-item">
                            <i class="fa-solid fa-cash-register"></i> Ventas
                        </a>
                    </nav>
                <?php endif; ?>

                <div class="navbar-user">
                    <span>
                        <i class="fa-solid fa-user-shield"></i>
                        <?php
                        // Muestra el nombre del usuario autenticado (guardado en sesión por AuthController).
                        $usuarioSesion = $_SESSION['usuario'] ?? null;
                        echo $usuarioSesion
                            ? htmlspecialchars($usuarioSesion['nombre'] . ' ' . $usuarioSesion['apellido'])
                            : 'Invitado';
                        ?>
                    </span>

                    <a href="index.php?controller=auth&action=logout" class="btn btn-danger">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Cerrar sesión
                    </a>
                </div>

            </header>