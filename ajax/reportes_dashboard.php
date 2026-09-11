<?php
/**
 * Endpoint AJAX del Reporte Sembrado.
 * Consulta de siembras activas de la tabla `plane`, cruzando color (ld_variedades + ld_colores)
 * y semana yyww ISO (tabla `dates`).
 */

require_once dirname(__DIR__) . '/funciones/conexion.php';

use App\Controllers\ReportesDashboardController;

header('Content-Type: application/json; charset=utf-8');

$accion = $_GET['accion'] ?? 'datos';
$respuesta = $accion === 'opciones'
    ? ReportesDashboardController::opciones($_GET)
    : ReportesDashboardController::datos($_GET);

echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
