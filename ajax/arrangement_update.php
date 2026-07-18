<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$tipo = trim($_POST['tipo'] ?? '');
$aplicar = trim($_POST['aplicar'] ?? '');
$seccion = isset($_POST['seccion']) && $_POST['seccion'] !== '' ? (int)$_POST['seccion'] : null;
$orden = isset($_POST['orden']) && $_POST['orden'] !== '' ? (int)$_POST['orden'] : null;
$calc = isset($_POST['calc_conciclo']) && $_POST['calc_conciclo'] !== '' ? (int)$_POST['calc_conciclo'] : null;

$old_tipo = trim($_POST['old_tipo'] ?? '');
$old_aplicar = trim($_POST['old_aplicar'] ?? '');

if ($id > 0) {
    $sql = "UPDATE informes.arrangement SET tipo=?, aplicar=?, seccion=?, orden=?, calc_conciclo=? WHERE id=?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
    $stmt->bind_param('ssiiii', $tipo, $aplicar, $seccion, $orden, $calc, $id);
} else {
    $sql = "UPDATE informes.arrangement SET tipo=?, aplicar=?, seccion=?, orden=?, calc_conciclo=? WHERE tipo=? AND aplicar=?";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) { echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
    $stmt->bind_param('ssiiiss', $tipo, $aplicar, $seccion, $orden, $calc, $old_tipo, $old_aplicar);
}

$ok = $stmt->execute();
if ($ok) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false, 'message'=>$stmt->error]);
}
