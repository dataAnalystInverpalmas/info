<?php
namespace App\Models;

use App\Helpers\Database;

class ProyectoRiesgo {

    public static function getByProyecto($proyecto_id, $usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT r.*
                 FROM proyecto_riesgos r
                 INNER JOIN proyectos p ON p.id = r.proyecto_id
                 WHERE r.proyecto_id = ?
                   AND (p.usuario_id = ? OR p.usuario_id IS NULL)
                 ORDER BY r.fecha_creacion DESC, r.id DESC"
            );
            if (!$stmt) return [];
            $stmt->bind_param("ii", $proyecto_id, $usuario_id);
        } else {
            $stmt = $conexion->prepare(
                "SELECT r.*
                 FROM proyecto_riesgos r
                 WHERE r.proyecto_id = ?
                 ORDER BY r.fecha_creacion DESC, r.id DESC"
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

    public static function getById($id, $usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT r.*
                 FROM proyecto_riesgos r
                 INNER JOIN proyectos p ON p.id = r.proyecto_id
                 WHERE r.id = ?
                   AND (p.usuario_id = ? OR p.usuario_id IS NULL)
                 LIMIT 1"
            );
            if (!$stmt) return null;
            $stmt->bind_param("ii", $id, $usuario_id);
        } else {
            $stmt = $conexion->prepare("SELECT * FROM proyecto_riesgos WHERE id = ? LIMIT 1");
            if (!$stmt) return null;
            $stmt->bind_param("i", $id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }

    public static function create($proyecto_id, $descripcion, $probabilidad, $impacto, $estado, $responsable, $plan_mitigacion, $fecha_compromiso, $usuario_id = null) {
        $conexion = Database::getConnection();

        if ($usuario_id !== null) {
            $v = $conexion->prepare("SELECT id FROM proyectos WHERE id = ? AND (usuario_id = ? OR usuario_id IS NULL) LIMIT 1");
            if (!$v) return ['success' => false, 'mensaje' => 'Error de consulta'];
            $v->bind_param("ii", $proyecto_id, $usuario_id);
            $v->execute();
            if (!$v->get_result()->fetch_assoc()) {
                return ['success' => false, 'mensaje' => 'Proyecto no autorizado'];
            }
        }

        $stmt = $conexion->prepare(
            "INSERT INTO proyecto_riesgos
                (proyecto_id, descripcion, probabilidad, impacto, estado, responsable, plan_mitigacion, fecha_compromiso)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $stmt->bind_param("isssssss", $proyecto_id, $descripcion, $probabilidad, $impacto, $estado, $responsable, $plan_mitigacion, $fecha_compromiso);
        $ok = $stmt->execute();

        return ['success' => $ok, 'mensaje' => $ok ? 'Riesgo creado' : $conexion->error, 'id' => $conexion->insert_id];
    }

    public static function update($id, $descripcion, $probabilidad, $impacto, $estado, $responsable, $plan_mitigacion, $fecha_compromiso, $usuario_id = null) {
        $conexion = Database::getConnection();

        if ($usuario_id !== null) {
            $v = $conexion->prepare(
                "SELECT r.id
                 FROM proyecto_riesgos r
                 INNER JOIN proyectos p ON p.id = r.proyecto_id
                 WHERE r.id = ? AND (p.usuario_id = ? OR p.usuario_id IS NULL)
                 LIMIT 1"
            );
            if (!$v) return ['success' => false, 'mensaje' => 'Error de consulta'];
            $v->bind_param("ii", $id, $usuario_id);
            $v->execute();
            if (!$v->get_result()->fetch_assoc()) {
                return ['success' => false, 'mensaje' => 'Riesgo no autorizado'];
            }
        }

        $stmt = $conexion->prepare(
            "UPDATE proyecto_riesgos
             SET descripcion = ?, probabilidad = ?, impacto = ?, estado = ?, responsable = ?, plan_mitigacion = ?, fecha_compromiso = ?
             WHERE id = ?"
        );
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $stmt->bind_param("sssssssi", $descripcion, $probabilidad, $impacto, $estado, $responsable, $plan_mitigacion, $fecha_compromiso, $id);
        $ok = $stmt->execute();

        return ['success' => $ok, 'mensaje' => $ok ? 'Riesgo actualizado' : $conexion->error];
    }

    public static function delete($id, $usuario_id = null) {
        $conexion = Database::getConnection();

        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "DELETE r FROM proyecto_riesgos r
                 INNER JOIN proyectos p ON p.id = r.proyecto_id
                 WHERE r.id = ?
                   AND (p.usuario_id = ? OR p.usuario_id IS NULL)"
            );
            if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
            $stmt->bind_param("ii", $id, $usuario_id);
        } else {
            $stmt = $conexion->prepare("DELETE FROM proyecto_riesgos WHERE id = ?");
            if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
            $stmt->bind_param("i", $id);
        }

        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Riesgo eliminado' : $conexion->error];
    }
}
