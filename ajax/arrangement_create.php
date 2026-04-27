<?php
include("../funciones/conexion.php");

$tipo = trim($_POST['tipo'] ?? '');
$aplicar = trim($_POST['aplicar'] ?? '');
$seccion = isset($_POST['seccion']) && $_POST['seccion'] !== '' ? (int)$_POST['seccion'] : null;
$orden = isset($_POST['orden']) && $_POST['orden'] !== '' ? (int)$_POST['orden'] : null;
$calc = isset($_POST['calc_conciclo']) && $_POST['calc_conciclo'] !== '' ? (int)$_POST['calc_conciclo'] : null;

if ($tipo === '' || $aplicar === '') {
    echo json_encode(['success'=>false, 'message'=>'Campos obligatorios incompletos']);
    exit;
}

$sql = "INSERT INTO informes.arrangement (tipo, aplicar, seccion, orden, calc_conciclo) VALUES (?,?,?,?,?)";
$stmt = $conexion->prepare($sql);
if (!$stmt) { echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
$stmt->bind_param('ssiii', $tipo, $aplicar, $seccion, $orden, $calc);
$ok = $stmt->execute();

if ($ok) {
    echo json_encode(['success'=>true, 'id'=>$conexion->insert_id]);
} else {
    echo json_encode(['success'=>false, 'message'=>$stmt->error]);
}
