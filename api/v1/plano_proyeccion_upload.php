<?php
declare(strict_types=1);

/**
 * Recibe el plano "BASE PLANO PROYECCION" de una finca (Palermo o Palmas)
 * subido desde una macro de Excel, y lo guarda en la tabla de staging
 * stg_plano_proyeccion. El job planos_proyeccion (app FastAPI) lee esa
 * tabla en vez de abrir el .xlsm directamente, asi ninguna finca depende
 * de sincronizar un archivo local hasta el servidor.
 *
 * No se borra el lote anterior de esa finca: se marca como
 * estado='historico' y el lote nuevo entra como estado='activo', para
 * conservar historico. El job de FastAPI solo lee estado='activo'.
 *
 * Contrato del body (JSON):
 * {
 *   "finca": "002",              // "001" (Palmas) o "002" (Palermo)
 *   "rows": [
 *     {
 *       "bloque": 1,
 *       "variedad": "2048",
 *       "cosecha": "SE2638",
 *       "nvari": "FARIDA",
 *       "fec_siem": "2026-03-11",   // YYYY-MM-DD
 *       "rev": 0,
 *       "amort": 1,
 *       "proyecta_quipus": 0.88
 *     },
 *     ...
 *   ]
 * }
 *
 * Respuesta: {"status":"ok","finca":"002","inserted":574}
 */

define('API_DIR', dirname(__DIR__));
define('APP_DIR', dirname(__DIR__, 2));

require_once API_DIR . '/Response.php';
require_once API_DIR . '/Auth.php';
require_once APP_DIR . '/funciones/conexion.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Method Not Allowed', 405);
}

if (!isset($conexion) || $conexion->connect_errno) {
    Response::error('Database connection failed', 503);
}

Auth::verify();

const FINCAS_VALIDAS = ['001', '002'];
const MAX_ROWS = 5000;
const REQUIRED_ROW_FIELDS = ['bloque', 'variedad', 'cosecha', 'nvari', 'fec_siem', 'rev', 'amort', 'proyecta_quipus'];

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);

if (!is_array($payload)) {
    Response::error('Body invalido: se esperaba JSON', 400);
}

$finca = isset($payload['finca']) ? trim((string)$payload['finca']) : '';
if (!in_array($finca, FINCAS_VALIDAS, true)) {
    Response::error("finca invalida: debe ser una de " . implode(', ', FINCAS_VALIDAS), 400);
}

$rows = $payload['rows'] ?? null;
if (!is_array($rows) || count($rows) === 0) {
    Response::error("'rows' debe ser un arreglo no vacio", 400);
}
if (count($rows) > MAX_ROWS) {
    Response::error('Demasiadas filas en un solo envio (maximo ' . MAX_ROWS . ')', 400);
}

$clean = [];
foreach ($rows as $i => $row) {
    if (!is_array($row)) {
        Response::error("La fila en la posicion $i no es un objeto valido", 400);
    }
    foreach (REQUIRED_ROW_FIELDS as $field) {
        if (!array_key_exists($field, $row) || $row[$field] === null || $row[$field] === '') {
            Response::error("Falta el campo '$field' en la fila en la posicion $i", 400);
        }
    }

    $fecSiem = (string)$row['fec_siem'];
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecSiem)) {
        Response::error("fec_siem invalida en la fila $i (formato esperado YYYY-MM-DD): '$fecSiem'", 400);
    }

    $clean[] = [
        'bloque'          => (string)$row['bloque'],
        'variedad'        => (string)$row['variedad'],
        'cosecha'         => (string)$row['cosecha'],
        'nvari'           => (string)$row['nvari'],
        'fec_siem'        => $fecSiem,
        'rev'             => (float)$row['rev'],
        'amort'           => (float)$row['amort'],
        'proyecta_quipus' => (float)$row['proyecta_quipus'],
    ];
}

$conexion->query(
    "CREATE TABLE IF NOT EXISTS stg_plano_proyeccion (
        id BIGINT AUTO_INCREMENT PRIMARY KEY,
        finca VARCHAR(10) NOT NULL,
        bloque VARCHAR(10) NOT NULL,
        variedad VARCHAR(30) NOT NULL,
        cosecha VARCHAR(30) NOT NULL,
        nvari VARCHAR(100) NOT NULL,
        fec_siem DATE NULL,
        rev DOUBLE NULL,
        amort DOUBLE NULL,
        proyecta_quipus DOUBLE NULL,
        estado VARCHAR(10) NOT NULL DEFAULT 'activo',
        uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_finca (finca),
        INDEX idx_finca_estado (finca, estado)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);
if ($conexion->errno) {
    Response::error('No se pudo preparar la tabla de staging: ' . $conexion->error, 500);
}

// Migracion para tablas creadas antes de agregar "estado" (portatil: "ADD
// COLUMN/INDEX IF NOT EXISTS" no es valido en todas las versiones de MySQL,
// asi que se verifica contra information_schema primero).
$colExiste = $conexion->query(
    "SELECT 1 FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stg_plano_proyeccion' AND COLUMN_NAME = 'estado'"
);
if ($colExiste && $colExiste->num_rows === 0) {
    $conexion->query(
        "ALTER TABLE stg_plano_proyeccion ADD COLUMN estado VARCHAR(10) NOT NULL DEFAULT 'activo'"
    );
}

$idxExiste = $conexion->query(
    "SELECT 1 FROM information_schema.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stg_plano_proyeccion' AND INDEX_NAME = 'idx_finca_estado'"
);
if ($idxExiste && $idxExiste->num_rows === 0) {
    $conexion->query('ALTER TABLE stg_plano_proyeccion ADD INDEX idx_finca_estado (finca, estado)');
}

$conexion->begin_transaction();
try {
    $marcarHistorico = $conexion->prepare(
        "UPDATE stg_plano_proyeccion SET estado = 'historico' WHERE finca = ? AND estado = 'activo'"
    );
    $marcarHistorico->bind_param('s', $finca);
    $marcarHistorico->execute();
    $marcarHistorico->close();

    $ins = $conexion->prepare(
        "INSERT INTO stg_plano_proyeccion
            (finca, bloque, variedad, cosecha, nvari, fec_siem, rev, amort, proyecta_quipus, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo')"
    );
    foreach ($clean as $row) {
        $ins->bind_param(
            'ssssssddd',
            $finca,
            $row['bloque'],
            $row['variedad'],
            $row['cosecha'],
            $row['nvari'],
            $row['fec_siem'],
            $row['rev'],
            $row['amort'],
            $row['proyecta_quipus']
        );
        $ins->execute();
    }
    $ins->close();

    $conexion->commit();
} catch (Throwable $e) {
    $conexion->rollback();
    Response::error('Error guardando el plano: ' . $e->getMessage(), 500);
}

Response::json(['finca' => $finca, 'inserted' => count($clean)], [], count($clean), 1, count($clean));
