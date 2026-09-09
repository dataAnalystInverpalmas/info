<?php
namespace App\Models;

use App\Helpers\Database;

class Tarea {

    private static $columnCache = [];

    private static function columnExists($table, $column) {
        $key = $table . '.' . $column;
        if (array_key_exists($key, self::$columnCache)) {
            return self::$columnCache[$key];
        }

        $conexion = Database::getConnection();
        $stmt = $conexion->prepare("SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
        if (!$stmt) {
            self::$columnCache[$key] = false;
            return false;
        }

        $stmt->bind_param("ss", $table, $column);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $result = ((int)($row['total'] ?? 0)) > 0;
        self::$columnCache[$key] = $result;
        return $result;
    }

    private static function ordenSelect($alias = 't') {
        return self::columnExists('tareas', 'orden_ejecucion') ? ", {$alias}.orden_ejecucion" : ', NULL AS orden_ejecucion';
    }

    private static function ordenBy($alias = 't') {
        return self::columnExists('tareas', 'orden_ejecucion')
            ? "ORDER BY {$alias}.proyecto_id ASC, {$alias}.orden_ejecucion ASC, {$alias}.fecha_creacion ASC, {$alias}.id ASC"
            : "ORDER BY {$alias}.proyecto_id ASC, {$alias}.fecha_creacion ASC, {$alias}.id ASC";
    }

    public static function getAll($usuario_id = null) {
        $conexion = Database::getConnection();
        $ordenSelect = self::ordenSelect();
        $ordenBy = self::ordenBy();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                        t.porcentaje_avance{$ordenSelect}, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 WHERE t.usuario_id = ?
                 {$ordenBy}"
            );
            if (!$stmt) {
                $stmt = $conexion->prepare(
                    "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                            t.porcentaje_avance, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                            t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                     FROM tareas t
                     LEFT JOIN proyectos p ON t.proyecto_id = p.id
                     WHERE t.usuario_id = ?
                     ORDER BY t.proyecto_id ASC, t.fecha_creacion ASC, t.id ASC"
                );
            }
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conexion->query(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                    t.porcentaje_avance{$ordenSelect}, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                    t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 {$ordenBy}"
            );
            if (!$result) {
                $result = $conexion->query(
                    "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                        t.porcentaje_avance, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                     FROM tareas t
                     LEFT JOIN proyectos p ON t.proyecto_id = p.id
                     ORDER BY t.proyecto_id ASC, t.fecha_creacion ASC, t.id ASC"
                );
            }
        }
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public static function getById($id, $usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare("SELECT * FROM tareas WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL) LIMIT 1");
            if (!$stmt) {
                return null;
            }
            $stmt->bind_param("ii", $id, $usuario_id);
        } else {
            $stmt = $conexion->prepare("SELECT * FROM tareas WHERE id = ? LIMIT 1");
            if (!$stmt) {
                return null;
            }
            $stmt->bind_param("i", $id);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }

    public static function getParaExportarExcel($usuario_id = null) {
        $conexion = Database::getConnection();
        $ordenSelect  = self::columnExists('tareas', 'orden_ejecucion')  ? ', t.orden_ejecucion'  : ', NULL AS orden_ejecucion';
        $cronoSelect  = self::columnExists('tareas', 'etapa_fase')
            ? ', t.etapa_fase, t.fecha_fin_real, t.entregable_concreto, t.evidencia_soporte, t.observaciones, t.dependencia'
            : ', NULL AS etapa_fase, NULL AS fecha_fin_real, NULL AS entregable_concreto, NULL AS evidencia_soporte, NULL AS observaciones, NULL AS dependencia';
        $sql = "SELECT t.id, t.nombre, t.descripcion, t.estado, t.porcentaje_avance,
                       t.fecha_inicio, t.fecha_vencimiento, t.fecha_actualizacion, t.responsable,
                       t.proyecto_id{$ordenSelect}{$cronoSelect}, p.nombre AS proyecto_nombre
                FROM tareas t
                LEFT JOIN proyectos p ON t.proyecto_id = p.id";

        if ($usuario_id !== null) {
            $stmt = $conexion->prepare($sql . ' WHERE (t.usuario_id = ? OR t.usuario_id IS NULL)
                                              ORDER BY t.proyecto_id ASC, t.orden_ejecucion ASC, t.fecha_creacion ASC, t.id ASC');
            if (!$stmt) return [];
            $stmt->bind_param('i', $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conexion->query($sql . ' ORDER BY t.proyecto_id ASC, t.orden_ejecucion ASC, t.fecha_creacion ASC, t.id ASC');
        }

        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public static function getPendientes($usuario_id = null) {
        $conexion = Database::getConnection();
        $ordenSelect = self::ordenSelect();
        $ordenBy = self::ordenBy();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                                                t.porcentaje_avance{$ordenSelect}, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 WHERE t.estado IN ('pendiente', 'en_progreso')
                   AND (t.usuario_id = ? OR t.usuario_id IS NULL)
                                                                 {$ordenBy}"
            );
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conexion->query(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                    t.porcentaje_avance{$ordenSelect}, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 WHERE t.estado IN ('pendiente', 'en_progreso')
                {$ordenBy}"
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

    public static function getImprevistas($usuario_id = null) {
        $conexion = Database::getConnection();
        $ordenSelect = self::ordenSelect();
        $ordenBy = self::ordenBy();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                                                t.porcentaje_avance{$ordenSelect}, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 WHERE t.tipo = 'imprevista'
                   AND (t.usuario_id = ? OR t.usuario_id IS NULL)
                                                                 {$ordenBy}"
            );
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $conexion->query(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                    t.porcentaje_avance{$ordenSelect}, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 WHERE t.tipo = 'imprevista'
                {$ordenBy}"
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

    public static function getProximas($usuario_id = null, $dias = 7) {
        $conexion = Database::getConnection();
        $ordenSelect = self::ordenSelect();
        $ordenBy = self::ordenBy();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                                                t.porcentaje_avance{$ordenSelect}, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 WHERE t.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                   AND t.estado <> 'cancelada'
                   AND (t.usuario_id = ? OR t.usuario_id IS NULL)
                                                                 {$ordenBy}"
            );
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param("ii", $dias, $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $stmt = $conexion->prepare(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                                                                                                t.porcentaje_avance{$ordenSelect}, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 WHERE t.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                   AND t.estado <> 'cancelada'
                                                                                                                                 {$ordenBy}"
            );
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param("i", $dias);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        $data = [];
        while ($row = $result->fetch_object()) {
            $data[] = $row;
        }
        return $data;
    }

    public static function getByProyecto($proyecto_id, $usuario_id = null) {
        $conexion = Database::getConnection();
        $ordenSelect = self::ordenSelect();
        $ordenBy = self::ordenBy();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                                                t.porcentaje_avance{$ordenSelect}, t.fecha_vencimiento, t.fecha_inicio, t.responsable, t.quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 WHERE t.proyecto_id = ?
                   AND (t.usuario_id = ? OR t.usuario_id IS NULL)
                                                                 {$ordenBy}"
            );
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param("ii", $proyecto_id, $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $stmt = $conexion->prepare(
                "SELECT t.id, t.nombre, t.tipo, t.descripcion, t.estado, t.prioridad,
                    t.porcentaje_avance{$ordenSelect}, t.fecha_vencimiento, t.fecha_inicio, t.responsable, NULL AS quien_solicita,
                        t.proyecto_id, p.nombre AS proyecto_nombre, p.categoria AS proyecto_categoria
                 FROM tareas t
                 LEFT JOIN proyectos p ON t.proyecto_id = p.id
                 WHERE t.proyecto_id = ?
                {$ordenBy}"
            );
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param("i", $proyecto_id);
            $stmt->execute();
            $result = $stmt->get_result();
        }

        $data = [];
        while ($row = $result->fetch_object()) {
            $data[] = $row;
        }
        return $data;
    }
}
