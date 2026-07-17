<?php
$tituloPagina = "Empleados";
$pageStyles = "assets/css/empleados.css";
require_once "views/layouts/header.php";
?>



<main class="content">

    <div class="page-header">
        <h1>Gestión de Empleados</h1>
        <button type="button" class="btn btn-primary" id="btnToggleFormulario">
            <i class="fas fa-user-plus"></i> <span id="btnText">Nuevo Empleado</span>
        </button>
    </div>

    <?php if (isset($_GET['status'])): ?>
        <?php
        $mensajes = [
            'success'             => ['tipo' => 'success', 'texto' => 'Empleado registrado correctamente.'],
            'updated'             => ['tipo' => 'success', 'texto' => 'Empleado actualizado correctamente.'],
            'deleted'             => ['tipo' => 'success', 'texto' => 'Empleado desactivado correctamente.'],
            'campos_vacios'       => ['tipo' => 'danger',  'texto' => 'Completa todos los campos obligatorios.'],
            'correo_invalido'     => ['tipo' => 'danger',  'texto' => 'Ingresa un correo electrónico válido.'],
            'correo_existente'    => ['tipo' => 'danger',  'texto' => 'Ese correo ya está registrado.'],
            'username_existente'  => ['tipo' => 'danger',  'texto' => 'Ese nombre de usuario ya está en uso.'],
            'password_corta'      => ['tipo' => 'danger',  'texto' => 'La contraseña debe tener al menos 6 caracteres.'],
            'no_encontrado'       => ['tipo' => 'danger',  'texto' => 'El empleado solicitado no existe.'],
            'error'               => ['tipo' => 'danger',  'texto' => 'Ocurrió un error al procesar la solicitud.'],
        ];
        $estado = $mensajes[$_GET['status']] ?? null;
        ?>
        <?php if ($estado): ?>
            <div class="alert alert-<?= $estado['tipo'] ?>"><?= htmlspecialchars($estado['texto']) ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <div id="seccionFormulario" class="card hidden empleados-form-card">
        <h3 class="card-title"><i class="fas fa-user-plus"></i> Registrar Nuevo Empleado</h3>
        <p class="empleados-form-hint">
            Se creará una cuenta de acceso (rol Colaborador) junto con la información laboral del empleado.
        </p>

        <form action="index.php?controller=empleado&action=registrar" method="POST">
            <div class="empleados-form-grid">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Ej. María" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido" placeholder="Ej. López" required>
                </div>

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
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
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres" required>
                </div>

                <div class="form-group">
                    <label for="cargo">Cargo</label>
                    <input type="text" id="cargo" name="cargo" placeholder="Ej. Cosmetóloga" required>
                </div>

                <div class="form-group">
                    <label for="fecha_ingreso">Fecha de ingreso</label>
                    <input type="date" id="fecha_ingreso" name="fecha_ingreso" required>
                </div>
            </div>

            <div class="empleados-form-actions">
                <button type="button" class="btn btn-secondary" id="btnCancelarFormulario">Cancelar</button>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Guardar Empleado
                </button>
            </div>
        </form>
    </div>

    <div class="card">
        <form action="index.php" method="GET" class="empleados-buscador">
            <input type="hidden" name="controller" value="empleado">
            <input type="hidden" name="action" value="listar">
            <input type="text" name="termino" placeholder="Buscar por nombre, apellido, correo o cargo..."
                value="<?= htmlspecialchars($_GET['termino'] ?? '') ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Buscar</button>
            <?php if (!empty($_GET['termino'])): ?>
                <a href="index.php?controller=empleado&action=listar" class="btn btn-secondary">Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card">
        <h3 class="card-title"><i class="fas fa-table"></i> Lista de Empleados</h3>

        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre completo</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th>Cargo</th>
                        <th>Fecha de ingreso</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($empleados)): ?>
                        <?php foreach ($empleados as $e): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($e->getNombre() . ' ' . $e->getApellido()) ?></strong></td>
                                <td><?= htmlspecialchars($e->getCorreo()) ?></td>
                                <td><?= htmlspecialchars($e->getCelular()) ?></td>
                                <td><?= htmlspecialchars($e->getCargo()) ?></td>
                                <td><?= htmlspecialchars($e->getFechaIngreso()) ?></td>
                                <td>
                                    <?php if ($e->getEstado()): ?>
                                        <span class="badge badge-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="empleados-acciones">
                                        <a href="index.php?controller=empleado&action=editar&id=<?= $e->getIdEmpleado() ?>"
                                            class="btn btn-warning-soft" title="Editar empleado">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?controller=empleado&action=eliminar&id=<?= $e->getIdEmpleado() ?>"
                                            class="btn btn-danger" title="Desactivar empleado"
                                            onclick="return confirm('¿Desactivar a este empleado? No podrá iniciar sesión.');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No hay empleados registrados.</td>
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
$pageScript = "assets/js/admin/empleados.js";
require_once "views/layouts/footer.php";
?>
