<?php

if (is_file("funciones/conexion.php")){
	  include ("funciones/conexion.php");
        }
	else {
	  include ("../funciones/conexion.php");
}

$where = " WHERE estado=1 ";

if (!empty($_POST)){
	$finicial = strval($_POST['fini'] ?? '');
	$ffinal = strval($_POST['ffin'] ?? '');
    $year = $_POST['year_p'] ?? '';	
    $casa = strval($_POST['casa'] ?? '');

    if ($finicial != "" && $ffinal != "" && $year != "" && $casa == ""){
		$where .= " and p.programa=" . intval($year) . " ";
	} else if ($finicial != "" && $ffinal != "" && $year == "" && $casa == "") {
        $where .= " and p.fecha_siembra BETWEEN '" . $conexion->real_escape_string($finicial) . "' and '" . $conexion->real_escape_string($ffinal) . "' ";
    } else if ($finicial != "" && $ffinal != "" && $year != "" && $casa != "") {
        $where .= " and p.programa=" . intval($year) . " and b.nombre='" . $conexion->real_escape_string($casa) . "' ";
    } else if ($year != "") {
        $where .= " and p.programa=" . intval($year) . " ";
    }
}       

$query="
        SELECT p.variedad,
                p.temporada_obj,
                p.producto,
                p.ciclo,
                p.fecha_siembra,
                MIN(p.fecha_ensarte) AS fecha_ensarte,
                MIN(p.fecha_cosecha) AS fecha_cosecha,
                MIN(p.fecha_pico) AS fecha_pico,
                p.programa,
                if(p.tipo='REEMPLAZO','REEMPLAZO','PROGRAMA') AS tipo,
                b.nombre as casa,
                p.raiz,
                sum(p.plantas) as plantas
        FROM program as p
        LEFT JOIN breeders as b ON b.id=p.casa_id
        $where
        GROUP BY p.variedad, p.temporada_obj, p.producto, p.ciclo, p.fecha_siembra, p.programa, p.tipo, b.nombre, p.raiz  
        UNION
        SELECT p.variedad,
                p.temporada_obj,
                p.producto,
                p.ciclo,
                p.fecha_siembra,
                MIN(p.fecha_ensarte) AS fecha_ensarte,
                MIN(p.fecha_cosecha) AS fecha_cosecha,
                MIN(p.fecha_pico) AS fecha_pico,
                p.programa,
                if(p.tipo='REEMPLAZO','REEMPLAZO','PROGRAMA') AS tipo,
                b.nombre as casa,
                p.raiz,
                sum(p.plantas) as plantas
        FROM program_add_pto as p
        LEFT JOIN breeders as b ON b.id=p.casa_id
        $where
        GROUP BY p.variedad, p.temporada_obj, p.producto, p.ciclo, p.fecha_siembra, p.programa, p.tipo, b.nombre, p.raiz
";

$result=$conexion->query($query);

$response = array();

while($row = $result->fetch_assoc()){
    $response[]=$row;
}

echo json_encode($response);

$conexion->close();

 ?> 