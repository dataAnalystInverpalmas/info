<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $sql = "SELECT id, tipo, aplicar, seccion, orden, calc_conciclo FROM informes.arrangement WHERE id = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
    $stmt->bind_param('i', $id);
} else {
    $tipo = $_GET['tipo'] ?? '';
    $aplicar = $_GET['aplicar'] ?? '';
    $sql = "SELECT id, tipo, aplicar, seccion, orden, calc_conciclo FROM informes.arrangement WHERE tipo = ? AND aplicar = ? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
    $stmt->bind_param('ss', $tipo, $aplicar);
}

$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();

if ($row) {
    echo json_encode(['success'=>true, 'data'=>$row]);
} else {
    echo json_encode(['success'=>false, 'message'=>'No encontrado']);
}
