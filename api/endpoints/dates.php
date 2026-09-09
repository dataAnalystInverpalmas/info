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

if (!empty($_GET['date'])) {
    $where[] = 'fecha = ?';
    $params[] = $_GET['date'];
    $types .= 's';
}
if (isset($_GET['year']) && $_GET['year'] !== '') {
    $where[] = '`año` = ?';
    $params[] = (int)$_GET['year'];
    $types .= 'i';
}
if (isset($_GET['month']) && $_GET['month'] !== '') {
    $where[] = 'mes = ?';
    $params[] = (int)$_GET['month'];
    $types .= 'i';
}
if (isset($_GET['week']) && $_GET['week'] !== '') {
    $where[] = 'semana = ?';
    $params[] = (int)$_GET['week'];
    $types .= 'i';
}
if (isset($_GET['year_week']) && $_GET['year_week'] !== '') {
    $where[] = 'aass = ?';
    $params[] = (int)$_GET['year_week'];
    $types .= 'i';
}
if (isset($_GET['day_of_week']) && $_GET['day_of_week'] !== '') {
    $where[] = 'dia_semana = ?';
    $params[] = (int)$_GET['day_of_week'];
    $types .= 'i';
}

$whereClause = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

$countStmt = $conexion->prepare("SELECT COUNT(*) FROM dates $whereClause");
if (!$countStmt) {
    Response::error('Error preparando consulta COUNT: ' . $conexion->error, 500);
}
if ($types !== '') $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_row()[0];
$countStmt->close();

$stmt = $conexion->prepare(
    "SELECT fecha AS `date`,
            `año` AS `year`,
            mes AS `month`,
            semana AS `week`,
            aass AS year_week,
            aaaass AS year_week_iso,
            dia_semana AS day_of_week,
            ndia_semana AS day_of_week_num,
            dia AS day_name,
            lunes AS monday,
            IFNULL(festivo, 0) AS holiday
     FROM dates
     $whereClause
     ORDER BY fecha
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
    'date'         => $_GET['date']         ?? null,
    'year'         => $_GET['year']         ?? null,
    'month'        => $_GET['month']        ?? null,
    'week'         => $_GET['week']         ?? null,
    'year_week'    => $_GET['year_week']    ?? null,
    'day_of_week'  => $_GET['day_of_week']  ?? null,
], $total, $page, $pageSize);