<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

use App\Controllers\PlanoReemplazoController;

header('Content-Type: application/json; charset=utf-8');

try {
    $metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $accion = $_GET['accion'] ?? $_POST['accion'] ?? ($metodo === 'POST' ? 'guardar' : 'listar');

    if ($metodo === 'GET') {
        if ($accion === 'catalogos') {
            echo json_encode(PlanoReemplazoController::catalogos());
            exit;
        }

        echo json_encode(PlanoReemplazoController::listar());
        exit;
    }

    if ($metodo === 'POST') {
        if ($accion !== 'guardar') {
            echo json_encode(['success' => false, 'message' => 'Acción no permitida']);
            exit;
        }

        echo json_encode(PlanoReemplazoController::guardar());
        exit;
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
