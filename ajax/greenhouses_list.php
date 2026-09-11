<?php
require_once __DIR__ . '/_crud_dynamic_common.php';
include(__DIR__ . '/../funciones/conexion.php');
header('Content-Type: application/json; charset=utf-8');

$types = '';
$params = [];

$sql = "SELECT g.*, f.nombre AS finca_name
        FROM greenhouses g
        LEFT JOIN farms f ON g.finca_id = f.id
        WHERE 1=1";

if (isset($_GET['finca_id']) && trim((string)$_GET['finca_id']) !== '') {
    $sql .= ' AND g.finca_id = ?';
    $types .= 'i';
    $params[] = (int)$_GET['finca_id'];
}

if (isset($_GET['bloque']) && trim((string)$_GET['bloque']) !== '') {
    $sql .= ' AND g.bloque LIKE ?';
    $types .= 's';
    $params[] = '%' . trim($_GET['bloque']) . '%';
}

if (isset($_GET['tabla']) && trim((string)$_GET['tabla']) !== '') {
    $sql .= ' AND g.tabla LIKE ?';
    $types .= 's';
    $params[] = '%' . trim($_GET['tabla']) . '%';
}

if (isset($_GET['nave']) && trim((string)$_GET['nave']) !== '') {
    $sql .= ' AND g.nave LIKE ?';
    $types .= 's';
    $params[] = '%' . trim($_GET['nave']) . '%';
}

$sql .= ' ORDER BY g.id DESC';

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