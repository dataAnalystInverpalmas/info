<?php
declare(strict_types=1);

if (!defined('API_DIR')) {
    http_response_code(403);
    exit;
}

$page     = max(1, (int)($_GET['page']     ?? 1));
$pageSize = min(5000, max(1, (int)($_GET['pageSize'] ?? 500)));
$offset   = ($page - 1) * $pageSize;

$where  = ['p.plantas > 0'];
$params = [];
$types  = '';

if (!empty($_GET['finca'])) {
    $where[] = 'p.finca = ?';
    $params[] = $_GET['finca'];
    $types .= 's';
}
if (!empty($_GET['bloque'])) {
    $where[] = 'p.bloque = ?';
    $params[] = (int)$_GET['bloque'];
    $types .= 'i';
}
if (!empty($_GET['producto'])) {
    // Acepta lista separada por comas: ROSAS ROJAS,ROSAS COLORES
    $productos = array_values(array_filter(
        array_map('trim', explode(',', (string)$_GET['producto']))
    ));
    if ($productos) {
        $ph = implode(',', array_fill(0, count($productos), '?'));
        $where[] = "p.producto IN ($ph)";
        foreach ($productos as $prod) {
            $params[] = $prod;
            $types .= 's';
        }
    }
}
if (!empty($_GET['variedad'])) {
    $where[] = '(p.variedad = ? OR p.variedad_reem = ?)';
    $params[] = $_GET['variedad'];
    $params[] = $_GET['variedad'];
    $types .= 'ss';
}
if (!empty($_GET['tipo_siembra'])) {
    $where[] = 'p.tipo_siembra = ?';
    $params[] = $_GET['tipo_siembra'];
    $types .= 's';
}
if (!empty($_GET['fecha_inicio'])) {
    $where[] = 'p.fecha_siembra >= ?';
    $params[] = $_GET['fecha_inicio'];
    $types .= 's';
}
if (!empty($_GET['fecha_fin'])) {
    $where[] = 'p.fecha_siembra <= ?';
    $params[] = $_GET['fecha_fin'];
    $types .= 's';
}
if (!empty($_GET['semana_siembra'])) {
    $ss = trim((string)$_GET['semana_siembra']);
    // 6 dígitos → ISO YYYYWW (ej. 202501 = semana ISO 1 del año ISO 2025).
    // 4 dígitos → legacy YYWW (ej. 2501) interpretado como ISO 2025W01.
    if (preg_match('/^\d{6}$/', $ss)) {
        $where[] = 'YEARWEEK(p.fecha_siembra, 3) = ?';
        $params[] = (int)$ss;
        $types .= 'i';
    } elseif (preg_match('/^\d{4}$/', $ss)) {
        $where[] = 'YEARWEEK(p.fecha_siembra, 3) = ?';
        $params[] = (int)('20' . $ss);
        $types .= 'i';
    }
}

$whereClause = implode(' AND ', $where);

$countStmt = $conexion->prepare("SELECT COUNT(*) FROM plane p WHERE $whereClause");
if (!$countStmt) {
    Response::error('Error preparando consulta COUNT: ' . $conexion->error, 500);
}
if ($types !== '') $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$total = (int)$countStmt->get_result()->fetch_row()[0];
$countStmt->close();

$stmt = $conexion->prepare(
    "SELECT p.finca, p.bloque, p.tabla, p.nave, p.cama,
            p.producto, p.variedad, p.tipo_suelo, p.fecha_siembra,
            p.plantas, p.tipo_siembra, p.origen, p.temporada,
            p.variedad_reem, p.cosecha_reem
     FROM plane p
     WHERE $whereClause
     ORDER BY p.finca, p.bloque, p.tabla, p.nave, p.cama
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
    'finca'          => $_GET['finca']          ?? null,
    'bloque'         => $_GET['bloque']         ?? null,
    'producto'       => $_GET['producto']       ?? null,
    'variedad'       => $_GET['variedad']       ?? null,
    'tipo_siembra'   => $_GET['tipo_siembra']   ?? null,
    'fecha_inicio'   => $_GET['fecha_inicio']   ?? null,
    'fecha_fin'      => $_GET['fecha_fin']      ?? null,
    'semana_siembra' => $_GET['semana_siembra'] ?? null,
], $total, $page, $pageSize);
