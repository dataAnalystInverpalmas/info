<?php
require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';

use App\Controllers\DashboardProyeccionesController;

header('Content-Type: application/json; charset=utf-8');

register_shutdown_function(function () {
    $error = error_get_last();
    if (!$error) {
        return;
    }

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: application/json; charset=utf-8');
    }

    echo json_encode([
        'ok' => false,
        'message' => 'Error fatal en dashboard_proyecciones',
        'error' => $error['message'],
    ]);
});

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'message' => 'Metodo no permitido']);
        exit;
    }

    echo json_encode(DashboardProyeccionesController::data($_GET));
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'message' => 'Error interno del servidor',
        'error' => $e->getMessage(),
    ]);
}
