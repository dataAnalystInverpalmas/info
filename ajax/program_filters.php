<?php
// Devuelve listas filtradas de variedad y temporada según filtros (programa, estado)
header('Content-Type: application/json; charset=utf-8');

if (is_file("../funciones/conexion.php")){
    include "../funciones/conexion.php";
} else {
    include "./funciones/conexion.php";
}

$programa = isset($_GET['programa']) && $_GET['programa'] !== '' ? (int)$_GET['programa'] : null;
$estado = isset($_GET['estado']) && $_GET['estado'] !== '' ? $_GET['estado'] : null; // keep as string for comparision

$where = [];
if ($programa !== null) { $where[] = "programa = " . $programa; }
if ($estado !== null && $estado !== '') { $estado_esc = $conexion->real_escape_string($estado); $where[] = "estado = '" . $estado_esc . "'"; }
$where_sql = count($where) ? (' WHERE ' . implode(' AND ', $where)) : '';

$data = ['variedades'=>[], 'temporadas'=>[], 'productos'=>[], 'colores'=>[]];

// Variedades
$qv = "SELECT DISTINCT variedad FROM program" . $where_sql . " AND variedad IS NOT NULL AND variedad <> '' ORDER BY variedad";
// If where_sql is empty, fix the extra AND
if (!$where_sql) { $qv = "SELECT DISTINCT variedad FROM program WHERE variedad IS NOT NULL AND variedad <> '' ORDER BY variedad"; }

$res = $conexion->query($qv);
if ($res) {
    while ($r = $res->fetch_object()) { $data['variedades'][] = $r->variedad; }
    $res->free();
}

// Temporadas
$qt = "SELECT DISTINCT temporada_obj FROM program" . $where_sql . " AND temporada_obj IS NOT NULL AND temporada_obj <> '' ORDER BY temporada_obj";
if (!$where_sql) { $qt = "SELECT DISTINCT temporada_obj FROM program WHERE temporada_obj IS NOT NULL AND temporada_obj <> '' ORDER BY temporada_obj"; }

$res = $conexion->query($qt);
if ($res) {
    while ($r = $res->fetch_object()) { $data['temporadas'][] = $r->temporada_obj; }
    $res->free();
}

// Productos (Flor)
$qp = "SELECT DISTINCT producto FROM program" . $where_sql . " AND producto IS NOT NULL AND producto <> '' ORDER BY producto";
if (!$where_sql) { $qp = "SELECT DISTINCT producto FROM program WHERE producto IS NOT NULL AND producto <> '' ORDER BY producto"; }

$res = $conexion->query($qp);
if ($res) {
    while ($r = $res->fetch_object()) { $data['productos'][] = $r->producto; }
    $res->free();
}

// Colores
$qc = "SELECT DISTINCT color FROM program" . $where_sql . " AND color IS NOT NULL AND color <> '' ORDER BY color";
if (!$where_sql) { $qc = "SELECT DISTINCT color FROM program WHERE color IS NOT NULL AND color <> '' ORDER BY color"; }

$res = $conexion->query($qc);
if ($res) {
    while ($r = $res->fetch_object()) { $data['colores'][] = $r->color; }
    $res->free();
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
