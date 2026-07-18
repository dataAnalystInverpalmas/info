<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';
require_once dirname(__DIR__) . '/bd/conexion.php';

use App\Controllers\ResumenEjecutivoMensualController;

header('Content-Type: application/json; charset=utf-8');

try {
    $metodo = $_SERVER['REQUEST_METHOD'];
    if ($metodo !== 'GET') {
        echo json_encode(['success' => false, 'mensaje' => 'Metodo no permitido']);
        exit;
    }

    echo json_encode(ResumenEjecutivoMensualController::listar());
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'mensaje' => $e->getMessage()]);
}
