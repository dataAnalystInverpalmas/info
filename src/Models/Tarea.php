<?php
namespace App\Models;

use App\Helpers\Database;

class Tarea {

    public static function getAll($usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                        t.porcentaje_avance, t.fecha_vencimiento, t.fecha_inicio, t.responsable, t.quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 WHERE t.usuario_id = ?
                 ORDER BY FIELD(t.prioridad,'urgente','alta','media','baja'), t.fecha_vencimiento ASC"
            );
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conexion->query(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                        t.porcentaje_avance, t.fecha_vencimiento, t.fecha_inicio, t.responsable, t.quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 ORDER BY FIELD(t.prioridad,'urgente','alta','media','baja'), t.fecha_vencimiento ASC"
            );
        }
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
