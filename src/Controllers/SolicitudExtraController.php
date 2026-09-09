<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Models\SolicitudExtra;

class SolicitudExtraController {
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public function index() {
        $usuario_id = $_SESSION['id'] ?? null;
        $solicitudes = SolicitudExtra::getAll($usuario_id);
        require __DIR__ . '/../Views/SolicitudesExtra/index.php';
    }

    public static function crear() {
        $input = self::input();
        $datos = self::normalizar($input);
        if ($datos['descripcion'] === '') {
            return ['success' => false, 'mensaje' => 'La descripción es requerida'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        if (!$usuario_id) {
            return ['success' => false, 'mensaje' => 'Sesión no válida. Inicia sesión nuevamente.'];
        }

        $conexion = Database::getConnection();
        $stmt = $conexion->prepare('INSERT INTO solicitudes_extra (usuario_id, fecha, solicitante, area, descripcion, tipo, tiempo_invertido_horas, responsable, estado, se_convirtio_en_proyecto, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        if (!$stmt) {
            return ['success' => false, 'mensaje' => $conexion->error ?: 'Error de consulta'];
        }
        $stmt->bind_param('isssssdssis', $usuario_id, $datos['fecha'], $datos['solicitante'], $datos['area'], $datos['descripcion'], $datos['tipo'], $datos['tiempo_invertido_horas'], $datos['responsable'], $datos['estado'], $datos['se_convirtio_en_proyecto'], $datos['observaciones']);
        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Solicitud creada' : $stmt->error, 'id' => $conexion->insert_id];
    }

    public static function actualizar(int $id) {
        $input = self::input();
        $datos = self::normalizar($input);
        if ($datos['descripcion'] === '') {
            return ['success' => false, 'mensaje' => 'La descripción es requerida'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        if (!$usuario_id) {
            return ['success' => false, 'mensaje' => 'Sesión no válida. Inicia sesión nuevamente.'];
        }

        $conexion = Database::getConnection();
        $stmt = $conexion->prepare('UPDATE solicitudes_extra SET fecha=?, solicitante=?, area=?, descripcion=?, tipo=?, tiempo_invertido_horas=?, responsable=?, estado=?, se_convirtio_en_proyecto=?, observaciones=? WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)');
        if (!$stmt) {
            return ['success' => false, 'mensaje' => $conexion->error ?: 'Error de consulta'];
        }
        $stmt->bind_param('sssssdssisii', $datos['fecha'], $datos['solicitante'], $datos['area'], $datos['descripcion'], $datos['tipo'], $datos['tiempo_invertido_horas'], $datos['responsable'], $datos['estado'], $datos['se_convirtio_en_proyecto'], $datos['observaciones'], $id, $usuario_id);
        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Solicitud actualizada' : $stmt->error];
    }

    public static function eliminar(int $id) {
        $usuario_id = $_SESSION['id'] ?? null;
        if (!$usuario_id) {
            return ['success' => false, 'mensaje' => 'Sesión no válida. Inicia sesión nuevamente.'];
        }

        $conexion = Database::getConnection();
        $stmt = $conexion->prepare('DELETE FROM solicitudes_extra WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)');
        if (!$stmt) {
            return ['success' => false, 'mensaje' => $conexion->error ?: 'Error de consulta'];
        }
        $stmt->bind_param('ii', $id, $usuario_id);
        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Solicitud eliminada' : $stmt->error];
    }

    public static function convertirEnTarea(int $id) {
        $usuario_id = $_SESSION['id'] ?? null;
        if (!$usuario_id) {
            return ['success' => false, 'mensaje' => 'Sesión no válida. Inicia sesión nuevamente.'];
        }

        $conexion = Database::getConnection();
        $columnas = $conexion->query("SHOW COLUMNS FROM tareas LIKE 'solicitud_id'");
        if (!$columnas || $columnas->num_rows === 0) {
            return ['success' => false, 'mensaje' => 'Ejecuta la migración de solicitudes antes de convertir registros.'];
        }

        $stmt = $conexion->prepare('SELECT * FROM solicitudes_extra WHERE id=? AND (usuario_id=? OR usuario_id IS NULL) LIMIT 1');
        $stmt->bind_param('ii', $id, $usuario_id);
        $stmt->execute();
        $solicitud = $stmt->get_result()->fetch_assoc();
        if (!$solicitud) {
            return ['success' => false, 'mensaje' => 'Solicitud no encontrada'];
        }

        $stmt = $conexion->prepare('SELECT id FROM tareas WHERE solicitud_id=? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        if ($tarea = $stmt->get_result()->fetch_assoc()) {
            return ['success' => true, 'mensaje' => 'La solicitud ya tiene una tarea', 'tarea_id' => (int)$tarea['id']];
        }

        $fecha_vencimiento = trim((string)($_POST['fecha_vencimiento'] ?? ''));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_vencimiento)) {
            return ['success' => false, 'mensaje' => 'La fecha de vencimiento es requerida'];
        }
        $fecha_inicio = $solicitud['fecha'] ?: date('Y-m-d');
        $nombre = mb_substr(trim($solicitud['descripcion']), 0, 255);
        $tipo = 'imprevista';
        $estado = in_array($solicitud['estado'], ['pendiente', 'en_progreso', 'completada', 'cancelada'], true) ? $solicitud['estado'] : 'pendiente';
        $avance = $estado === 'completada' ? 100 : ($estado === 'en_progreso' ? 25 : 0);
        $prioridad = 'media';
        $responsable = $solicitud['responsable'] ?: null;
        $quien_solicita = $solicitud['solicitante'] ?: null;
        $tieneQuienSolicita = $conexion->query("SHOW COLUMNS FROM tareas LIKE 'quien_solicita'")->num_rows > 0;

        $conexion->begin_transaction();
        try {
            $sql = $tieneQuienSolicita
                ? 'INSERT INTO tareas (usuario_id, solicitud_id, nombre, tipo, descripcion, responsable, quien_solicita, estado, porcentaje_avance, prioridad, fecha_inicio, fecha_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                : 'INSERT INTO tareas (usuario_id, solicitud_id, nombre, tipo, descripcion, responsable, estado, porcentaje_avance, prioridad, fecha_inicio, fecha_vencimiento) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt = $conexion->prepare($sql);
            if (!$stmt) throw new \RuntimeException($conexion->error ?: 'Error de consulta');
            if ($tieneQuienSolicita) {
                $stmt->bind_param('iissssssisss', $usuario_id, $id, $nombre, $tipo, $solicitud['descripcion'], $responsable, $quien_solicita, $estado, $avance, $prioridad, $fecha_inicio, $fecha_vencimiento);
            } else {
                $stmt->bind_param('iisssssisss', $usuario_id, $id, $nombre, $tipo, $solicitud['descripcion'], $responsable, $estado, $avance, $prioridad, $fecha_inicio, $fecha_vencimiento);
            }
            if (!$stmt->execute()) throw new \RuntimeException($stmt->error ?: 'No se pudo crear la tarea');
            $tarea_id = $conexion->insert_id;

            $stmt = $conexion->prepare("UPDATE solicitudes_extra SET observaciones=CONCAT(COALESCE(observaciones, ''), ?) WHERE id=?");
            $nota = ' Convertida en tarea Kanban #' . $tarea_id . '.';
            $stmt->bind_param('si', $nota, $id);
            $stmt->execute();
            $conexion->commit();
            return ['success' => true, 'mensaje' => 'Solicitud convertida en tarea', 'tarea_id' => $tarea_id];
        } catch (\Throwable $exception) {
            $conexion->rollback();
            return ['success' => false, 'mensaje' => $exception->getMessage()];
        }
    }

    public static function handleRequest(array $server, array $query): void {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $metodo = $server['REQUEST_METHOD'] ?? 'GET';
            $id = (int)($query['id'] ?? 0);
            $usuario_id = $_SESSION['id'] ?? null;
            if ($metodo === 'GET') {
                echo json_encode($id ? SolicitudExtra::getById($id, $usuario_id) : SolicitudExtra::getAll($usuario_id), self::JSON_FLAGS);
                return;
            }
            if ($metodo === 'POST') {
                if (($query['accion'] ?? '') === 'convertir' && $id > 0) {
                    echo json_encode(self::convertirEnTarea($id), self::JSON_FLAGS);
                    return;
                }
                echo json_encode(self::crear(), self::JSON_FLAGS);
                return;
            }
            if ($metodo === 'PUT' && $id > 0) {
                echo json_encode(self::actualizar($id), self::JSON_FLAGS);
                return;
            }
            if ($metodo === 'DELETE' && $id > 0) {
                echo json_encode(self::eliminar($id), self::JSON_FLAGS);
                return;
            }
            http_response_code(405);
            echo json_encode(['success' => false, 'mensaje' => 'Método o identificador inválido'], self::JSON_FLAGS);
        } catch (\Throwable $exception) {
            http_response_code(500);
            echo json_encode(['success' => false, 'mensaje' => $exception->getMessage()], self::JSON_FLAGS);
        }
    }

    private static function input(): array {
        return json_decode(file_get_contents('php://input'), true) ?? $_POST;
    }

    private static function normalizar(array $input): array {
        $estado = $input['estado'] ?? 'pendiente';
        if (!in_array($estado, ['pendiente', 'en_progreso', 'completada', 'cancelada'], true)) {
            $estado = 'pendiente';
        }
        $horas = $input['tiempo_invertido_horas'] ?? null;
        return [
            'fecha' => self::nulo($input['fecha'] ?? null),
            'solicitante' => self::nulo($input['solicitante'] ?? null),
            'area' => self::nulo($input['area'] ?? null),
            'descripcion' => trim((string)($input['descripcion'] ?? '')),
            'tipo' => self::nulo($input['tipo'] ?? null),
            'tiempo_invertido_horas' => $horas === '' || $horas === null ? null : max(0, (float)$horas),
            'responsable' => self::nulo($input['responsable'] ?? null),
            'estado' => $estado,
            'se_convirtio_en_proyecto' => !empty($input['se_convirtio_en_proyecto']) ? 1 : 0,
            'observaciones' => self::nulo($input['observaciones'] ?? null)
        ];
    }

    private static function nulo($valor) {
        $valor = trim((string)$valor);
        return $valor === '' ? null : $valor;
    }
}
