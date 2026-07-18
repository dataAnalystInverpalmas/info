<?php
namespace App\Models;

use App\Helpers\Database;

class ProyectoLogro {

    public static function getByProyecto($proyecto_id, $usuario_id = null) {
        $conexion = Database::getConnection();
        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "SELECT l.*
                 FROM proyecto_logros l
                 INNER JOIN proyectos p ON p.id = l.proyecto_id
                 WHERE l.proyecto_id = ?
                   AND (p.usuario_id = ? OR p.usuario_id IS NULL)
                 ORDER BY COALESCE(l.fecha_logro, l.fecha_creacion) DESC, l.id DESC"
            );
            if (!$stmt) return [];
            $stmt->bind_param("ii", $proyecto_id, $usuario_id);
        } else {
            $stmt = $conexion->prepare(
                "SELECT l.*
                 FROM proyecto_logros l
                 WHERE l.proyecto_id = ?
                 ORDER BY COALESCE(l.fecha_logro, l.fecha_creacion) DESC, l.id DESC"
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
                "SELECT l.*
                 FROM proyecto_logros l
                 INNER JOIN proyectos p ON p.id = l.proyecto_id
                 WHERE l.id = ?
                   AND (p.usuario_id = ? OR p.usuario_id IS NULL)
                 LIMIT 1"
            );
            if (!$stmt) return null;
            $stmt->bind_param("ii", $id, $usuario_id);
        } else {
            $stmt = $conexion->prepare("SELECT * FROM proyecto_logros WHERE id = ? LIMIT 1");
            if (!$stmt) return null;
            $stmt->bind_param("i", $id);
        }

        $stmt->execute();
        return $stmt->get_result()->fetch_object();
    }

    public static function create($proyecto_id, $descripcion, $impacto, $fecha_logro, $estado, $autor, $usuario_id = null) {
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
            "INSERT INTO proyecto_logros (proyecto_id, descripcion, impacto, fecha_logro, estado, autor)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $stmt->bind_param("isssss", $proyecto_id, $descripcion, $impacto, $fecha_logro, $estado, $autor);
        $ok = $stmt->execute();

        return ['success' => $ok, 'mensaje' => $ok ? 'Logro creado' : $conexion->error, 'id' => $conexion->insert_id];
    }

    public static function update($id, $descripcion, $impacto, $fecha_logro, $estado, $autor, $usuario_id = null) {
        $conexion = Database::getConnection();

        if ($usuario_id !== null) {
            $v = $conexion->prepare(
                "SELECT l.id
                 FROM proyecto_logros l
                 INNER JOIN proyectos p ON p.id = l.proyecto_id
                 WHERE l.id = ? AND (p.usuario_id = ? OR p.usuario_id IS NULL)
                 LIMIT 1"
            );
            if (!$v) return ['success' => false, 'mensaje' => 'Error de consulta'];
            $v->bind_param("ii", $id, $usuario_id);
            $v->execute();
            if (!$v->get_result()->fetch_assoc()) {
                return ['success' => false, 'mensaje' => 'Logro no autorizado'];
            }
        }

        $stmt = $conexion->prepare(
            "UPDATE proyecto_logros
             SET descripcion = ?, impacto = ?, fecha_logro = ?, estado = ?, autor = ?
             WHERE id = ?"
        );
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $stmt->bind_param("sssssi", $descripcion, $impacto, $fecha_logro, $estado, $autor, $id);
        $ok = $stmt->execute();

        return ['success' => $ok, 'mensaje' => $ok ? 'Logro actualizado' : $conexion->error];
    }

    public static function delete($id, $usuario_id = null) {
        $conexion = Database::getConnection();

        if ($usuario_id !== null) {
            $stmt = $conexion->prepare(
                "DELETE l FROM proyecto_logros l
                 INNER JOIN proyectos p ON p.id = l.proyecto_id
                 WHERE l.id = ?
                   AND (p.usuario_id = ? OR p.usuario_id IS NULL)"
            );
            if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
            $stmt->bind_param("ii", $id, $usuario_id);
        } else {
            $stmt = $conexion->prepare("DELETE FROM proyecto_logros WHERE id = ?");
            if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
            $stmt->bind_param("i", $id);
        }

        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Logro eliminado' : $conexion->error];
    }
}
