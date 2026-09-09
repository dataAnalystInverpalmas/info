<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

// filtros opcionales enviados por GET
$programa = isset($_GET['programa']) && $_GET['programa'] !== '' ? (int)$_GET['programa'] : null;
$estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $conexion->real_escape_string($_GET['estado']) : null;
$variedad = isset($_GET['variedad']) && trim($_GET['variedad']) !== '' ? $conexion->real_escape_string(trim($_GET['variedad'])) : null;
$temporada = isset($_GET['temporada']) && $_GET['temporada'] !== '' ? trim($_GET['temporada']) : null;
$producto = isset($_GET['producto']) && $_GET['producto'] !== '' ? $conexion->real_escape_string($_GET['producto']) : null;
$finca = isset($_GET['finca']) && $_GET['finca'] !== '' ? $conexion->real_escape_string($_GET['finca']) : null;
$bloque = isset($_GET['bloque']) && $_GET['bloque'] !== '' ? (int)$_GET['bloque'] : null;
$ciclo = isset($_GET['ciclo']) && $_GET['ciclo'] !== '' ? (int)$_GET['ciclo'] : null;
$adicional = isset($_GET['adicional']) && $_GET['adicional'] !== '' ? $conexion->real_escape_string($_GET['adicional']) : null;
$fecha_inicio = isset($_GET['fecha_inicio']) && $_GET['fecha_inicio'] !== '' ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) && $_GET['fecha_fin'] !== '' ? $_GET['fecha_fin'] : null;
$semana_siembra = isset($_GET['semana_siembra']) && $_GET['semana_siembra'] !== '' ? $conexion->real_escape_string($_GET['semana_siembra']) : null;
$color = isset($_GET['color']) && $_GET['color'] !== '' ? $conexion->real_escape_string($_GET['color']) : null;

$sql = "SELECT id, programa, producto, variedad, temporada_obj, plantas, finca, bloque, ncamas, ciclo, fecha_siembra, fecha_pico, ferradica, adicional, estado, COALESCE(color, (SELECT v.color FROM varieties v WHERE TRIM(v.nombre) = TRIM(programf.variedad))) AS color FROM informes.programf";
$where = [];
if($programa !== null){ $where[] = "programa = " . $programa; }
if($estado !== null){ $where[] = "estado = '" . $estado . "'"; }
if($variedad !== null){ $where[] = "TRIM(variedad) = '" . $variedad . "'"; }
if($temporada !== null){
    $temporadas = array_filter(array_map(function($item){ return trim($item); }, preg_split('/[,;\n]/', $temporada)), function($item){ return $item !== ''; });
    if(!empty($temporadas)){
        $or = [];
        foreach($temporadas as $tmp){
            $tmpNorm = strtoupper(trim($tmp));
            $or[] = "UPPER(TRIM(temporada_obj)) = '" . $conexion->real_escape_string($tmpNorm) . "'";
        }
        $where[] = '(' . implode(' OR ', $or) . ')';
    }
}
if($producto !== null){ $where[] = "producto LIKE '%" . $producto . "%'"; }
if($finca !== null){ $where[] = "finca LIKE '%" . $finca . "%'"; }
if($bloque !== null){ $where[] = "bloque = " . $bloque; }
if($ciclo !== null){ $where[] = "ciclo = " . $ciclo; }
if($adicional !== null){ $where[] = "adicional = '" . $adicional . "'"; }
if($fecha_inicio !== null){ $where[] = "DATE(fecha_siembra) >= '" . $conexion->real_escape_string($fecha_inicio) . "'"; }
if($fecha_fin !== null){ $where[] = "DATE(fecha_siembra) <= '" . $conexion->real_escape_string($fecha_fin) . "'"; }
if($semana_siembra !== null && strlen($semana_siembra) === 4){
    $where[] = "DATE_FORMAT(fecha_siembra, '%y%v') = " . (int)$semana_siembra;
}
if($color !== null){
    $colorNorm = strtoupper(trim($color));
    $where[] = "(UPPER(TRIM(COALESCE(color, (SELECT distinct color FROM programf)))) = '" . $conexion->real_escape_string($colorNorm) . "')";
}
if(count($where) > 0){ $sql .= " WHERE " . implode(' AND ', $where); }

$sql .= " ORDER BY programa DESC, fecha_siembra ASC";

$result = $conexion->query($sql);
$data = [];
if($result){
    while($row = $result->fetch_assoc()){
        $data[] = $row;
    }
}

echo json_encode(['data' => $data]);

?>
