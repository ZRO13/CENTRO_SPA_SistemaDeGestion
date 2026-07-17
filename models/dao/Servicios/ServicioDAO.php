<?php


// DAO: acceso a datos del catálogo de servicios.

require_once "models/dao/BaseDAO.php";
require_once "models/dto/Servicios/servicio.php";

class ServicioDAO extends BaseDAO
{
    private const SELECT_BASE = "SELECT s.*, c.nombre_categoria
                                 FROM servicios s
                                 INNER JOIN categorias_servicio c
                                     ON s.id_categoria_servicio = c.id_categoria_servicio";

    private function mapearFila(array $fila): Servicio
    {
        return new Servicio(
            (int)$fila['id_servicio'],
            (int)$fila['id_categoria_servicio'],
            $fila['nombre'],
            $fila['descripcion'] !== null ? $fila['descripcion'] : null,
            (float)$fila['precio'],
            (bool)$fila['disponibilidad'],
            $fila['imagen'] !== null ? $fila['imagen'] : null,
            $fila['nombre_categoria']
        );
    }

    public function listar(): array
    {
        return $this->filtrar('', 0, null);
    }

    // Usada por el módulo de Citas: devuelve id y nombre de los servicios de
    // una categoría (arreglo asociativo, tal como lo esperan sus vistas).
    public function listarPorCategoria(int $id_categoria): array
    {
        try {
            $sql = "SELECT id_servicio, nombre FROM servicios WHERE id_categoria_servicio = :id_cat";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':id_cat', $id_categoria, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en ServicioDAO::listarPorCategoria -> " . $e->getMessage());
            return [];
        }
    }

    /**
     * Servicios visibles en el sitio público y en el área del cliente.
     */
    public function listarDisponibles(?int $limite = null): array
    {
        $servicios = [];

        try {
            $sql = self::SELECT_BASE . "
                    WHERE s.disponibilidad = 1
                      AND c.estado = 1
                    ORDER BY s.nombre";

            if ($limite !== null && $limite > 0) {
                $sql .= " LIMIT :limite";
            }

            $stmt = $this->conexion->prepare($sql);

            if ($limite !== null && $limite > 0) {
                $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            }

            $stmt->execute();

            while ($fila = $stmt->fetch()) {
                $servicios[] = $this->mapearFila($fila);
            }
        } catch (PDOException $e) {
            error_log("Error en ServicioDAO::listarDisponibles -> " . $e->getMessage());
        }

        return $servicios;
    }

    public function filtrar(string $termino = '', int $idCategoria = 0, ?int $disponibilidad = null): array
    {
        $servicios = [];

        try {
            $condiciones = [];
            $parametros = [];

            if ($termino !== '') {
                $condiciones[] = "(s.nombre LIKE :termino1
                                   OR s.descripcion LIKE :termino2
                                   OR c.nombre_categoria LIKE :termino3)";
                $comodin = "%{$termino}%";
                $parametros[':termino1'] = $comodin;
                $parametros[':termino2'] = $comodin;
                $parametros[':termino3'] = $comodin;
            }

            if ($idCategoria > 0) {
                $condiciones[] = "s.id_categoria_servicio = :id_categoria";
                $parametros[':id_categoria'] = $idCategoria;
            }

            if ($disponibilidad !== null) {
                $condiciones[] = "s.disponibilidad = :disponibilidad";
                $parametros[':disponibilidad'] = $disponibilidad;
            }

            $sql = self::SELECT_BASE;

            if (!empty($condiciones)) {
                $sql .= " WHERE " . implode(' AND ', $condiciones);
            }

            $sql .= " ORDER BY s.nombre";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($parametros);

            while ($fila = $stmt->fetch()) {
                $servicios[] = $this->mapearFila($fila);
            }
        } catch (PDOException $e) {
            error_log("Error en ServicioDAO::filtrar -> " . $e->getMessage());
        }

        return $servicios;
    }

    /**
     * Búsqueda pública: siempre limita los resultados a servicios disponibles.
     */
    public function filtrarDisponibles(string $termino = '', int $idCategoria = 0): array
    {
        return $this->filtrar($termino, $idCategoria, 1);
    }

    public function buscarPorId(int $idServicio): ?Servicio
    {
        try {
            $sql = self::SELECT_BASE . " WHERE s.id_servicio = :id_servicio";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_servicio' => $idServicio]);

            $fila = $stmt->fetch();
            return $fila ? $this->mapearFila($fila) : null;
        } catch (PDOException $e) {
            error_log("Error en ServicioDAO::buscarPorId -> " . $e->getMessage());
            return null;
        }
    }

    public function insertar(Servicio $servicio): bool
    {
        try {
            $sql = "INSERT INTO servicios
                        (id_categoria_servicio, nombre, descripcion, precio, disponibilidad, imagen)
                    VALUES
                        (:id_categoria_servicio, :nombre, :descripcion, :precio, :disponibilidad, :imagen)";

            $stmt = $this->conexion->prepare($sql);

            return $stmt->execute([
                ':id_categoria_servicio' => $servicio->getIdCategoriaServicio(),
                ':nombre' => $servicio->getNombre(),
                ':descripcion' => $servicio->getDescripcion(),
                ':precio' => $servicio->getPrecio(),
                ':disponibilidad' => $servicio->getDisponibilidad() ? 1 : 0,
                ':imagen' => $servicio->getImagen()
            ]);
        } catch (PDOException $e) {
            error_log("Error en ServicioDAO::insertar -> " . $e->getMessage());
            return false;
        }
    }

    public function actualizar(Servicio $servicio): bool
    {
        try {
            $sql = "UPDATE servicios SET
                        id_categoria_servicio = :id_categoria_servicio,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        precio = :precio,
                        disponibilidad = :disponibilidad,
                        imagen = :imagen
                    WHERE id_servicio = :id_servicio";

            $stmt = $this->conexion->prepare($sql);

            return $stmt->execute([
                ':id_categoria_servicio' => $servicio->getIdCategoriaServicio(),
                ':nombre' => $servicio->getNombre(),
                ':descripcion' => $servicio->getDescripcion(),
                ':precio' => $servicio->getPrecio(),
                ':disponibilidad' => $servicio->getDisponibilidad() ? 1 : 0,
                ':imagen' => $servicio->getImagen(),
                ':id_servicio' => $servicio->getIdServicio()
            ]);
        } catch (PDOException $e) {
            error_log("Error en ServicioDAO::actualizar -> " . $e->getMessage());
            return false;
        }
    }

    public function cambiarDisponibilidad(int $idServicio, bool $disponibilidad): bool
    {
        try {
            $sql = "UPDATE servicios
                    SET disponibilidad = :disponibilidad
                    WHERE id_servicio = :id_servicio";

            $stmt = $this->conexion->prepare($sql);

            return $stmt->execute([
                ':disponibilidad' => $disponibilidad ? 1 : 0,
                ':id_servicio' => $idServicio
            ]);
        } catch (PDOException $e) {
            error_log("Error en ServicioDAO::cambiarDisponibilidad -> " . $e->getMessage());
            return false;
        }
    }
}
