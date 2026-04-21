<?php
include("../funciones/conexion.php");

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id > 0) {
    $sql = "DELETE FROM informes.arrangement WHERE id = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
    $stmt->bind_param('i', $id);
} else {
    $tipo = trim($_POST['tipo'] ?? '');
    $aplicar = trim($_POST['aplicar'] ?? '');

    $sql = "DELETE FROM informes.arrangement WHERE tipo = ? AND aplicar = ?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
    $stmt->bind_param('ss', $tipo, $aplicar);
}

$ok = $stmt->execute();
if ($ok) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false, 'message'=>$stmt->error]);
}
