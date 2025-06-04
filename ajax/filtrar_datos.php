<?php
include('../funciones/conexion.php');

//consutla para filtrar los datos dinamicamente

$fincas =  $_POST['finca'] ?? [];
$bloques = $_POST['bloque'] ?? [];
$variedades = $_POST['variedad'] ?? [];
$siembras = $_POST['siembra'] ?? [];

//aqui se constuye el where  dependiendo la seleccion
function buildWhere($exclude, $fincas, $bloques, $variedades, $siembras, $conexion){
    $where = [];

    if($exclude !== 'finca' && !empty($fincas)) {
        $in = "'" . implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($fincas), $conexion), $fincas)) . "'";
        $where[] = "finca IN ($in)";
    }
    
    if($exclude !== 'bloque' && !empty($bloques)) {
        $in = "'" . implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($bloques), $conexion), $bloques)) . "'";
        $where[] = "bloque IN ($in)";
    }
    
    if($exclude !== 'variedad' && !empty($variedades)) {
        $in = "'" . implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($variedades), $conexion), $variedades)) . "'";
        $where[] = "variedad IN ($in)";
    }
    
    if($exclude !== 'siembra' && !empty($siembras)) {
        $in = "'" . implode("','", array_map('mysqli_real_escape_string', array_fill(0, count($siembras), $conexion), $siembras)) . "'";
        $where[] = "temporada IN ($in)";
    }

    //revisa que fue lo que seleccione y construye el sql
    return !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
}

$respuesta = [
    'finca' => [],
    'bloque' => [],
    'variedad' => [],
    'siembra' => []
];

//aqui hace la busqueda independiente de la infromacion

//finca
$whereFinca = buildWhere('finca', $fincas, $bloques, $variedades, $siembras, $conexion);
$queryFinca = "SELECT DISTINCT finca from informes.plane $whereFinca order by 1 asc ";
$resultFinca = $conexion->query($queryFinca);
while($row = $resultFinca->fetch_assoc()){
    $respuesta['finca'][] = $row['finca'];
}

//bloque
$whereBloque = buildWhere('bloque', $fincas, $bloques, $variedades, $siembras, $conexion);
$queryBloque = "SELECT DISTINCT bloque from informes.plane $whereBloque order by 1 asc";
$resultBloque = $conexion->query($queryBloque);
while($row = $resultBloque->fetch_assoc()){
    $respuesta['bloque'][] = $row['bloque'];
}

//variedad
$whereVariedad = buildWhere('variedad', $fincas, $bloques, $variedades, $siembras, $conexion);
$queryVariedad = "SELECT DISTINCT variedad from informes.plane $whereVariedad order by 1 asc";
$resultVariedad = $conexion->query($queryVariedad);
while($row = $resultVariedad->fetch_assoc()){
    $respuesta['variedad'][] = $row['variedad'];
}

//siembra
$whereSiembra = buildWhere('siembra', $fincas, $bloques, $variedades, $siembras, $conexion);
$querySiembra = "SELECT DISTINCT  temporada from informes.plane $whereSiembra order by 1 asc";
$resultSiembra = $conexion->query($querySiembra);
while($row = $resultSiembra->fetch_assoc()){
    $respuesta['siembra'][] = $row['temporada'];
}

foreach($respuesta as $key => $values){
    $respuesta[$key] = array_values(array_unique($values));
}

header('Content-Type: application/json');

echo json_encode($respuesta);