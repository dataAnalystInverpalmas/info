<?php
declare(strict_types=1);

/**
 * Recibe los ajustes de proyeccion de una finca (Palmas o Palermo) subidos
 * desde una macro de Excel, y los guarda en la tabla ld_ajustes_proyeccion.
 * Es la contraparte de subida de ajustes_proyeccion_download.php: la macro
 * descarga el archivo por finca, el usuario lo edita y lo vuelve a subir.
 *
 * El conjunto enviado reemplaza por completo las filas de esa finca (se
 * borran las anteriores y se insertan las nuevas) dentro de una misma
 * transaccion, para que la macro trabaje siempre con el estado total.
 *
 * Contrato del body (JSON):
 * {
 *   "finca": "002",              // "001" (Palmas) o "002" (Palermo)
 *   "rows": [
 *     {
 *       "id_lote_siembra": 11956,
 *       "factor": 1,
 *       "mueve_semanas": -5,
 *       "vigencia": 10,
 *       "bloqueado": 0
 *     },
 *     ...
 *   ]
 * }
 *
 * id_lote_siembra es obligatorio; factor, mueve_semanas, vigencia y
 * bloqueado son opcionales (si no vienen o vienen vacios se guarda NULL).
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

// null si el campo no viene, viene null o vacio; si viene, debe ser numerico.
$numOrNull = static function ($value): ?float {
    if ($value === null || $value === '') {
        return null;
    }
    if (!is_numeric($value)) {
        Response::error('Valor numerico invalido: ' . json_encode($value), 400);
    }
    return (float)$value;
};

$clean = [];
foreach ($rows as $i => $row) {
    if (!is_array($row)) {
        Response::error("La fila en la posicion $i no es un objeto valido", 400);
    }

    $idLote = $row['id_lote_siembra'] ?? null;
    if ($idLote === null || $idLote === '' || !is_numeric($idLote) || (int)$idLote <= 0) {
        Response::error("Falta un 'id_lote_siembra' valido en la fila en la posicion $i", 400);
    }

    $clean[] = [
        'id_lote_siembra' => (int)$idLote,
        'factor'          => $numOrNull($row['factor'] ?? null),
        'mueve_semanas'   => $numOrNull($row['mueve_semanas'] ?? null),
        'vigencia'        => $numOrNull($row['vigencia'] ?? null),
        'bloqueado'       => $numOrNull($row['bloqueado'] ?? null),
    ];
}

$conexion->query(
    "CREATE TABLE IF NOT EXISTS ld_ajustes_proyeccion (
        finca VARCHAR(10) NOT NULL,
        id_lote_siembra BIGINT NULL,
        factor DOUBLE NULL,
        mueve_semanas DOUBLE NULL,
        vigencia BIGINT NULL,
        bloqueado BIGINT NULL,
        INDEX idx_finca (finca)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);
if ($conexion->errno) {
    Response::error('No se pudo preparar la tabla de ajustes: ' . $conexion->error, 500);
}

// Migracion para tablas creadas antes de agregar el indice (portatil:
// "ADD INDEX IF NOT EXISTS" no es valido en todas las versiones de MySQL,
// asi que se verifica contra information_schema primero).
$idxExiste = $conexion->query(
    "SELECT 1 FROM information_schema.STATISTICS
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ld_ajustes_proyeccion' AND INDEX_NAME = 'idx_finca'"
);
if ($idxExiste && $idxExiste->num_rows === 0) {
    $conexion->query('ALTER TABLE ld_ajustes_proyeccion ADD INDEX idx_finca (finca)');
}

$conexion->begin_transaction();
try {
    $del = $conexion->prepare('DELETE FROM ld_ajustes_proyeccion WHERE finca = ?');
    $del->bind_param('s', $finca);
    if (!$del->execute()) {
        throw new RuntimeException('No se pudo borrar los ajustes anteriores: ' . $del->error);
    }
    $del->close();

    $ins = $conexion->prepare(
        'INSERT INTO ld_ajustes_proyeccion
            (finca, id_lote_siembra, factor, mueve_semanas, vigencia, bloqueado)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $insertadas = 0;
    foreach ($clean as $i => $row) {
        $idLote       = $row['id_lote_siembra'];
        $factor       = $row['factor'];
        $mueveSemanas = $row['mueve_semanas'];
        $vigencia     = $row['vigencia'];
        $bloqueado    = $row['bloqueado'];
        $ins->bind_param('siddii', $finca, $idLote, $factor, $mueveSemanas, $vigencia, $bloqueado);
        if (!$ins->execute()) {
            throw new RuntimeException("Fallo al insertar la fila $i (id_lote_siembra $idLote): " . $ins->error);
        }
        $insertadas += $ins->affected_rows;
    }
    $ins->close();

    if ($insertadas !== count($clean)) {
        throw new RuntimeException("Se esperaban " . count($clean) . " filas insertadas pero solo se confirmaron $insertadas");
    }

    $conexion->commit();
} catch (Throwable $e) {
    $conexion->rollback();
    Response::error('Error guardando los ajustes: ' . $e->getMessage(), 500);
}

Response::json(['finca' => $finca, 'inserted' => $insertadas], [], $insertadas, 1, $insertadas);
