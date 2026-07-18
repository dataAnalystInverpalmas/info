<?php
namespace App\Controllers;

use App\Models\Proyecto;

class ProyectoController {
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public function index() {
        $usuario_id = $_SESSION['id'] ?? null;
        $proyectos = Proyecto::getAll($usuario_id);
        $categorias = Proyecto::getCategorias($usuario_id);
        extract(['proyectos' => $proyectos, 'categorias' => $categorias]);
        require_once __DIR__ . '/../Views/Proyectos/index.php';
    }

    // ── Métodos estáticos para AJAX ───────────────────────────

    public static function listar() {
        $usuario_id = $_SESSION['id'] ?? null;
        return Proyecto::getAll($usuario_id);
    }

    public static function obtener($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        return Proyecto::getById($id, $usuario_id);
    }

    public static function estadisticas($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        $stats = Proyecto::getEstadisticas((int)$id, $usuario_id);
        if (!$stats) {
            return ['success' => false, 'mensaje' => 'Proyecto no encontrado'];
        }
        return ['success' => true, 'data' => $stats];
    }

    public static function crear() {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $nombre = trim($input['nombre'] ?? '');
        $descripcion = trim($input['descripcion'] ?? '');
        $categoria = trim($input['categoria'] ?? '');
        $fecha_inicio = $input['fecha_inicio'] ?? null;
        $fecha_fin = $input['fecha_fin'] ?? null;

        if (empty($nombre)) {
            return ['success' => false, 'mensaje' => 'Nombre requerido'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        $stmt = $conexion->prepare("INSERT INTO proyectos (nombre, descripcion, categoria, fecha_inicio, fecha_fin, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $stmt->bind_param("sssssi", $nombre, $descripcion, $categoria, $fecha_inicio, $fecha_fin, $usuario_id);
        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Proyecto creado' : $conexion->error, 'id' => $conexion->insert_id];
    }

    public static function actualizar($id) {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $nombre = trim($input['nombre'] ?? '');
        $descripcion = trim($input['descripcion'] ?? '');
        $categoria = trim($input['categoria'] ?? '');
        $fecha_inicio = $input['fecha_inicio'] ?? null;
        $fecha_fin = $input['fecha_fin'] ?? null;
        $estado = $input['estado'] ?? 'activo';

        if (empty($nombre)) {
            return ['success' => false, 'mensaje' => 'Nombre requerido'];
        }

        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        $stmt = $conexion->prepare("UPDATE proyectos SET nombre=?, descripcion=?, categoria=?, fecha_inicio=?, fecha_fin=?, estado=? WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $stmt->bind_param("ssssssii", $nombre, $descripcion, $categoria, $fecha_inicio, $fecha_fin, $estado, $id, $usuario_id);
        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Proyecto actualizado' : $conexion->error];
    }

    public static function eliminar($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        $conexion = \App\Helpers\Database::getConnection();
        $stmt = $conexion->prepare("DELETE FROM proyectos WHERE id=? AND (usuario_id=? OR usuario_id IS NULL)");
        if (!$stmt) return ['success' => false, 'mensaje' => 'Error de consulta'];
        $stmt->bind_param("ii", $id, $usuario_id);
        $ok = $stmt->execute();
        return ['success' => $ok, 'mensaje' => $ok ? 'Proyecto eliminado' : $conexion->error];
    }

    public static function handleRequest(array $server, array $query): void {
        self::sendJsonHeader();

        try {
            $metodo = $server['REQUEST_METHOD'] ?? 'GET';
            $accion = $query['accion'] ?? null;
            $id = $query['id'] ?? null;

            switch ($metodo) {
                case 'GET':
                    if ($accion === 'estadisticas' && $id) {
                        self::respond(self::estadisticas($id));
                        return;
                    }

                    self::respond($id ? self::obtener($id) : self::listar());
                    return;

                case 'POST':
                    self::respond(self::crear());
                    return;

                case 'PUT':
                    if (!$id) {
                        self::respond(['error' => 'ID es requerido']);
                        return;
                    }

                    self::respond(self::actualizar($id));
                    return;

                case 'DELETE':
                    if (!$id) {
                        self::respond(['error' => 'ID es requerido']);
                        return;
                    }

                    self::respond(self::eliminar($id));
                    return;

                default:
                    self::respond(['error' => 'Método no permitido']);
                    return;
            }
        } catch (\Throwable $exception) {
            http_response_code(500);
            self::respond(['error' => $exception->getMessage()]);
        }
    }

    public static function handleLegacyCrudRequest(array $post): void {
        self::sendJsonHeader();

        $accion = $post['accion'] ?? '';

        switch ($accion) {
            case 'create':
                self::respond(self::crear());
                return;

            case 'update':
                $id = (int)($post['id'] ?? 0);
                if ($id <= 0) {
                    self::respond(['success' => false, 'mensaje' => 'ID inválido']);
                    return;
                }

                self::respond(self::actualizar($id));
                return;

            case 'delete':
                $id = (int)($post['id'] ?? 0);
                if ($id <= 0) {
                    self::respond(['success' => false, 'mensaje' => 'ID inválido']);
                    return;
                }

                self::respond(self::eliminar($id));
                return;

            default:
                self::respond(['success' => false, 'mensaje' => 'Acción inválida']);
                return;
        }
    }

    private static function sendJsonHeader(): void {
        header('Content-Type: application/json; charset=utf-8');
    }

    private static function respond($payload): void {
        echo json_encode($payload, self::JSON_FLAGS);
    }
}
