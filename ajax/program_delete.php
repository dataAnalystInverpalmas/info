<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

$id = $_POST['id'] ?? null;
if(!$id){ echo json_encode(['success'=>false,'message'=>'Falta id']); exit; }

$sql = "DELETE FROM informes.program WHERE id = ?";
$stmt = $conexion->prepare($sql);
if(!$stmt){ echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
$stmt->bind_param('i',$id);
$ok = $stmt->execute();
if($ok){ echo json_encode(['success'=>true]); } else { echo json_encode(['success'=>false,'message'=>$stmt->error]); }

?>
