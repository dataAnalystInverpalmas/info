<?php
/**
 * Endpoint AJAX del reporte Teórico vs Real Siembras (viewsowing + seasons + dates).
 */

require_once dirname(__DIR__) . '/funciones/conexion.php';

use App\Controllers\ReporteComparativoController;

header('Content-Type: application/json; charset=utf-8');

$accion = $_GET['accion'] ?? 'datos';
$respuesta = $accion === 'opciones'
    ? ReporteComparativoController::opciones($_GET)
    : ReporteComparativoController::datos($_GET);

echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
