<?php
include("../funciones/conexion.php");

// filtros opcionales enviados por GET: programa (id), estado, variedad, temporada
$programa = isset($_GET['programa']) && $_GET['programa'] !== '' ? (int)$_GET['programa'] : null;
$estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $conexion->real_escape_string($_GET['estado']) : null;
$variedad = isset($_GET['variedad']) && $_GET['variedad'] !== '' ? $conexion->real_escape_string($_GET['variedad']) : null;
$temporada = isset($_GET['temporada']) && $_GET['temporada'] !== '' ? $conexion->real_escape_string($_GET['temporada']) : null;

// Select columns ordered to match table headers: id, programa, variedad, ciclo, fecha_siembra, temporada_obj, pico, ncamas, casa_id, raiz, pm, ferradica, estado, adicional, cantidad_pedida
$sql = "SELECT id, programa, variedad, ciclo, fecha_siembra, temporada_obj, pico, ncamas, casa_id, raiz, pm, ferradica, estado, adicional, cantidad_pedida FROM informes.program";
$where = [];
if($programa !== null){ $where[] = "programa = " . $programa; }
if($estado !== null){ $where[] = "estado = '" . $estado . "'"; }
if($variedad !== null){ $where[] = "variedad LIKE '%" . $variedad . "%'"; }
if($temporada !== null){ $where[] = "temporada_obj LIKE '%" . $temporada . "%'"; }
if(count($where) > 0){ $sql .= " WHERE " . implode(' AND ', $where); }

$sql .= " ORDER BY programa DESC";

$result = $conexion->query($sql);
$data = [];
if($result){
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
}

echo json_encode(['data' => $data]);

?>
