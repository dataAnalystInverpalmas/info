<?php
namespace App\Models;

use App\Helpers\Database;

class Proyecto {

    public static function getAll() {
        $conexion = Database::getConnection();
        $result = $conexion->query(
            "SELECT id, nombre, categoria, descripcion, estado, fecha_inicio, fecha_fin, fecha_creacion
             FROM proyectos
             ORDER BY fecha_creacion DESC"
        );
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public static function getCategorias() {
        $conexion = Database::getConnection();
        $result = $conexion->query(
            "SELECT DISTINCT categoria FROM proyectos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria"
        );
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) {
                $data[] = $row->categoria;
            }
        }
        return $data;
    }

    public static function getById($id) {
        $conexion = Database::getConnection();
        $stmt = $conexion->prepare("SELECT * FROM proyectos WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }
}
