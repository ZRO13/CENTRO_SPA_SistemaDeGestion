<?php
$tituloPagina = "Clientes";
$pageStyles = "assets/css/clientes.css";
require_once "views/layouts/header.php";
?>



<main class="content">

    <div class="page-header">
        <h1>Gestión de Clientes</h1>
        <button type="button" class="btn btn-primary" id="btnToggleFormulario">
            <i class="fas fa-user-plus"></i> <span id="btnText">Nuevo Cliente</span>
        </button>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php
        $mensajes = [
            'success'             => ['tipo' => 'success', 'texto' => 'Cliente registrado correctamente.'],
            'updated'             => ['tipo' => 'success', 'texto' => 'Cliente actualizado correctamente.'],
            'deleted'             => ['tipo' => 'success', 'texto' => 'Cliente desactivado correctamente.'],
            'campos_vacios'       => ['tipo' => 'danger',  'texto' => 'Completa todos los campos obligatorios.'],
            'correo_invalido'     => ['tipo' => 'danger',  'texto' => 'Ingresa un correo electrónico válido.'],
            'correo_existente'    => ['tipo' => 'danger',  'texto' => 'Ese correo ya está registrado.'],
            'username_existente'  => ['tipo' => 'danger',  'texto' => 'Ese nombre de usuario ya está en uso.'],
            'password_corta'      => ['tipo' => 'danger',  'texto' => 'La contraseña debe tener al menos 6 caracteres.'],
            'no_encontrado'       => ['tipo' => 'danger',  'texto' => 'El cliente solicitado no existe.'],
            'error'               => ['tipo' => 'danger',  'texto' => 'Ocurrió un error al procesar la solicitud.'],
        ];
        $estado = $mensajes[$_GET['status']] ?? null;
        ?>
        <?php if ($estado): ?>
            <div class="alert alert-<?= $estado['tipo'] ?>"><?= htmlspecialchars($estado['texto']) ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <div id="seccionFormulario" class="card hidden clientes-form-card">
        <h3 class="card-title"><i class="fas fa-user-plus"></i> Registrar Nuevo Cliente</h3>
        <p class="clientes-form-hint">
            Los datos se guardarán simultáneamente en las tablas 'usuarios' y 'clientes'.
        </p>

        <form action="index.php?controller=cliente&action=registrarCliente" method="POST">
            <div class="clientes-form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej. Juan" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Ej. Pérez" required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" placeholder="juan@ejemplo.com" required>
                </div>

                <div class="form-group">
                    <label for="celular">Celular</label>
                    <input type="text" id="celular" name="celular" placeholder="Ej. 0999999999" required>
                </div>

                <div class="form-group">
                    <label for="username">Usuario</label>
                    <input type="text" id="username" name="username" placeholder="Nombre de usuario" required>
                </div>

                <div class="form-group">
                    <label for="password">Contraseña de acceso</label>
                    <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
                </div>
            </div>

            <div class="clientes-form-actions">
                <button type="button" class="btn btn-secondary" id="btnCancelarFormulario">Cancelar</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar Cliente
                </button>
            </div>
        </form>
    </div>

    <div class="card">
        <form action="index.php" method="GET" class="clientes-buscador">
            <input type="hidden" name="controller" value="cliente">
            <input type="hidden" name="action" value="listar">
            <input type="text" name="termino" placeholder="Buscar por nombre, apellido, correo o usuario..."
                value="<?= htmlspecialchars($_GET['termino'] ?? '') ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <?php if (!empty($_GET['termino'])): ?>
                <a href="index.php?controller=cliente&action=listar" class="btn btn-secondary">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3 class="card-title"><i class="fas fa-table"></i> Lista de Clientes Registrados</h3>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre completo</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clientes)): ?>
                        <?php foreach ($clientes as $c): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($c->getNombre() . ' ' . $c->getApellido()) ?></strong></td>
                                <td><?= htmlspecialchars($c->getCorreo()) ?></td>
                                <td><?= htmlspecialchars($c->getCelular()) ?></td>
                                <td><?= htmlspecialchars($c->getUsername()) ?></td>
                                <td>
                                    <?php if ($c->getEstado()): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="clientes-acciones">
                                        <a href="index.php?controller=cliente&action=editar&id=<?= $c->getIdCliente() ?>"
                                            class="btn btn-warning-soft" title="Editar Cliente">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?controller=cliente&action=eliminar&id=<?= $c->getIdCliente() ?>"
                                            class="btn btn-danger" title="Eliminar Cliente"
                                            onclick="return confirm('¿Estás seguro de que deseas desactivar este cliente?');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                No hay clientes registrados en el sistema actualmente.
                            </td>
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
$pageScript = "assets/js/admin/clientes.js";
require_once "views/layouts/footer.php";
?>
