<?php
namespace App\Models;

use App\Helpers\Database;

class SolicitudExtra {
    public static function getAll($usuario_id = null) {
        $conexion = Database::getConnection();
        $sql = "SELECT s.*, p.nombre AS proyecto_convertido_nombre,
                   tor.id AS tarea_origen_id,
                   tct.id AS tarea_convertida_id
                FROM solicitudes_extra s
            LEFT JOIN proyectos p ON p.id = s.proyecto_convertido_id
            LEFT JOIN tareas tor ON tor.id = s.tarea_origen_id
            LEFT JOIN tareas tct ON tct.solicitud_id = s.id";

        if ($usuario_id !== null) {
            $stmt = $conexion->prepare($sql . " WHERE s.usuario_id = ? OR s.usuario_id IS NULL
                                              ORDER BY s.fecha DESC, s.id DESC");
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param('i', $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conexion->query($sql . ' ORDER BY s.fecha DESC, s.id DESC');
        }

        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public static function getById(int $id, $usuario_id = null) {
        $conexion = Database::getConnection();
        $sql = 'SELECT s.*, tor.id AS tarea_origen_id, tct.id AS tarea_convertida_id
            FROM solicitudes_extra s
            LEFT JOIN tareas tor ON tor.id = s.tarea_origen_id
            LEFT JOIN tareas tct ON tct.solicitud_id = s.id
            WHERE s.id = ?';
        if ($usuario_id !== null) {
            $sql .= ' AND (usuario_id = ? OR usuario_id IS NULL)';
        }
        $stmt = $conexion->prepare($sql . ' LIMIT 1');
        if (!$stmt) {
            return null;
        }
        if ($usuario_id !== null) {
            $stmt->bind_param('ii', $id, $usuario_id);
        } else {
            $stmt->bind_param('i', $id);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }
}
