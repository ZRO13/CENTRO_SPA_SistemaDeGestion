<?php


// DAO - Capa de acceso a datos: ejecuta las consultas PDO sobre la tabla
// 'usuarios', entidad central de autenticación de la que dependen
// empleados y clientes. No contiene lógica de negocio ni validaciones;
// esa responsabilidad pertenece a los Controllers.

require_once "models/dao/BaseDAO.php";
require_once "models/dto/usuarios/usuario.php";

class UsuarioDAO extends BaseDAO
{
    // Permite recibir una conexión externa opcional para las transacciones
    // (por ejemplo, cuando ClienteDAO/EmpleadoDAO necesitan compartir la
    // misma transacción que UsuarioDAO al crear un usuario + su rol).
    public function __construct(?PDO $conexionCompartida = null)
    {
        parent::__construct();
        if ($conexionCompartida !== null) {
            $this->setConexion($conexionCompartida);
        }
    }

    // Convierte una fila del resultset (con JOIN a roles) en un DTO Usuario.
    private function mapearFila(array $fila): Usuario
    {
        return new Usuario(
            (int)$fila['id_usuario'],
            (int)$fila['id_rol'],
            $fila['nombre'],
            $fila['apellido'],
            $fila['correo'],
            $fila['celular'],
            $fila['username'],
            $fila['password'],
            (bool)$fila['estado'],
            $fila['fecha_creacion'],
            $fila['nombre_rol'] ?? ''
        );
    }

    public function listar(): array
    {
        $lista = [];
        try {
            $sql = "SELECT u.*, r.nombre AS nombre_rol
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    ORDER BY u.fecha_creacion DESC";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            while ($fila = $stmt->fetch()) {
                $lista[] = $this->mapearFila($fila);
            }
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::listar -> " . $e->getMessage());
        }
        return $lista;
    }

    // Búsqueda utilizada por el CRUD de Usuarios (por nombre, apellido, correo o username).
    public function buscar(string $termino): array
    {
        $lista = [];
        try {
            $sql = "SELECT u.*, r.nombre AS nombre_rol
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.nombre LIKE :termino1
                       OR u.apellido LIKE :termino2
                       OR u.correo LIKE :termino3
                       OR u.username LIKE :termino4
                    ORDER BY u.fecha_creacion DESC";

            $stmt = $this->conexion->prepare($sql);
            $comodin = "%{$termino}%";
            $stmt->execute([
                ':termino1' => $comodin,
                ':termino2' => $comodin,
                ':termino3' => $comodin,
                ':termino4' => $comodin,
            ]);

            while ($fila = $stmt->fetch()) {
                $lista[] = $this->mapearFila($fila);
            }
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::buscar -> " . $e->getMessage());
        }
        return $lista;
    }

    public function buscarPorId(int $idUsuario): ?Usuario
    {
        try {
            $sql = "SELECT u.*, r.nombre AS nombre_rol
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.id_usuario = :id_usuario";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);

            $fila = $stmt->fetch();
            return $fila ? $this->mapearFila($fila) : null;
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::buscarPorId -> " . $e->getMessage());
            return null;
        }
    }

    // Utilizado por el login: acepta username o correo como credencial.
    public function buscarPorCredencial(string $credencial): ?Usuario
    {
        try {
            $sql = "SELECT u.*, r.nombre AS nombre_rol
                    FROM usuarios u
                    INNER JOIN roles r ON u.id_rol = r.id_rol
                    WHERE u.username = :credencial1 OR u.correo = :credencial2
                    LIMIT 1";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([
                ':credencial1' => $credencial,
                ':credencial2' => $credencial,
            ]);

            $fila = $stmt->fetch();
            return $fila ? $this->mapearFila($fila) : null;
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::buscarPorCredencial -> " . $e->getMessage());
            return null;
        }
    }

    // Valida unicidad de correo antes de insertar/actualizar.
    // $excluirId permite ignorar el propio registro al editar.
    public function existeCorreo(string $correo, ?int $excluirId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE correo = :correo";
            $params = [':correo' => $correo];

            if ($excluirId !== null) {
                $sql .= " AND id_usuario != :id_usuario";
                $params[':id_usuario'] = $excluirId;
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);

            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::existeCorreo -> " . $e->getMessage());
            return false;
        }
    }

    // Valida unicidad de username antes de insertar/actualizar.
    public function existeUsername(string $username, ?int $excluirId = null): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE username = :username";
            $params = [':username' => $username];

            if ($excluirId !== null) {
                $sql .= " AND id_usuario != :id_usuario";
                $params[':id_usuario'] = $excluirId;
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);

            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::existeUsername -> " . $e->getMessage());
            return false;
        }
    }

    // Inserta un usuario nuevo. El password ya debe llegar hasheado con
    // password_hash() (responsabilidad del Controller). Devuelve el ID
    // generado o null si la operación falló.
    public function insertar(Usuario $usuario): ?int
    {
        try {
            $sql = "INSERT INTO usuarios (id_rol, nombre, apellido, correo, celular, username, password, estado)
                    VALUES (:id_rol, :nombre, :apellido, :correo, :celular, :username, :password, :estado)";

            $stmt = $this->conexion->prepare($sql);

            $exito = $stmt->execute([
                ':id_rol'   => $usuario->getIdRol(),
                ':nombre'   => $usuario->getNombre(),
                ':apellido' => $usuario->getApellido(),
                ':correo'   => $usuario->getCorreo(),
                ':celular'  => $usuario->getCelular(),
                ':username' => $usuario->getUsername(),
                ':password' => $usuario->getPassword(),
                ':estado'   => $usuario->getEstado()
            ]);

            return $exito ? (int)$this->conexion->lastInsertId() : null;
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::insertar -> " . $e->getMessage());
            return null;
        }
    }

    // Actualiza los datos personales/rol de un usuario. La contraseña se
    // gestiona aparte en actualizarPassword() para no sobreescribirla
    // accidentalmente cuando el formulario de edición no la incluye.
    public function actualizar(Usuario $usuario): bool
    {
        try {
            $sql = "UPDATE usuarios SET
                        id_rol = :id_rol,
                        nombre = :nombre,
                        apellido = :apellido,
                        correo = :correo,
                        celular = :celular,
                        username = :username,
                        estado = :estado
                    WHERE id_usuario = :id_usuario";

            $stmt = $this->conexion->prepare($sql);

            return $stmt->execute([
                ':id_rol'     => $usuario->getIdRol(),
                ':nombre'     => $usuario->getNombre(),
                ':apellido'   => $usuario->getApellido(),
                ':correo'     => $usuario->getCorreo(),
                ':celular'    => $usuario->getCelular(),
                ':username'   => $usuario->getUsername(),
                ':estado'     => $usuario->getEstado(),
                ':id_usuario' => $usuario->getIdUsuario()
            ]);
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::actualizar -> " . $e->getMessage());
            return false;
        }
    }

    public function actualizarPassword(int $idUsuario, string $passwordHash): bool
    {
        try {
            $sql = "UPDATE usuarios SET password = :password WHERE id_usuario = :id_usuario";

            $stmt = $this->conexion->prepare($sql);

            return $stmt->execute([
                ':password'   => $passwordHash,
                ':id_usuario' => $idUsuario
            ]);
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::actualizarPassword -> " . $e->getMessage());
            return false;
        }
    }

    // Eliminación lógica: al desactivar el usuario también se bloquea su
    // acceso al sistema, ya que empleados/clientes no tienen su propio
    // campo de estado (viven de 'usuarios.estado').
    public function eliminar(int $idUsuario): bool
    {
        try {
            $sql = "UPDATE usuarios SET estado = 0 WHERE id_usuario = :id_usuario";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([':id_usuario' => $idUsuario]);
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::eliminar -> " . $e->getMessage());
            return false;
        }
    }

    public function reactivar(int $idUsuario): bool
    {
        try {
            $sql = "UPDATE usuarios SET estado = 1 WHERE id_usuario = :id_usuario";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([':id_usuario' => $idUsuario]);
        } catch (PDOException $e) {
            error_log("Error en UsuarioDAO::reactivar -> " . $e->getMessage());
            return false;
        }
    }
}
