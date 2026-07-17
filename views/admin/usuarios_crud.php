<?php
$tituloPagina = "Usuarios";
$pageStyles = "assets/css/usuarios.css";
require_once "views/layouts/header.php";
?>



<main class="content">

    <div class="page-header">
        <h1>Gestión de Usuarios</h1>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php
        $mensajes = [
            'updated'             => ['tipo' => 'success', 'texto' => 'Usuario actualizado correctamente.'],
            'deleted'             => ['tipo' => 'success', 'texto' => 'Usuario desactivado correctamente.'],
            'reactivated'         => ['tipo' => 'success', 'texto' => 'Usuario reactivado correctamente.'],
            'campos_vacios'       => ['tipo' => 'danger',  'texto' => 'Completa todos los campos obligatorios.'],
            'correo_invalido'     => ['tipo' => 'danger',  'texto' => 'Ingresa un correo electrónico válido.'],
            'correo_existente'    => ['tipo' => 'danger',  'texto' => 'Ese correo ya está registrado.'],
            'username_existente'  => ['tipo' => 'danger',  'texto' => 'Ese nombre de usuario ya está en uso.'],
            'password_corta'      => ['tipo' => 'danger',  'texto' => 'La contraseña debe tener al menos 6 caracteres.'],
            'no_encontrado'       => ['tipo' => 'danger',  'texto' => 'El usuario solicitado no existe.'],
            'error'               => ['tipo' => 'danger',  'texto' => 'Ocurrió un error al procesar la solicitud.'],
        ];
        $estado = $mensajes[$_GET['status']] ?? null;
        ?>
        <?php if ($estado): ?>
            <div class="alert alert-<?= $estado['tipo'] ?>"><?= htmlspecialchars($estado['texto']) ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card">
        <form action="index.php" method="GET" class="usuarios-buscador">
            <input type="hidden" name="controller" value="usuario">
            <input type="hidden" name="action" value="listar">
            <input type="text" name="termino" placeholder="Buscar por nombre, apellido, correo o usuario..."
                value="<?= htmlspecialchars($_GET['termino'] ?? '') ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <?php if (!empty($_GET['termino'])): ?>
                <a href="index.php?controller=usuario&action=listar" class="btn btn-secondary">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3 class="card-title"><i class="fas fa-table"></i> Todas las cuentas del sistema</h3>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre completo</th>
                        <th>Usuario</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($u->getNombre() . ' ' . $u->getApellido()) ?></strong></td>
                                <td><?= htmlspecialchars($u->getUsername()) ?></td>
                                <td><?= htmlspecialchars($u->getCorreo()) ?></td>
                                <td><?= htmlspecialchars($u->getCelular()) ?></td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($u->getNombreRol()) ?></span></td>
                                <td>
                                    <?php if ($u->getEstado()): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="usuarios-acciones">
                                        <a href="index.php?controller=usuario&action=editar&id=<?= $u->getIdUsuario() ?>"
                                            class="btn btn-warning-soft" title="Editar usuario">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if ($u->getEstado()): ?>
                                            <a href="index.php?controller=usuario&action=eliminar&id=<?= $u->getIdUsuario() ?>"
                                                class="btn btn-danger" title="Desactivar usuario"
                                                onclick="return confirm('¿Desactivar esta cuenta? El usuario no podrá iniciar sesión.');">
                                                <i class="fas fa-user-slash"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="index.php?controller=usuario&action=reactivar&id=<?= $u->getIdUsuario() ?>"
                                                class="btn btn-success" title="Reactivar usuario"
                                                onclick="return confirm('¿Reactivar esta cuenta?');">
                                                <i class="fas fa-user-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No se encontraron usuarios.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

</div>
</div>
<?php
$pageScript = "assets/js/admin/usuarios.js";
require_once "views/layouts/footer.php";
?>
