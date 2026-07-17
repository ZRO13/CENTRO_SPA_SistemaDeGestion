<?php


// DAO - Capa de acceso a datos: ejecuta las consultas PDO sobre la tabla
// 'clientes'. Los datos personales viven en 'usuarios', por lo que todas
// las consultas de lectura se resuelven mediante INNER JOIN.
// (Renombrado desde Cliente.DAO.php para mantener la convención NombreDAO.php
// utilizada por el resto de los DAO del proyecto: UsuarioDAO, EmpleadoDAO, RolDAO)

require_once "models/dao/BaseDAO.php";
require_once "models/dto/Usuarios/cliente.php";

class ClienteDAO extends BaseDAO
{
    // Permitimos recibir una conexión externa opcional para las transacciones
    public function __construct(?PDO $conexionCompartida = null)
    {
        parent::__construct();

        if ($conexionCompartida !== null) {
            $this->setConexion($conexionCompartida);
        }
    }

    private function mapearFila(array $fila): Cliente
    {
        return new Cliente(
            (int)$fila['id_cliente'],
            (int)$fila['id_usuario'],
            $fila['nombre'],
            $fila['apellido'],
            $fila['correo'],
            $fila['celular'],
            $fila['username'],
            (bool)$fila['estado']
        );
    }

    private const SELECT_BASE = "SELECT c.*, u.nombre, u.apellido, u.correo, u.celular, u.username, u.estado
                                  FROM clientes c
                                  INNER JOIN usuarios u ON c.id_usuario = u.id_usuario";

    public function listar(): array
    {
        $lista = [];
        try {
            $sql = self::SELECT_BASE . " ORDER BY u.nombre, u.apellido";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            while ($fila = $stmt->fetch()) {
                $lista[] = $this->mapearFila($fila);
            }
        } catch (PDOException $e) {
            error_log("Error en ClienteDAO::listar -> " . $e->getMessage());
        }
        return $lista;
    }

    public function buscar(string $termino): array
    {
        $lista = [];
        try {
            $sql = self::SELECT_BASE . " WHERE u.nombre LIKE :termino1
                       OR u.apellido LIKE :termino2
                       OR u.correo LIKE :termino3
                       OR u.username LIKE :termino4
                    ORDER BY u.nombre, u.apellido";

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
            error_log("Error en ClienteDAO::buscar -> " . $e->getMessage());
        }
        return $lista;
    }

    public function buscarPorId(int $idCliente): ?Cliente
    {
        try {
            $sql = self::SELECT_BASE . " WHERE c.id_cliente = :id_cliente";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_cliente' => $idCliente]);

            $fila = $stmt->fetch();
            return $fila ? $this->mapearFila($fila) : null;
        } catch (PDOException $e) {
            error_log("Error en ClienteDAO::buscarPorId -> " . $e->getMessage());
            return null;
        }
    }

    public function buscarPorIdUsuario(int $idUsuario): ?Cliente
    {
        try {
            $sql = self::SELECT_BASE . " WHERE c.id_usuario = :id_usuario";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);

            $fila = $stmt->fetch();
            return $fila ? $this->mapearFila($fila) : null;
        } catch (PDOException $e) {
            error_log("Error en ClienteDAO::buscarPorIdUsuario -> " . $e->getMessage());
            return null;
        }
    }

    // Inserta la fila de 'clientes' asociada a un usuario ya creado
    // (el registro en 'usuarios' se realiza previamente desde el Controller).
    public function insertar(Cliente $cliente): bool
    {
        try {
            $sql = "INSERT INTO clientes (id_usuario) VALUES (:id_usuario)";

            $stmt = $this->conexion->prepare($sql);

            return $stmt->execute([
                ':id_usuario' => $cliente->getIdUsuario()
            ]);
        } catch (PDOException $e) {
            error_log("Error en ClienteDAO::insertar -> " . $e->getMessage());
            return false;
        }
    }

    // La eliminación lógica real se maneja desactivando el usuario asociado
    // (ver UsuarioDAO::eliminar), ya que 'clientes' no posee su propio
    // campo de estado. Este método queda disponible para una baja física.
    public function eliminar(int $idCliente): bool
    {
        try {
            $sql = "DELETE FROM clientes WHERE id_cliente = :id_cliente";

            $stmt = $this->conexion->prepare($sql);
            return $stmt->execute([':id_cliente' => $idCliente]);
        } catch (PDOException $e) {
            error_log("Error en ClienteDAO::eliminar -> " . $e->getMessage());
            return false;
        }
    }
}
