<?php
include("../funciones/conexion.php");

$variedad = trim($_POST['variedad'] ?? '');
$finca = trim($_POST['finca'] ?? '');
$tipo = trim($_POST['tipo'] ?? '');
$aplicar = trim($_POST['aplicar'] ?? '');
$medidat = trim($_POST['medidat'] ?? '');
$valor = is_numeric($_POST['valor'] ?? null) ? (float)$_POST['valor'] : 0;

if ($variedad === '' || $finca === '' || $tipo === '' || $aplicar === '') {
    echo json_encode(['success'=>false, 'message'=>'Campos obligatorios incompletos']);
    exit;
}

$sql = "INSERT INTO informes.arrangements (variedad, finca, tipo, aplicar, medidat, valor) VALUES (?,?,?,?,?,?)";
$stmt = $conexion->prepare($sql);
if (!$stmt) { echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
$stmt->bind_param('sssssd', $variedad, $finca, $tipo, $aplicar, $medidat, $valor);
$ok = $stmt->execute();

if ($ok) {
    echo json_encode(['success'=>true, 'id'=>$conexion->insert_id]);
} else {
    echo json_encode(['success'=>false, 'message'=>$stmt->error]);
}
