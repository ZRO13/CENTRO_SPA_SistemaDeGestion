<?php
require_once "models/dao/BaseDAO.php";
require_once "models/dto/cita/Servicio.php";

class ServicioDAO extends BaseDAO {

    public function __construct()
        {
            parent::__construct();
        }

    //no tocar esta funcion, es para obtener todos los servicios para las citas
    public function listarPorCategoria($id_categoria) {
        $sql = "SELECT id_servicio, nombre FROM servicios WHERE id_categoria_servicio = :id_cat";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':id_cat', $id_categoria, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Servicios destacados para la página de Inicio pública (reutiliza la
    // misma tabla 'servicios' que administra este módulo, sin duplicar datos).
    public function listarDestacados(int $limite = 3): array {
        $sql = "SELECT id_servicio, nombre, descripcion, precio
                FROM servicios
                WHERE disponibilidad = 1
                ORDER BY id_servicio
                LIMIT :limite";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}