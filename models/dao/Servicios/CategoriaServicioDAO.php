<?php


// DAO de consulta para el catálogo fijo de categorías de servicio.

require_once "models/dao/BaseDAO.php";
require_once "models/dto/Servicios/categoriaServicio.php";

class CategoriaServicioDAO extends BaseDAO
{
    private function mapearFila(array $fila): CategoriaServicio
    {
        return new CategoriaServicio(
            (int)$fila['id_categoria_servicio'],
            $fila['nombre_categoria'],
            $fila['descripcion'] !== null ? $fila['descripcion'] : null,
            (bool)$fila['estado']
        );
    }

    public function listarActivas(): array
    {
        $categorias = [];

        try {
            $sql = "SELECT *
                    FROM categorias_servicio
                    WHERE estado = 1
                    ORDER BY nombre_categoria";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            while ($fila = $stmt->fetch()) {
                $categorias[] = $this->mapearFila($fila);
            }
        } catch (PDOException $e) {
            error_log("Error en CategoriaServicioDAO::listarActivas -> " . $e->getMessage());
        }

        return $categorias;
    }

    public function buscarPorId(int $idCategoriaServicio): ?CategoriaServicio
    {
        try {
            $sql = "SELECT *
                    FROM categorias_servicio
                    WHERE id_categoria_servicio = :id_categoria_servicio";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([':id_categoria_servicio' => $idCategoriaServicio]);

            $fila = $stmt->fetch();
            return $fila ? $this->mapearFila($fila) : null;
        } catch (PDOException $e) {
            error_log("Error en CategoriaServicioDAO::buscarPorId -> " . $e->getMessage());
            return null;
        }
    }
}
