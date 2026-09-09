<?php
declare(strict_types=1);

if (!defined('API_DIR')) {
    http_response_code(403);
    exit;
}

$page     = max(1, (int)($_GET['page']     ?? 1));
$pageSize = min(5000, max(1, (int)($_GET['pageSize'] ?? 500)));
$offset   = ($page - 1) * $pageSize;

$where  = [];
$params = [];
$types  = '';

if (!empty($_GET['finca'])) {
    $where[] = 'finca = ?';
    $params[] = $_GET['finca'];
    $types .= 's';
}
if (!empty($_GET['bloque'])) {
    $where[] = 'bloque = ?';
    $params[] = (int)$_GET['bloque'];
    $types .= 'i';
}
if (!empty($_GET['cosecha'])) {
    $where[] = 'cosecha = ?';
    $params[] = $_GET['cosecha'];
    $types .= 's';
}
if (!empty($_GET['flor'])) {
    $where[] = 'flor = ?';
    $params[] = $_GET['flor'];
    $types .= 's';
}
if (!empty($_GET['variedad'])) {
    $where[] = 'nombre_variedad = ?';
    $params[] = $_GET['variedad'];
    $types .= 's';
}
if (!empty($_GET['fecha_inicio'])) {
    $where[] = 'fecha >= ?';
    $params[] = $_GET['fecha_inicio'];
    $types .= 's';
}
if (!empty($_GET['fecha_fin'])) {
    $where[] = 'fecha <= ?';
    $params[] = $_GET['fecha_fin'];
    $types .= 's';
}

$whereClause = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$countStmt = $conexion->prepare("SELECT COUNT(*) FROM informes.ld_arre $whereClause");
if (!$countStmt) {
    Response::error('Error preparando consulta COUNT: ' . $conexion->error, 500);
}
if ($types !== '') $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_row()[0];
$countStmt->close();

$stmt = $conexion->prepare(
    "SELECT fecha, finca, bloque, flor, nombre_variedad AS variedad, cosecha, tipo, tallos
     FROM informes.ld_arre
     $whereClause
     ORDER BY fecha DESC, finca, bloque
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
    'finca'        => $_GET['finca']        ?? null,
    'bloque'       => $_GET['bloque']       ?? null,
    'cosecha'      => $_GET['cosecha']      ?? null,
    'flor'         => $_GET['flor']         ?? null,
    'variedad'     => $_GET['variedad']     ?? null,
    'fecha_inicio' => $_GET['fecha_inicio'] ?? null,
    'fecha_fin'    => $_GET['fecha_fin']    ?? null,
], $total, $page, $pageSize);
