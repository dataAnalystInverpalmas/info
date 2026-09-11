<?php
require_once __DIR__ . '/_crud_dynamic_common.php';
include(__DIR__ . '/../funciones/conexion.php');
header('Content-Type: application/json; charset=utf-8');

$types = '';
$params = [];

$sql = "SELECT s.*, CONCAT(aa.tipo, ' - ', aa.aplicar) AS arrangement_name
        FROM supplies s
        LEFT JOIN arrangement aa ON s.arrangement_id = aa.id
        WHERE 1=1";

if (isset($_GET['arrangement_name']) && trim((string)$_GET['arrangement_name']) !== '') {
    $sql .= ' AND CONCAT(aa.tipo, " - ", aa.aplicar) LIKE ?';
    $types .= 's';
    $params[] = '%' . trim($_GET['arrangement_name']) . '%';
}

if (isset($_GET['finca']) && trim((string)$_GET['finca']) !== '') {
    $sql .= ' AND s.finca LIKE ?';
    $types .= 's';
    $params[] = '%' . trim($_GET['finca']) . '%';
}

if (isset($_GET['arrangement_id']) && trim((string)$_GET['arrangement_id']) !== '') {
    $sql .= ' AND s.arrangement_id = ?';
    $types .= 'i';
    $params[] = (int)$_GET['arrangement_id'];
}
if (isset($_GET['insumo']) && trim((string)$_GET['insumo']) !== '') {
    $sql .= ' AND s.insumo LIKE ?';
    $types .= 's';
    $params[] = '%' . trim($_GET['insumo']) . '%';
}
if (isset($_GET['medida']) && trim((string)$_GET['medida']) !== '') {
    $sql .= ' AND s.medida LIKE ?';
    $types .= 's';
    $params[] = '%' . trim($_GET['medida']) . '%';
}
if (isset($_GET['dosis']) && trim((string)$_GET['dosis']) !== '') {
    $sql .= ' AND s.dosis LIKE ?';
    $types .= 's';
    $params[] = '%' . trim($_GET['dosis']) . '%';
}

$sql .= ' ORDER BY s.id DESC';

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['data' => [], 'message' => $conexion->error]);
    exit;
}

if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$res = $stmt->get_result();

$data = [];
while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode(['data' => $data]);
