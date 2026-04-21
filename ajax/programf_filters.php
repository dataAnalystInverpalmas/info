<?php
// Devuelve listas filtradas de variedad, temporada, producto y finca según filtros
header('Content-Type: application/json; charset=utf-8');

if (is_file("../funciones/conexion.php")){
    include "../funciones/conexion.php";
} else {
    include "./funciones/conexion.php";
}

$programa = isset($_GET['programa']) && $_GET['programa'] !== '' ? (int)$_GET['programa'] : null;
$estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : null;

$where = [];
if ($programa !== null) { $where[] = "programa = " . $programa; }
if ($estado !== null && $estado !== '') { $estado_esc = $conexion->real_escape_string($estado); $where[] = "estado = '" . $estado_esc . "'"; }
$where_sql = count($where) ? (' WHERE ' . implode(' AND ', $where)) : '';

$data = ['variedades'=>[], 'temporadas'=>[], 'productos'=>[], 'fincas'=>[], 'bloques'=>[]];

// Variedades
$qv = "SELECT DISTINCT variedad FROM programf" . $where_sql . " AND variedad IS NOT NULL AND variedad <> '' ORDER BY variedad";
if (!$where_sql) { $qv = "SELECT DISTINCT variedad FROM programf WHERE variedad IS NOT NULL AND variedad <> '' ORDER BY variedad"; }
$res = $conexion->query($qv);
if ($res) {
    while ($r = $res->fetch_object()) { $data['variedades'][] = $r->variedad; }
    $res->free();
}

// Temporadas
$qt = "SELECT DISTINCT temporada_obj FROM programf" . $where_sql . " AND temporada_obj IS NOT NULL AND temporada_obj <> '' ORDER BY temporada_obj";
if (!$where_sql) { $qt = "SELECT DISTINCT temporada_obj FROM programf WHERE temporada_obj IS NOT NULL AND temporada_obj <> '' ORDER BY temporada_obj"; }
$res = $conexion->query($qt);
if ($res) {
    while ($r = $res->fetch_object()) { $data['temporadas'][] = $r->temporada_obj; }
    $res->free();
}

// Productos
$qp = "SELECT DISTINCT producto FROM programf" . $where_sql . " AND producto IS NOT NULL AND producto <> '' ORDER BY producto";
if (!$where_sql) { $qp = "SELECT DISTINCT producto FROM programf WHERE producto IS NOT NULL AND producto <> '' ORDER BY producto"; }
$res = $conexion->query($qp);
if ($res) {
    while ($r = $res->fetch_object()) { $data['productos'][] = $r->producto; }
    $res->free();
}

// Fincas
$qf = "SELECT DISTINCT finca FROM programf" . $where_sql . " AND finca IS NOT NULL AND finca <> '' ORDER BY finca";
if (!$where_sql) { $qf = "SELECT DISTINCT finca FROM programf WHERE finca IS NOT NULL AND finca <> '' ORDER BY finca"; }
$res = $conexion->query($qf);
if ($res) {
    while ($r = $res->fetch_object()) { $data['fincas'][] = $r->finca; }
    $res->free();
}

// Bloques
$qb = "SELECT DISTINCT bloque FROM programf" . $where_sql . " AND bloque IS NOT NULL ORDER BY bloque";
if (!$where_sql) { $qb = "SELECT DISTINCT bloque FROM programf WHERE bloque IS NOT NULL ORDER BY bloque"; }
$res = $conexion->query($qb);
if ($res) {
    while ($r = $res->fetch_object()) { $data['bloques'][] = $r->bloque; }
    $res->free();
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
