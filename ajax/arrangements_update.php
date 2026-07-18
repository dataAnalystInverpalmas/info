<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$variedad = trim($_POST['variedad'] ?? '');
$finca = trim($_POST['finca'] ?? '');
$tipo = trim($_POST['tipo'] ?? '');
$aplicar = trim($_POST['aplicar'] ?? '');
$medidat = trim($_POST['medidat'] ?? '');
$valor = is_numeric($_POST['valor'] ?? null) ? (float)$_POST['valor'] : 0;

$old_variedad = trim($_POST['old_variedad'] ?? '');
$old_finca = trim($_POST['old_finca'] ?? '');
$old_tipo = trim($_POST['old_tipo'] ?? '');
$old_aplicar = trim($_POST['old_aplicar'] ?? '');

if ($id > 0) {
    $sql = "UPDATE informes.arrangements SET variedad=?, finca=?, tipo=?, aplicar=?, medidat=?, valor=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
    $stmt->bind_param('sssssdi', $variedad, $finca, $tipo, $aplicar, $medidat, $valor, $id);
} else {
    $sql = "UPDATE informes.arrangements SET variedad=?, finca=?, tipo=?, aplicar=?, medidat=?, valor=? WHERE variedad=? AND finca=? AND tipo=? AND aplicar=?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
    $stmt->bind_param('sssssdssss', $variedad, $finca, $tipo, $aplicar, $medidat, $valor, $old_variedad, $old_finca, $old_tipo, $old_aplicar);
}

$ok = $stmt->execute();
if ($ok) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false, 'message'=>$stmt->error]);
}
