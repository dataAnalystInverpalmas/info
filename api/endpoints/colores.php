<?php
declare(strict_types=1);

if (!defined('API_DIR')) {
    http_response_code(403);
    exit;
}

$page     = max(1, (int)($_GET['page']     ?? 1));
$pageSize = min(500, max(1, (int)($_GET['pageSize'] ?? 200)));
$offset   = ($page - 1) * $pageSize;

$where  = [];
$params = [];
$types  = '';

if (!empty($_GET['codigo'])) {
    $where[] = 'codigo = ?';
    $params[] = $_GET['codigo'];
    $types .= 's';
}
if (!empty($_GET['nombre'])) {
    $where[] = 'nombre LIKE ?';
    $params[] = '%' . $_GET['nombre'] . '%';
    $types .= 's';
}

$whereClause = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$countStmt = $conexion->prepare("SELECT COUNT(*) FROM ld_colores $whereClause");
if (!$countStmt) {
    Response::error('Error preparando consulta COUNT: ' . $conexion->error, 500);
}
if ($types !== '') $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_row()[0];
$countStmt->close();

$stmt = $conexion->prepare(
    "SELECT codigo, nombre, orden
     FROM ld_colores
     $whereClause
     ORDER BY nombre
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
    'codigo' => $_GET['codigo'] ?? null,
    'nombre' => $_GET['nombre'] ?? null,
], $total, $page, $pageSize);
