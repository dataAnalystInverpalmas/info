<?php
namespace App\Controllers;

use App\Models\Bitacora;

class BitacoraController {

    public function index() {
        $usuario_id = $_SESSION['id'] ?? null;
        $registros = Bitacora::getAll($usuario_id);
        extract(['registros' => $registros]);
        require_once __DIR__ . '/../Views/Bitacora/index.php';
    }

    // ── Métodos estáticos para ajax/bitacora.php ───────────────────────────

    public static function obtenerHistorialTarea($tarea_id) {
        $usuario_id = $_SESSION['id'] ?? null;
        return Bitacora::getByTarea((int)$tarea_id, $usuario_id);
    }

    public static function obtenerPorTarea($tarea_id) {
        $usuario_id = $_SESSION['id'] ?? null;
        return Bitacora::getByTarea((int)$tarea_id, $usuario_id);
    }

    public static function listar() {
        $usuario_id = $_SESSION['id'] ?? null;
        return Bitacora::getAll($usuario_id);
    }

    public static function obtener($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        $stmt = $conexion->prepare(
            "SELECT b.*, t.nombre AS tarea_nombre, p.nombre AS proyecto_nombre
             FROM bitacora b
             LEFT JOIN tareas t ON b.tarea_id = t.id
             LEFT JOIN proyectos p ON COALESCE(b.proyecto_id, t.proyecto_id) = p.id
             WHERE b.id = ? AND COALESCE(b.usuario_id, t.usuario_id) = ?
             LIMIT 1"
        );
        if (!$stmt) return ['error' => 'Error de consulta'];
        $stmt->bind_param("ii", $id, $usuario_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_object();
        return $row ?: ['error' => 'No encontrado'];
    }

    public static function obtenerReporte() {
        $usuario_id = $_SESSION['id'] ?? null;
        return Bitacora::getAll($usuario_id);
    }

    public static function crear() {
        return ['error' => 'Usar ajax/bitacora_crud.php para crear registros'];
    }

    public static function actualizar($id) {
        return ['error' => 'Operación no soportada'];
    }

    public static function eliminar($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        // Solo permite eliminar si la entrada pertenece al usuario (via tarea)
        $stmt = $conexion->prepare(
            "DELETE b FROM bitacora b
             LEFT JOIN tareas t ON b.tarea_id = t.id
             WHERE b.id = ? AND COALESCE(b.usuario_id, t.usuario_id) = ?"
        );
        if (!$stmt) return ['success' => false, 'error' => 'Error de consulta'];
        $stmt->bind_param("ii", $id, $usuario_id);
        $ok = $stmt->execute();
        return ['success' => $ok];
    }
}
