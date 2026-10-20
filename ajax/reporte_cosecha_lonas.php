<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/funciones/conexion.php';

use App\Controllers\ReporteLonasController;

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$params = $method === 'POST' ? $_POST : $_GET;
$accion = $params['accion'] ?? 'datos';

$respuesta = $accion === 'opciones'
    ? ReporteLonasController::opciones($params)
    : ReporteLonasController::datos($params);

echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
