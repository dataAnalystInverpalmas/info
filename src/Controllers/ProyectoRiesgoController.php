<?php
namespace App\Controllers;

use App\Models\ProyectoRiesgo;

class ProyectoRiesgoController {
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public static function listar($proyecto_id) {
        $usuario_id = $_SESSION['id'] ?? null;
        return ProyectoRiesgo::getByProyecto((int)$proyecto_id, $usuario_id);
    }

    public static function obtener($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        return ProyectoRiesgo::getById((int)$id, $usuario_id);
    }

    public static function crear() {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $proyecto_id = (int)($input['proyecto_id'] ?? 0);
        $descripcion = trim((string)($input['descripcion'] ?? ''));
        $probabilidad = $input['probabilidad'] ?? 'media';
        $impacto = $input['impacto'] ?? 'medio';
        $estado = $input['estado'] ?? 'abierto';
        $responsable = trim((string)($input['responsable'] ?? ''));
        $plan_mitigacion = trim((string)($input['plan_mitigacion'] ?? ''));
        $fecha_compromiso = $input['fecha_compromiso'] ?? null;

        if ($proyecto_id <= 0 || $descripcion === '') {
            return ['success' => false, 'mensaje' => 'Proyecto y descripcion son requeridos'];
        }

        $probabilidades = ['baja', 'media', 'alta', 'muy_alta'];
        if (!in_array($probabilidad, $probabilidades, true)) {
            $probabilidad = 'media';
        }

        $impactos = ['bajo', 'medio', 'alto', 'critico'];
        if (!in_array($impacto, $impactos, true)) {
            $impacto = 'medio';
        }

        $estados = ['abierto', 'en_seguimiento', 'mitigado', 'cerrado'];
        if (!in_array($estado, $estados, true)) {
            $estado = 'abierto';
        }

        $usuario_id = $_SESSION['id'] ?? null;
        return ProyectoRiesgo::create($proyecto_id, $descripcion, $probabilidad, $impacto, $estado, $responsable, $plan_mitigacion, $fecha_compromiso, $usuario_id);
    }

    public static function actualizar($id) {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $descripcion = trim((string)($input['descripcion'] ?? ''));
        $probabilidad = $input['probabilidad'] ?? 'media';
        $impacto = $input['impacto'] ?? 'medio';
        $estado = $input['estado'] ?? 'abierto';
        $responsable = trim((string)($input['responsable'] ?? ''));
        $plan_mitigacion = trim((string)($input['plan_mitigacion'] ?? ''));
        $fecha_compromiso = $input['fecha_compromiso'] ?? null;

        if ($descripcion === '') {
            return ['success' => false, 'mensaje' => 'Descripcion requerida'];
        }

        $probabilidades = ['baja', 'media', 'alta', 'muy_alta'];
        if (!in_array($probabilidad, $probabilidades, true)) {
            $probabilidad = 'media';
        }

        $impactos = ['bajo', 'medio', 'alto', 'critico'];
        if (!in_array($impacto, $impactos, true)) {
            $impacto = 'medio';
        }

        $estados = ['abierto', 'en_seguimiento', 'mitigado', 'cerrado'];
        if (!in_array($estado, $estados, true)) {
            $estado = 'abierto';
        }

        $usuario_id = $_SESSION['id'] ?? null;
        return ProyectoRiesgo::update((int)$id, $descripcion, $probabilidad, $impacto, $estado, $responsable, $plan_mitigacion, $fecha_compromiso, $usuario_id);
    }

    public static function eliminar($id) {
        $usuario_id = $_SESSION['id'] ?? null;
        return ProyectoRiesgo::delete((int)$id, $usuario_id);
    }

    public static function handleRequest(array $server, array $query): void {
        self::sendJsonHeader();

        try {
            $metodo = $server['REQUEST_METHOD'] ?? 'GET';
            $id = $query['id'] ?? null;
            $proyecto_id = $query['proyecto_id'] ?? null;

            switch ($metodo) {
                case 'GET':
                    if ($proyecto_id) {
                        self::respond(self::listar($proyecto_id));
                        return;
                    }
                    if ($id) {
                        self::respond(self::obtener($id));
                        return;
                    }

                    self::respond(['success' => false, 'mensaje' => 'proyecto_id o id es requerido']);
                    return;

                case 'POST':
                    self::respond(self::crear());
                    return;

                case 'PUT':
                    if (!$id) {
                        self::respond(['success' => false, 'mensaje' => 'id es requerido']);
                        return;
                    }

                    self::respond(self::actualizar($id));
                    return;

                case 'DELETE':
                    if (!$id) {
                        self::respond(['success' => false, 'mensaje' => 'id es requerido']);
                        return;
                    }

                    self::respond(self::eliminar($id));
                    return;

                default:
                    self::respond(['success' => false, 'mensaje' => 'Metodo no permitido']);
                    return;
            }
        } catch (\Throwable $exception) {
            http_response_code(500);
            self::respond(['success' => false, 'mensaje' => $exception->getMessage()]);
        }
    }

    private static function sendJsonHeader(): void {
        header('Content-Type: application/json; charset=utf-8');
    }

    private static function respond($payload): void {
        echo json_encode($payload, self::JSON_FLAGS);
    }
}
