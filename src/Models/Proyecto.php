<?php
namespace App\Models;

use App\Helpers\Database;

class Proyecto {

    public static function getAll($usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT p.id, p.nombre, p.categoria, p.descripcion, p.estado, p.fecha_inicio, p.fecha_fin, p.fecha_creacion,
                        ROUND(
                            COALESCE(SUM(
                                CASE t.prioridad
                                    WHEN 'urgente' THEN 4
                                    WHEN 'alta' THEN 3
                                    WHEN 'media' THEN 2
                                    ELSE 1
                                END * COALESCE(t.porcentaje_avance, 0)
                            ) / NULLIF(SUM(
                                CASE t.prioridad
                                    WHEN 'urgente' THEN 4
                                    WHEN 'alta' THEN 3
                                    WHEN 'media' THEN 2
                                    ELSE 1
                                END
                            ), 0), 0)
                        ) AS avance_proyecto
                 FROM proyectos p
                 LEFT JOIN tareas t ON t.proyecto_id = p.id
                       WHERE (p.usuario_id = ? OR p.usuario_id IS NULL)
                 GROUP BY p.id, p.nombre, p.categoria, p.descripcion, p.estado, p.fecha_inicio, p.fecha_fin, p.fecha_creacion
                 ORDER BY p.fecha_creacion DESC"
            );
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conexion->query(
                "SELECT p.id, p.nombre, p.categoria, p.descripcion, p.estado, p.fecha_inicio, p.fecha_fin, p.fecha_creacion,
                        ROUND(
                            COALESCE(SUM(
                                CASE t.prioridad
                                    WHEN 'urgente' THEN 4
                                    WHEN 'alta' THEN 3
                                    WHEN 'media' THEN 2
                                    ELSE 1
                                END * COALESCE(t.porcentaje_avance, 0)
                            ) / NULLIF(SUM(
                                CASE t.prioridad
                                    WHEN 'urgente' THEN 4
                                    WHEN 'alta' THEN 3
                                    WHEN 'media' THEN 2
                                    ELSE 1
                                END
                            ), 0), 0)
                        ) AS avance_proyecto
                 FROM proyectos p
                 LEFT JOIN tareas t ON t.proyecto_id = p.id
                 GROUP BY p.id, p.nombre, p.categoria, p.descripcion, p.estado, p.fecha_inicio, p.fecha_fin, p.fecha_creacion
                 ORDER BY p.fecha_creacion DESC"
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

    public static function getCategorias($usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                                "SELECT DISTINCT categoria
                                 FROM proyectos
                                 WHERE categoria IS NOT NULL
                                     AND categoria != ''
                                     AND (usuario_id = ? OR usuario_id IS NULL)
                                 ORDER BY categoria"
            );
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conexion->query(
                "SELECT DISTINCT categoria FROM proyectos WHERE categoria IS NOT NULL AND categoria != '' ORDER BY categoria"
            );
        }
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
