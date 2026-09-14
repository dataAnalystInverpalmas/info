<?php
declare(strict_types=1);

/**
 * Descarga ajustes_proyeccion.xlsx filtrado por finca, generado al vuelo
 * desde la tabla ld_ajustes_proyeccion (llenada por el job planos_proyeccion
 * de la app FastAPI). Cada finca descarga solo sus propias filas.
 *
 * GET /api/v1/ajustes_proyeccion_download.php?finca=002
 * Auth: header "Authorization: Bearer <token>", o ?api_key=<token>
 * (el fallback por query param existe para poder abrir el link
 * directamente en el navegador o desde una macro sin construir headers).
 *
 * Columnas del archivo: ID, Factor, Mueve semanas, Vigencia, Bloqueado
 * (sin "finca" -- solo se usa para filtrar).
 */

define('API_DIR', dirname(__DIR__));
define('APP_DIR', dirname(__DIR__, 2));

require_once API_DIR . '/Response.php';
require_once API_DIR . '/Auth.php';
require_once APP_DIR . '/funciones/conexion.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method Not Allowed', 405);
}

if (!isset($conexion) || $conexion->connect_errno) {
    Response::error('Database connection failed', 503);
}

Auth::verify();

const FINCAS_VALIDAS = ['001', '002'];

$finca = isset($_GET['finca']) ? trim((string)$_GET['finca']) : '';
if (!in_array($finca, FINCAS_VALIDAS, true)) {
    Response::error("Parametro 'finca' invalido: debe ser una de " . implode(', ', FINCAS_VALIDAS), 400);
}

$stmt = $conexion->prepare(
    'SELECT id_lote_siembra, factor, mueve_semanas, vigencia, bloqueado
     FROM ld_ajustes_proyeccion
     WHERE finca = ?
     ORDER BY id_lote_siembra'
);
if (!$stmt) {
    Response::error('Error preparando consulta: ' . $conexion->error, 500);
}
$stmt->bind_param('s', $finca);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_row()) {
    $rows[] = $row;
}
$stmt->close();

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('ajustes');

$headers = ['ID', 'Factor', 'Mueve semanas', 'Vigencia', 'Bloqueado'];
$sheet->fromArray($headers, null, 'A1', true);

// strictNullComparison=true: sin esto, fromArray compara con "!=" y en PHP
// "0 == null" es true, asi que valores en 0 (mueve_semanas, bloqueado)
// quedaban en blanco en vez de escribirse como 0.
$sheet->fromArray($rows, null, 'A2', true);

$filename = 'ajustes_proyeccion_' . $finca . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save('php://output');
exit;
