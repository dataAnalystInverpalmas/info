<?php
namespace App\Models;

use App\Helpers\Database;

class Proyecto {

    private static function tableExists($table) {
        $conexion = Database::getConnection();
        $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("s", $table);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return ((int)($row['total'] ?? 0)) > 0;
    }

    private static function columnExists($table, $column) {
        $conexion = Database::getConnection();
        $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ss", $table, $column);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return ((int)($row['total'] ?? 0)) > 0;
    }

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

    public static function getById($id, $usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare("SELECT * FROM proyectos WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL) LIMIT 1");
            if (!$stmt) {
                return null;
            }
            $stmt->bind_param("ii", $id, $usuario_id);
        } else {
            $stmt = $conexion->prepare("SELECT * FROM proyectos WHERE id = ? LIMIT 1");
            if (!$stmt) {
                return null;
            }
            $stmt->bind_param("i", $id);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }

    public static function getEstadisticas($id, $usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT
                    p.id,
                    p.nombre,
                    p.estado,
                    COUNT(t.id) AS total_tareas,
                    SUM(CASE WHEN t.estado = 'completada' THEN 1 ELSE 0 END) AS tareas_completadas,
                    SUM(CASE WHEN t.estado = 'en_progreso' THEN 1 ELSE 0 END) AS tareas_en_progreso,
                    SUM(CASE WHEN t.estado = 'pendiente' THEN 1 ELSE 0 END) AS tareas_pendientes,
                    SUM(CASE WHEN t.estado = 'cancelada' THEN 1 ELSE 0 END) AS tareas_canceladas,
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
                 WHERE p.id = ? AND (p.usuario_id = ? OR p.usuario_id IS NULL)
                 GROUP BY p.id, p.nombre, p.estado"
            );
            if (!$stmt) {
                return null;
            }
            $stmt->bind_param("ii", $id, $usuario_id);
        } else {
            $stmt = $conexion->prepare(
                "SELECT
                    p.id,
                    p.nombre,
                    p.estado,
                    COUNT(t.id) AS total_tareas,
                    SUM(CASE WHEN t.estado = 'completada' THEN 1 ELSE 0 END) AS tareas_completadas,
                    SUM(CASE WHEN t.estado = 'en_progreso' THEN 1 ELSE 0 END) AS tareas_en_progreso,
                    SUM(CASE WHEN t.estado = 'pendiente' THEN 1 ELSE 0 END) AS tareas_pendientes,
                    SUM(CASE WHEN t.estado = 'cancelada' THEN 1 ELSE 0 END) AS tareas_canceladas,
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
                 WHERE p.id = ?
                 GROUP BY p.id, p.nombre, p.estado"
            );
            if (!$stmt) {
                return null;
            }
            $stmt->bind_param("i", $id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }

    public static function getResumenEjecutivoMensual($anio, $usuario_id = null, $categoria = '', $estado = '') {
        $conexion = Database::getConnection();

        $tieneObjetivo = self::columnExists('proyectos', 'objetivo_alcance');
        $tieneResponsableProyecto = self::columnExists('proyectos', 'responsable_proyecto');
        $tieneOrdenTareas = self::columnExists('tareas', 'orden_ejecucion');
        $tieneTablaLogros = self::tableExists('proyecto_logros');
        $tieneTablaRiesgos = self::tableExists('proyecto_riesgos');

        $campoObjetivo = $tieneObjetivo
            ? "COALESCE(NULLIF(p.objetivo_alcance, ''), p.descripcion, '')"
            : "COALESCE(p.descripcion, '')";

        $campoResponsable = $tieneResponsableProyecto
            ? "COALESCE(NULLIF(p.responsable_proyecto, ''), resumen.responsable_principal, '')"
            : "COALESCE(resumen.responsable_principal, '')";

        $ordenDetalle = $tieneOrdenTareas
            ? "COALESCE(CONCAT(COALESCE(t.orden_ejecucion, 0), '. '), '')"
            : "''";

        $ordenByTarea = $tieneOrdenTareas
            ? "COALESCE(t.orden_ejecucion, 999999) ASC,\n                                COALESCE(t.fecha_inicio, t.fecha_creacion) ASC,\n                                t.id ASC"
            : "COALESCE(t.fecha_inicio, t.fecha_creacion) ASC,\n                                t.id ASC";

        $sql = "SELECT
                    p.id,
                    p.nombre,
                    p.categoria,
                    p.estado,
                    {$campoObjetivo} AS objetivo_alcance,
                    {$campoResponsable} AS responsable,
                    COALESCE(resumen.avance_proyecto, 0) AS avance_proyecto,
                    COALESCE(resumen.tareas_detalle, '') AS tareas_detalle,
                    COALESCE(resumen.total_tareas, 0) AS total_tareas,
                    COALESCE(resumen.tareas_completadas, 0) AS tareas_completadas,
                    COALESCE(resumen.tareas_en_progreso, 0) AS tareas_en_progreso,
                    COALESCE(resumen.tareas_pendientes, 0) AS tareas_pendientes,
                    COALESCE(mensual.actividades_mes, 0) AS actividades_mes,
                    COALESCE(mensual.completadas_mes, 0) AS completadas_mes,
                    COALESCE(logros.logros_total, 0) AS logros_total,
                    COALESCE(logros.logros_principales, '') AS logros_principales,
                    COALESCE(riesgos.riesgos_abiertos, 0) AS riesgos_abiertos,
                    COALESCE(riesgos.riesgos_pendientes, '') AS riesgos_pendientes
                FROM proyectos p
                LEFT JOIN (
                    SELECT
                        t.proyecto_id,
                        GROUP_CONCAT(
                            CONCAT(
                                {$ordenDetalle},
                                COALESCE(NULLIF(TRIM(t.nombre), ''), '(Sin nombre)'),
                                ' (',
                                COALESCE(t.porcentaje_avance, 0),
                                '%)'
                            )
                            ORDER BY
                                {$ordenByTarea}
                            SEPARATOR ' | '
                        ) AS tareas_detalle,
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
                        ) AS avance_proyecto,
                        COUNT(*) AS total_tareas,
                        SUM(CASE WHEN t.estado = 'completada' THEN 1 ELSE 0 END) AS tareas_completadas,
                        SUM(CASE WHEN t.estado = 'en_progreso' THEN 1 ELSE 0 END) AS tareas_en_progreso,
                        SUM(CASE WHEN t.estado = 'pendiente' THEN 1 ELSE 0 END) AS tareas_pendientes,
                        SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT NULLIF(TRIM(t.responsable), '') ORDER BY t.fecha_actualizacion DESC SEPARATOR ' | '), ' | ', 1) AS responsable_principal
                    FROM tareas t
                    GROUP BY t.proyecto_id
                ) resumen ON resumen.proyecto_id = p.id
                LEFT JOIN (
                    SELECT
                        t.proyecto_id,
                        COUNT(*) AS actividades_mes,
                        SUM(CASE WHEN t.estado = 'completada' THEN 1 ELSE 0 END) AS completadas_mes
                    FROM tareas t
                    WHERE YEAR(COALESCE(t.fecha_actualizacion, t.fecha_creacion)) = ?
                    GROUP BY t.proyecto_id
                ) mensual ON mensual.proyecto_id = p.id";

        if ($tieneTablaLogros) {
            $sql .= "
                LEFT JOIN (
                    SELECT
                        l.proyecto_id,
                        COUNT(*) AS logros_total,
                        SUBSTRING_INDEX(GROUP_CONCAT(TRIM(l.descripcion) ORDER BY COALESCE(l.fecha_logro, l.fecha_creacion) DESC SEPARATOR ' | '), ' | ', 3) AS logros_principales
                    FROM proyecto_logros l
                                        WHERE YEAR(COALESCE(l.fecha_logro, l.fecha_creacion)) = ?
                    GROUP BY l.proyecto_id
                ) logros ON logros.proyecto_id = p.id";
        } else {
            $sql .= "
                LEFT JOIN (
                    SELECT
                        b.proyecto_id,
                        COUNT(*) AS logros_total,
                        SUBSTRING_INDEX(GROUP_CONCAT(TRIM(b.descripcion) ORDER BY b.fecha_registro DESC SEPARATOR ' | '), ' | ', 3) AS logros_principales
                    FROM bitacora b
                                        WHERE YEAR(b.fecha_registro) = ?
                      AND b.tipo_registro IN ('completada', 'actualizacion')
                    GROUP BY b.proyecto_id
                ) logros ON logros.proyecto_id = p.id";
        }

        if ($tieneTablaRiesgos) {
            $sql .= "
                LEFT JOIN (
                    SELECT
                        r.proyecto_id,
                        SUM(CASE WHEN r.estado IN ('abierto', 'en_seguimiento') THEN 1 ELSE 0 END) AS riesgos_abiertos,
                        SUBSTRING_INDEX(GROUP_CONCAT(CASE WHEN r.estado IN ('abierto', 'en_seguimiento') THEN TRIM(r.descripcion) END ORDER BY r.fecha_creacion DESC SEPARATOR ' | '), ' | ', 3) AS riesgos_pendientes
                    FROM proyecto_riesgos r
                    GROUP BY r.proyecto_id
                ) riesgos ON riesgos.proyecto_id = p.id";
        } else {
            $sql .= "
                LEFT JOIN (
                    SELECT
                        b.proyecto_id,
                        COUNT(*) AS riesgos_abiertos,
                        SUBSTRING_INDEX(GROUP_CONCAT(TRIM(b.descripcion) ORDER BY b.fecha_registro DESC SEPARATOR ' | '), ' | ', 3) AS riesgos_pendientes
                    FROM bitacora b
                                        WHERE YEAR(b.fecha_registro) = ?
                      AND LOWER(b.descripcion) LIKE '%riesgo%'
                    GROUP BY b.proyecto_id
                ) riesgos ON riesgos.proyecto_id = p.id";
        }

        $sql .= "
                WHERE 1 = 1";

        $params = [(int)$anio, (int)$anio];
        $types = "ii";

        if (!$tieneTablaRiesgos) {
            $params[] = (int)$anio;
            $types .= "i";
        }

        if ($usuario_id !== null) {
            $sql .= " AND (p.usuario_id = ? OR p.usuario_id IS NULL)";
            $params[] = (int)$usuario_id;
            $types .= "i";
        }

        if ($categoria !== '') {
            $sql .= " AND p.categoria = ?";
            $params[] = $categoria;
            $types .= "s";
        }

        if ($estado !== '') {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
            $types .= "s";
        }

        $sql .= " ORDER BY p.estado ASC, avance_proyecto DESC, p.nombre ASC";

        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            return [];
        }

        if (!$stmt->bind_param($types, ...$params)) {
            $stmt->close();
            return [];
        }

        if (!$stmt->execute()) {
            $stmt->close();
            return [];
        }

        $result = $stmt->get_result();
        if ($result === false) {
            $stmt->close();
            return [];
        }

        $data = [];
        while ($row = $result->fetch_object()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }
}
