<?php
declare(strict_types=1);

if (!defined('API_DIR')) {
    http_response_code(403);
    exit;
}

// La tabla 'table_curves' (variedad, edad, fmata) no existe en el esquema actual
// y se alimentaba por carga de archivo (archivos/tabla_curvas.xlsx).
// Este endpoint usa 'curva_variedad_rosas', tabla paramétrica existente con las
// curvas estandarizadas por variedad (ciclo y distribución JSON por estación).

$page     = max(1, (int)($_GET['page']     ?? 1));
$pageSize = min(1000, max(1, (int)($_GET['pageSize'] ?? 1000)));
$offset   = ($page - 1) * $pageSize;

$where  = [];
$params = [];
$types  = '';

if (!empty($_GET['variedad'])) {
    $where[] = 'variedad = ?';
    $params[] = $_GET['variedad'];
    $types .= 's';
}
if (isset($_GET['ciclo']) && $_GET['ciclo'] !== '') {
    $where[] = 'ciclo = ?';
    $params[] = (int)$_GET['ciclo'];
    $types .= 'i';
}
if (isset($_GET['activo']) && $_GET['activo'] !== '') {
    $where[] = 'activo = ?';
    $params[] = (int)$_GET['activo'];
    $types .= 'i';
}

$whereClause = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$countStmt = $conexion->prepare("SELECT COUNT(*) FROM curva_variedad_rosas $whereClause");
if (!$countStmt) {
    Response::error('Error preparando consulta COUNT: ' . $conexion->error, 500);
}
if ($types !== '') $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_row()[0];
$countStmt->close();

$stmt = $conexion->prepare(
    "SELECT id, variedad, ciclo, curva, s1, s2, s3, activo, fecha_creacion, porcentaje_ciegos
     FROM curva_variedad_rosas
     $whereClause
     ORDER BY variedad, ciclo
     LIMIT ? OFFSET ?"
);
if (!$stmt) {
    Response::error('Error preparando consulta de datos: ' . $conexion->error, 500);
}
$stmt->bind_param($types . 'ii', ...array_merge($params, [$pageSize, $offset]));
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) $data[] = $row;
$stmt->close();

Response::json($data, [
    'variedad' => $_GET['variedad'] ?? null,
    'ciclo'    => $_GET['ciclo']    ?? null,
    'activo'   => $_GET['activo']   ?? null,
], $total, $page, $pageSize);