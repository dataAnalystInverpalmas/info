<?php
namespace App\Models;

use App\Helpers\Database;

class Tarea {

    public static function getAll() {
        $conexion = Database::getConnection();
        $result = $conexion->query(
            "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                    t.fecha_vencimiento, t.fecha_inicio, t.responsable,
                    t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
             FROM tareas t
             LEFT JOIN proyectos p ON t.proyecto_id = p.id
             ORDER BY FIELD(t.prioridad,'urgente','alta','media','baja'), t.fecha_vencimiento ASC"
        );
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public static function getById($id) {
        $conexion = Database::getConnection();
        $stmt = $conexion->prepare("SELECT * FROM tareas WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }
}
