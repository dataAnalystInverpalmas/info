<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

$id = $_POST['id'] ?? null;
if(!$id){ echo json_encode(['success'=>false,'message'=>'Falta id']); exit; }

$programa = (int)($_POST['programa'] ?? 0);
$producto = $_POST['producto'] ?? '';
$variedad = $_POST['variedad'] ?? '';
$temporada_obj = $_POST['temporada_obj'] ?? '';
$plantas = (int)($_POST['plantas'] ?? 0);
$finca = $_POST['finca'] ?? '';
$bloque = (int)($_POST['bloque'] ?? 0);
$ncamas = (int)($_POST['ncamas'] ?? 0);
$ciclo = (int)($_POST['ciclo'] ?? 0);
$fecha_siembra = $_POST['fecha_siembra'] ?? null;
$fecha_pico = $_POST['fecha_pico'] ?? null;
$ferradica = $_POST['ferradica'] ?? '';
$adicional = $_POST['adicional'] ?? '';
$estado = (int)($_POST['estado'] ?? 1);

$sql = "UPDATE informes.programf SET programa=?, producto=?, variedad=?, temporada_obj=?, plantas=?, finca=?, bloque=?, ncamas=?, ciclo=?, fecha_siembra=?, fecha_pico=?, ferradica=?, adicional=?, estado=? WHERE id = ?";
$stmt = $conexion->prepare($sql);
if(!$stmt){ echo json_encode(['success'=>false,'message'=>$conexion->error]); exit; }
// types: programa(i), producto(s), variedad(s), temporada_obj(s), plantas(i), finca(s), bloque(i), ncamas(i), ciclo(i), fecha_siembra(s), fecha_pico(s), ferradica(s), adicional(s), estado(i), id(i)
$stmt->bind_param('isssisiiissssii', $programa, $producto, $variedad, $temporada_obj, $plantas, $finca, $bloque, $ncamas, $ciclo, $fecha_siembra, $fecha_pico, $ferradica, $adicional, $estado, $id);
$ok = $stmt->execute();
if($ok){ echo json_encode(['success'=>true]); } else { echo json_encode(['success'=>false,'message'=>$stmt->error]); }

?>
