<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= isset($tituloPagina) ? htmlspecialchars($tituloPagina) . ' | ' : '' ?>Delux Spa</title>

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

    <link rel="stylesheet" href="assets/css/design-system/client.css">

    <?php
    if (isset($pageStyles)) {
        echo '<link rel="stylesheet" href="' . htmlspecialchars($pageStyles) . '">';
    }
    ?>
</head>

<body>

    <nav class="navbar">
        <a href="index.php?controller=sitio&action=inicio" class="logo">Delux Spa</a>

        <ul class="nav-menu">
            <li><a href="index.php?controller=sitio&action=inicio">Inicio</a></li>
            <li><a href="index.php?controller=sitio&action=servicios">Servicios</a></li>
            <li><a href="index.php?controller=clienteProd&action=catalogo">Productos</a></li>
            <?php if (isset($_SESSION['usuario'])): ?>
                <?php if (in_array($_SESSION['usuario']['nombre_rol'], ['Administrador', 'Colaborador'], true)): ?>
                    <li><a href="index.php?controller=admin&action=dashboard">Panel Administrativo</a></li>
                <?php else: ?>
                    <li><a href="index.php?controller=area-cliente&action=inicio">Mi Cuenta</a></li>
                <?php endif; ?>
                <li><a href="index.php?controller=auth&action=logout">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="index.php?controller=auth&action=login">Iniciar sesión</a></li>
                <li><a href="index.php?controller=auth&action=registro">Registrarme</a></li>
            <?php endif; ?>
        </ul>
    </nav>
