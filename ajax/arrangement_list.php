<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$aplicar = isset($_GET['aplicar']) ? trim($_GET['aplicar']) : '';

$sql = "SELECT id, tipo, aplicar, seccion, orden, calc_conciclo FROM informes.arrangement WHERE 1=1";
$types = '';
$params = [];

if ($tipo !== '') { $sql .= " AND tipo LIKE ?"; $types .= 's'; $params[] = '%' . $tipo . '%'; }
if ($aplicar !== '') { $sql .= " AND aplicar LIKE ?"; $types .= 's'; $params[] = '%' . $aplicar . '%'; }

$sql .= " ORDER BY tipo, orden, aplicar";
$stmt = $conexion->prepare($sql);
if (!$stmt) { echo json_encode(['data'=>[], 'message'=>$conexion->error]); exit; }

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
