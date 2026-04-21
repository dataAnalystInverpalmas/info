<?php
namespace App\Models;

use App\Helpers\Database;

class Bitacora {

    public static function getByTarea($tarea_id) {
        $conexion = Database::getConnection();
        $stmt = $conexion->prepare(
            "SELECT b.id, b.tipo_registro, b.descripcion, b.descripcion_antes, b.cambios_json, b.autor, b.fecha_registro,
                    t.nombre AS tarea_nombre, t.descripcion AS tarea_descripcion,
                    p.nombre AS proyecto_nombre
             FROM bitacora b
             LEFT JOIN tareas t ON b.tarea_id = t.id
             LEFT JOIN proyectos p ON COALESCE(b.proyecto_id, t.proyecto_id) = p.id
             WHERE b.tarea_id = ?
             ORDER BY b.fecha_registro DESC"
        );
        $stmt->bind_param("i", $tarea_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_object()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function getAll() {
        $conexion = Database::getConnection();
        $result = $conexion->query(
            "SELECT b.id, b.tipo_registro, b.descripcion, b.descripcion_antes, b.cambios_json, b.autor, b.fecha_registro,
                    t.nombre AS tarea_nombre, t.descripcion AS tarea_descripcion,
                    p.nombre AS proyecto_nombre
             FROM bitacora b
             LEFT JOIN tareas t ON b.tarea_id = t.id
             LEFT JOIN proyectos p ON COALESCE(b.proyecto_id, t.proyecto_id) = p.id
             ORDER BY b.fecha_registro DESC"
        );
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}
