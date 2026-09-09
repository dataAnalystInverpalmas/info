<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

// filtros opcionales enviados por GET: programa (id), estado, variedad, temporada
$programa = isset($_GET['programa']) && $_GET['programa'] !== '' ? (int)$_GET['programa'] : null;
$estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $conexion->real_escape_string($_GET['estado']) : null;
$variedad = isset($_GET['variedad']) && $_GET['variedad'] !== '' ? $conexion->real_escape_string($_GET['variedad']) : null;
$temporada = isset($_GET['temporada']) && $_GET['temporada'] !== '' ? $conexion->real_escape_string($_GET['temporada']) : null;
$producto = isset($_GET['producto']) && $_GET['producto'] !== '' ? $conexion->real_escape_string($_GET['producto']) : null;
$color = isset($_GET['color']) && $_GET['color'] !== '' ? $conexion->real_escape_string($_GET['color']) : null;
$ciclo = isset($_GET['ciclo']) && $_GET['ciclo'] !== '' ? (int)$_GET['ciclo'] : null;
$adicional = isset($_GET['adicional']) && $_GET['adicional'] !== '' ? (int)$_GET['adicional'] : null;
$semana_siembra = isset($_GET['semana_siembra']) && $_GET['semana_siembra'] !== '' ? $conexion->real_escape_string($_GET['semana_siembra']) : null;

// Select columns ordered to match table headers: id, programa, variedad, ciclo, fecha_siembra, temporada_obj, pico, ncamas, casa_id, raiz, pm, ferradica, estado, adicional, cantidad_pedida
$sql = "SELECT id, programa, variedad, ciclo, fecha_siembra, temporada_obj, pico, ncamas, casa_id, raiz, pm, ferradica, estado, adicional, cantidad_pedida FROM informes.program";
$where = [];
if($programa !== null){ $where[] = "programa = " . $programa; }
if($estado !== null){ $where[] = "estado = '" . $estado . "'"; }
if($variedad !== null){ $where[] = "variedad = '" . $variedad . "'"; }
if($temporada !== null){ $where[] = "temporada_obj LIKE '%" . $temporada . "%'"; }
if($producto !== null){ $where[] = "producto = '" . $producto . "'"; }
if($color !== null){ $where[] = "color = '" . $color . "'"; }
if($ciclo !== null){ $where[] = "ciclo = " . $ciclo; }
if($adicional !== null){ $where[] = "adicional = " . $adicional; }
if($semana_siembra !== null && strlen($semana_siembra) === 4){
    $where[] = "DATE_FORMAT(fecha_siembra, '%y%v') = " . (int)$semana_siembra;
}
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
