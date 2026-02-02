<?php
include("../funciones/conexion.php");

$id = $_GET['id'] ?? null;
if(!$id){ echo json_encode(['success'=>false,'message'=>'Falta id']); exit; }

$sql = "SELECT id, programa, variedad, ciclo, fecha_siembra, temporada_obj, ncamas, casa_id, pico, raiz, pm, ferradica, estado, adicional, cantidad_pedida FROM informes.program WHERE id = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
if(!$stmt){ echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
$stmt->bind_param('i',$id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
if($row){
    echo json_encode(['success'=>true,'data'=>$row]);
} else {
    echo json_encode(['success'=>false,'message'=>'No encontrado']);
}

?>
