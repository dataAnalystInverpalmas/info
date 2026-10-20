<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';
\App\Helpers\Auth::requireAdmin();

header('Content-Type: application/json; charset=utf-8');

$desde = trim($_GET['desde'] ?? '');
$hasta = trim($_GET['hasta'] ?? '');
$status = trim($_GET['status'] ?? '');
$buscar = trim($_GET['buscar'] ?? '');

// 1. Estadísticas del día de hoy
$todayStats = [
    'total_hoy'     => 0,
    'exitosos_hoy'  => 0,
    'fallidos_hoy'  => 0,
    'bloqueados_hoy'=> 0,
];

$statRes = $conexion->query("SELECT 
    COUNT(*) AS total_hoy,
    SUM(CASE WHEN status = 'EXITOSO' THEN 1 ELSE 0 END) AS exitosos_hoy,
    SUM(CASE WHEN status = 'FALLIDO' THEN 1 ELSE 0 END) AS fallidos_hoy,
    SUM(CASE WHEN status = 'BLOQUEADO' THEN 1 ELSE 0 END) AS bloqueados_hoy
FROM login_logs 
WHERE DATE(created_at) = CURDATE()");

if ($statRes && ($row = $statRes->fetch_assoc())) {
    $todayStats['total_hoy']      = (int)($row['total_hoy'] ?? 0);
    $todayStats['exitosos_hoy']   = (int)($row['exitosos_hoy'] ?? 0);
    $todayStats['fallidos_hoy']   = (int)($row['fallidos_hoy'] ?? 0);
    $todayStats['bloqueados_hoy'] = (int)($row['bloqueados_hoy'] ?? 0);
}

// 2. Consulta de registros con filtros
$where = ['1=1'];
$types = '';
$params = [];

if ($desde !== '') {
    $where[] = "created_at >= ?";
    $types .= 's';
    $params[] = $desde . ' 00:00:00';
}
if ($hasta !== '') {
    $where[] = "created_at <= ?";
    $types .= 's';
    $params[] = $hasta . ' 23:59:59';
}
if ($status !== '' && in_array($status, ['EXITOSO', 'FALLIDO', 'BLOQUEADO'], true)) {
    $where[] = "status = ?";
    $types .= 's';
    $params[] = $status;
}
if ($buscar !== '') {
    $where[] = "(username LIKE ? OR ip_address LIKE ? OR details LIKE ?)";
    $types .= 'sss';
    $like = '%' . $buscar . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql = "SELECT id, user_id, username, ip_address, user_agent, status, details, created_at 
        FROM login_logs 
        WHERE " . implode(' AND ', $where) . " 
        ORDER BY id DESC LIMIT 500";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['data' => [], 'stats' => $todayStats, 'error' => $conexion->error]);
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($r = $res->fetch_assoc()) {
    $data[] = [
        'id'         => (int)$r['id'],
        'user_id'    => $r['user_id'] ? (int)$r['user_id'] : null,
        'username'   => htmlspecialchars($r['username'], ENT_QUOTES, 'UTF-8'),
        'ip_address' => htmlspecialchars($r['ip_address'], ENT_QUOTES, 'UTF-8'),
        'user_agent' => htmlspecialchars($r['user_agent'] ?? '', ENT_QUOTES, 'UTF-8'),
        'status'     => $r['status'],
        'details'    => htmlspecialchars($r['details'] ?? '', ENT_QUOTES, 'UTF-8'),
        'created_at' => $r['created_at'],
    ];
}
$stmt->close();

echo json_encode([
    'data'  => $data,
    'stats' => $todayStats,
]);
