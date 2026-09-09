<?php
declare(strict_types=1);

if (!defined('API_DIR')) {
    http_response_code(403);
    exit;
}

$page     = max(1, (int)($_GET['page']     ?? 1));
$pageSize = min(2000, max(1, (int)($_GET['pageSize'] ?? 500)));
$offset   = ($page - 1) * $pageSize;

$where  = [];
$params = [];
$types  = '';

if (isset($_GET['codflor']) && !empty($_GET['codflor'])) {
    // Acepta escalar (ROC), separado por comas (ROC,ROS) o repetido (codflor=ROC&codflor=ROS)
    $raw = $_GET['codflor'];
    $vals = is_array($raw) ? $raw : explode(',', (string)$raw);
    $codflores = array_values(array_filter(array_map(
        fn($v) => strtoupper(trim((string)$v)),
        $vals
    )));
    if ($codflores) {
        $ph = implode(',', array_fill(0, count($codflores), '?'));
        $where[] = "codflor IN ($ph)";
        foreach ($codflores as $cf) {
            $params[] = $cf;
            $types .= 's';
        }
    }
}
if (!empty($_GET['nombre'])) {
    $where[] = 'nombre LIKE ?';
    $params[] = '%' . $_GET['nombre'] . '%';
    $types .= 's';
}
if (isset($_GET['activo']) && $_GET['activo'] !== '') {
    $where[] = 'activo = ?';
    $params[] = (int)$_GET['activo'];
    $types .= 'i';
}

$whereClause = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$countStmt = $conexion->prepare("SELECT COUNT(*) FROM ld_variedades $whereClause");
if (!$countStmt) {
    Response::error('Error preparando consulta COUNT: ' . $conexion->error, 500);
}
if ($types !== '') $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_row()[0];
$countStmt->close();

$stmt = $conexion->prepare(
    "SELECT codigo, nombre, codflor, codgcol, activo
     FROM ld_variedades
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
    'codflor' => $_GET['codflor'] ?? null,
    'nombre'  => $_GET['nombre']  ?? null,
    'activo'  => $_GET['activo']  ?? null,
], $total, $page, $pageSize);
