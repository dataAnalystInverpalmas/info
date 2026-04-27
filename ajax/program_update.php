<?php
include("../funciones/conexion.php");

$id = $_POST['id'] ?? null;
if(!$id){ echo json_encode(['success'=>false,'message'=>'Falta id']); exit; }

$programa = (int)($_POST['programa'] ?? 0);
$variedad = $_POST['variedad'] ?? '';
$ciclo = (int)($_POST['ciclo'] ?? 0);
$fecha_siembra = $_POST['fecha_siembra'] ?? null;
$temporada_obj = $_POST['temporada_obj'] ?? '';
$ncamas = (int)($_POST['ncamas'] ?? 0);
$casa_id = (int)($_POST['casa_id'] ?? 0);
$pico = $_POST['pico'] ?? '';
$raiz = (int)($_POST['raiz'] ?? 0);
$pm = (int)($_POST['pm'] ?? 0);
$ferradica = $_POST['ferradica'] ?? '';
$estado = $_POST['estado'] ?? '';
$adicional = (int)($_POST['adicional'] ?? 0);
$cantidad_pedida = (int)($_POST['cantidad_pedida'] ?? 0);

$sql = "UPDATE informes.program SET programa=?, variedad=?, ciclo=?, fecha_siembra=?, temporada_obj=?, ncamas=?, casa_id=?, pico=?, raiz=?, pm=?, ferradica=?, estado=?, adicional=?, cantidad_pedida=? WHERE id = ?";
$stmt = $conexion->prepare($sql);
if(!$stmt){ echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
// types: programa(i), variedad(s), ciclo(i), fecha_siembra(s), temporada_obj(s), ncamas(i), casa_id(i), pico(s), raiz(i), pm(i), ferradica(s), estado(s), adicional(i), cantidad_pedida(i), id(i)
$stmt->bind_param('isississiiissii', $programa, $variedad, $ciclo, $fecha_siembra, $temporada_obj, $ncamas, $casa_id, $pico, $raiz, $pm, $ferradica, $estado, $adicional, $cantidad_pedida, $id);
$ok = $stmt->execute();
if($ok){ echo json_encode(['success'=>true]); } else { echo json_encode(['success'=>false,'message'=>$stmt->error]); }

?>
