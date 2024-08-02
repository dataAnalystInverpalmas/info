<?php
if (is_file("funciones/conexion.php")){
	  include ("funciones/conexion.php");
	}
	else {
	  include ("../funciones/conexion.php");
  }

$where = " WHERE a.sem<102 and p.tipo_siembra<>'REEMPLAZO' ";

if ($_POST){
	
	$fini=$_POST['fini'];
	$ffin=$_POST['ffin'];
	$flor=$_POST['flor'];
    $finca=$_POST['finca'];

	if ($fini != "" & $ffin != "" and $flor != "" and $finca != ""){
		$where .= " and p.finca='$finca' and v.producto='$flor' and (p.fecha_siembra BETWEEN '$fini' and '$ffin') ";
	}elseif ($fini != "" & $ffin != "" and $flor != "" and $finca = ""){
		$where .= " and p.finca='$flor' and (p.fecha_siembra BETWEEN '$fini' and '$ffin') ";
	}elseif ($fini != "" & $ffin != "" and $flor = "" and $finca != ""){
		$where .= " and v.producto='$finca' and (p.fecha_siembra BETWEEN '$fini' and '$ffin') ";
	}elseif ($fini != "" & $ffin != "" and $flor = "" and $finca = ""){
		$where .= " and (p.fecha_siembra BETWEEN '$fini' and '$ffin') ";
	}else{
		$where .= "";
	}
}

$query="
	SELECT p.finca,
	p.bloque,
	p.variedad,
	p.temporada,
	p.fecha_siembra,
	DATE_ADD(p.fecha_siembra, INTERVAL a.sem WEEK) as nfecha,
	sum(p.plantas) as plantas,
	avg(a.porcentaje) as porcentaje,
	sum(p.plantas * a.porcentaje) as nplantas,
	a.sem
	FROM plane as p
	INNER JOIN addxvariety as a 
	ON a.finca=p.finca AND a.variedad=p.variedad
	INNER JOIN varieties as v 
	ON v.nombre=p.variedad
	$where
	GROUP BY p.finca,p.bloque,p.variedad,p.temporada,p.fecha_siembra
";

$result=$conexion->query($query);

$response = array();

while($row = $result->fetch_assoc()){
    $response[]=$row;
}

echo json_encode($response);

 ?> 