<?php
include("../funciones/conexion.php");

$variedad = isset($_GET['variedad']) ? trim($_GET['variedad']) : '';
$finca = isset($_GET['finca']) ? trim($_GET['finca']) : '';
$tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';

$sql = "SELECT id, variedad, finca, tipo, aplicar, medidat, valor FROM informes.arrangements WHERE 1=1";
$types = '';
$params = [];

if ($variedad !== '') { $sql .= " AND variedad LIKE ?"; $types .= 's'; $params[] = '%' . $variedad . '%'; }
if ($finca !== '') { $sql .= " AND finca LIKE ?"; $types .= 's'; $params[] = '%' . $finca . '%'; }
if ($tipo !== '') { $sql .= " AND tipo LIKE ?"; $types .= 's'; $params[] = '%' . $tipo . '%'; }

$sql .= " ORDER BY tipo, aplicar, finca, variedad";
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
