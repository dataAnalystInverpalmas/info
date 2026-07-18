<?php
namespace App\Models;

use App\Helpers\Database;

class Bitacora {

    public static function getByTarea($tarea_id, $usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT b.id, b.tipo_registro, b.descripcion, b.descripcion_antes, b.cambios_json, b.autor, b.fecha_registro,
                        t.nombre AS tarea_nombre, t.descripcion AS tarea_descripcion,
                        p.nombre AS proyecto_nombre
                 FROM bitacora b
                 LEFT JOIN tareas t ON b.tarea_id = t.id
                 LEFT JOIN proyectos p ON COALESCE(b.proyecto_id, t.proyecto_id) = p.id
                 WHERE b.tarea_id = ? AND (t.usuario_id = ? OR t.usuario_id IS NULL)
                 ORDER BY b.fecha_registro DESC"
            );
            if (!$stmt) return [];
            $stmt->bind_param("ii", $tarea_id, $usuario_id);
        } else {
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
            if (!$stmt) return [];
            $stmt->bind_param("i", $tarea_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_object()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function getNotasByProyecto($proyecto_id, $usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT b.id, b.tipo_registro, b.descripcion, b.descripcion_antes, b.cambios_json, b.autor, b.fecha_registro,
                        t.nombre AS tarea_nombre, t.descripcion AS tarea_descripcion,
                        p.nombre AS proyecto_nombre
                 FROM bitacora b
                 LEFT JOIN tareas t ON b.tarea_id = t.id
                 LEFT JOIN proyectos p ON COALESCE(b.proyecto_id, t.proyecto_id) = p.id
                 WHERE b.proyecto_id = ?
                   AND b.tipo_registro = 'nota'
                   AND COALESCE(b.usuario_id, t.usuario_id) = ?
                 ORDER BY b.fecha_registro DESC"
            );
            if (!$stmt) return [];
            $stmt->bind_param("ii", $proyecto_id, $usuario_id);
        } else {
            $stmt = $conexion->prepare(
                "SELECT b.id, b.tipo_registro, b.descripcion, b.descripcion_antes, b.cambios_json, b.autor, b.fecha_registro,
                        t.nombre AS tarea_nombre, t.descripcion AS tarea_descripcion,
                        p.nombre AS proyecto_nombre
                 FROM bitacora b
                 LEFT JOIN tareas t ON b.tarea_id = t.id
                 LEFT JOIN proyectos p ON COALESCE(b.proyecto_id, t.proyecto_id) = p.id
                 WHERE b.proyecto_id = ?
                   AND b.tipo_registro = 'nota'
                 ORDER BY b.fecha_registro DESC"
            );
            if (!$stmt) return [];
            $stmt->bind_param("i", $proyecto_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_object()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function getAll($usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            // Filtra por el usuario del registro de bitácora, o si es null, por el usuario dueño de la tarea
            $stmt = $conexion->prepare(
                "SELECT b.id, b.tipo_registro, b.descripcion, b.descripcion_antes, b.cambios_json, b.autor, b.fecha_registro,
                        t.nombre AS tarea_nombre, t.descripcion AS tarea_descripcion,
                        p.nombre AS proyecto_nombre
                 FROM bitacora b
                 LEFT JOIN tareas t ON b.tarea_id = t.id
                 LEFT JOIN proyectos p ON COALESCE(b.proyecto_id, t.proyecto_id) = p.id
                 WHERE COALESCE(b.usuario_id, t.usuario_id) = ?
                 ORDER BY b.fecha_registro DESC"
            );
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conexion->query(
                "SELECT b.id, b.tipo_registro, b.descripcion, b.descripcion_antes, b.cambios_json, b.autor, b.fecha_registro,
                        t.nombre AS tarea_nombre, t.descripcion AS tarea_descripcion,
                        p.nombre AS proyecto_nombre
                 FROM bitacora b
                 LEFT JOIN tareas t ON b.tarea_id = t.id
                 LEFT JOIN proyectos p ON COALESCE(b.proyecto_id, t.proyecto_id) = p.id
                 ORDER BY b.fecha_registro DESC"
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
}
