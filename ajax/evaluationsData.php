<?php
if (is_file("funciones/conexion.php")){
	  include ("funciones/conexion.php");
	}
	else {
	  include ("../funciones/conexion.php");
  }

  $where = "WHERE 1 ";

  if ($_POST){
	  $fini=$_POST['fini'];
	  $ffin=$_POST['ffin'];
	  $flor=$_POST['flor'];
  
	  if ($fini != "" & $ffin != "" and $flor != ""){
		  $where .= "and v.producto='$flor' and o.date BETWEEN '$fini' and '$ffin 23:59:59' ";
	  }elseif ($fini != "" & $ffin != "" and $flor == ""){
		  $where .= "and o.date BETWEEN '$fini' and '$ffin 23:59:59' ";
	  }elseif ($flor != ""){
		  $where .= "and v.producto='$flor'";
	  }else{
		  $where .= "";
	  }
  }

$query="SELECT * FROM 
(
SELECT o.date as fecha,v.nombre as variedad,v.producto,oc.comment as comentario,e.nombre as evaluador,
sum(case when eg.nombre='COLOR' then od.value else 0 end) as color,
sum(case when eg.nombre='CICLO' then od.value else 0 end) as ciclo,
sum(case when eg.nombre='PRODUCTIVIDAD' then od.value else 0 end) as productividad,
sum(case when eg.nombre='TAMAÑO CABEZA' then od.value else 0 end) as tcabeza,
sum(case when eg.nombre='PETALO FUERTE' then od.value else 0 end) as pfuerte,
sum(case when eg.nombre='FORMA APERTURA' then od.value else 0 end) as fapertura,
sum(case when eg.nombre='GROSOR TALLO' then od.value else 0 end) as gtallo,
sum(case when eg.nombre='LONGITUD' then od.value else 0 end) as longitud,
sum(case when eg.nombre='RESISTENCIA FUSARIUM' then od.value else 0 end) as rfusarium,
sum(case when eg.nombre='SUCEPTIBILIDAD ENFERMEDADES' then od.value else 0 end) as senfermedades,
sum(case when eg.nombre='FOLLAJE' then od.value else 0 end) as follaje,
sum(case when eg.nombre='PUNTOS SPRAY' then od.value else 0 end) as pspray,
sum(case when eg.nombre='PUNTOS MINI' then od.value else 0 end) as pmini
FROM orders_details AS od
LEFT JOIN orders AS o
on o.id=od.order_id
LEFT JOIN varieties as v
ON v.id=od.variety_id
LEFT JOIN eval_goals as eg
ON eg.id=od.eval_goals_id
LEFT JOIN evaluators as e
ON e.id=o.evaluator_id
LEFT JOiN order_comments as oc
ON oc.orders_id=o.id AND oc.variety_id=od.variety_id
$where
GROUP BY v.nombre,e.nombre
) as eval
";

$result=$conexion->query($query);

$response = array();

while($row = $result->fetch_assoc()){
    $response[]=$row;
}

echo json_encode($response);
?> 