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
if (!empty($_GET['variedad'])) {
    $where[] = 'variedad = ?';
    $params[] = $_GET['variedad'];
    $types .= 's';
}
if (!empty($_GET['cosecha'])) {
    $where[] = 'cosecha = ?';
    $params[] = $_GET['cosecha'];
    $types .= 's';
}
if (!empty($_GET['nvari'])) {
    $where[] = 'nvari = ?';
    $params[] = $_GET['nvari'];
    $types .= 's';
}
if (!empty($_GET['yyww_siembra'])) {
    $where[] = 'yyww_siembra = ?';
    $params[] = $_GET['yyww_siembra'];
    $types .= 's';
}
if (!empty($_GET['id_lote_siembra'])) {
    $where[] = 'id_lote_siembra = ?';
    $params[] = (int)$_GET['id_lote_siembra'];
    $types .= 'i';
}

$whereClause = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$countStmt = $conexion->prepare("SELECT COUNT(*) FROM ld_planos_proyeccion $whereClause");
if (!$countStmt) {
    Response::error('Error preparando consulta COUNT: ' . $conexion->error, 500);
}
if ($types !== '') $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_row()[0];
$countStmt->close();

$stmt = $conexion->prepare(
    "SELECT finca, bloque, variedad, cosecha, nvari, yyww_siembra, rev, amort, proyecta_quipus, id_lote_siembra
     FROM ld_planos_proyeccion
     $whereClause
     ORDER BY finca, bloque, nvari
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
    'finca'           => $_GET['finca']           ?? null,
    'bloque'          => $_GET['bloque']          ?? null,
    'variedad'        => $_GET['variedad']        ?? null,
    'cosecha'         => $_GET['cosecha']         ?? null,
    'nvari'           => $_GET['nvari']           ?? null,
    'yyww_siembra'    => $_GET['yyww_siembra']    ?? null,
    'id_lote_siembra' => $_GET['id_lote_siembra'] ?? null,
], $total, $page, $pageSize);
