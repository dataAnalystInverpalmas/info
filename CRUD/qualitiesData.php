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
		  $where .= "and q.producto='$flor' and q.fecha BETWEEN '$fini' and '$ffin 23:59:59' ";
	  }elseif ($fini != "" & $ffin != "" and $flor == ""){
		  $where .= "and q.fecha BETWEEN '$fini' and '$ffin 23:59:59' ";
	  }elseif ($flor != ""){
		  $where .= "and q.producto='$flor'";
	  }else{
		  $where .= "";
	  }
  }

$query="
SELECT q.fecha,q.producto,q.nombre,consulta.causas,noselecto.causas as no_selecto,
	round(sum(q.SEL)/sum(q.valor)*100,0) as SEL,
	round(sum(q.FAN)/sum(q.valor)*100,0) as FAN,
	round(sum(q.STD)/sum(q.valor)*100,0) as STD,
	round(sum(q.SHR)/sum(q.valor)*100,0) as SHR,
	round(sum(q.NAL)/sum(q.valor)*100,0) as NAL
FROM ( 
	SELECT b.nombre,a.variedad,a.fecha,b.producto,sum(a.valor) as valor,
	sum(CASE WHEN c.grado='SELECT' OR c.grado='100' OR c.grado='090' OR c.grado='080' OR c.grado='070' THEN a.valor  ELSE 0 END) AS SEL,
	sum(CASE WHEN c.grado='FANCY' OR c.grado='060' THEN a.valor  ELSE 0 END) AS FAN,
	sum(CASE WHEN c.grado='STANDARD' OR c.grado='050' THEN a.valor  ELSE 0 END) AS STD,
	sum(CASE WHEN c.grado='SHORT' OR c.grado='040' OR c.grado='030' THEN a.valor  ELSE 0 END) AS SHR,
	sum(CASE WHEN c.grado='NACIONAL' THEN a.valor  ELSE 0 END) AS NAL
    FROM table_qualities as a
	LEFT JOIN varieties as b ON b.id=a.variedad
	LEFT JOIN grades as c ON c.id=a.grado
    group by b.nombre,a.fecha
    ) as q
LEFT JOIN 
(select * from (select nombre,group_concat(abr,':',round(round(aporte,2)*100,0),'%') as causas from
(select vari.nombre,cn.abr,sum(b.valor/b.muestra)/tot.total as aporte
from table_qualities as a
inner join table_nalcauses as b
on a.id=b.qualities_id
left join varieties as vari
on vari.id=a.variedad
left join causes_nal cn
on cn.id=b.causa
left join (
select vari.nombre,sum(b.valor/b.muestra) as total
from table_qualities as a
inner join table_nalcauses as b
on a.id=b.qualities_id
left join varieties as vari
on vari.id=a.variedad
left join causes_nal cn
on cn.id=b.causa
where a.variedad=vari.id and a.fecha BETWEEN '$fini' and '$ffin 23:59:59' 
group by vari.nombre
) as tot
on tot.nombre=vari.nombre
where a.variedad=vari.id and a.fecha BETWEEN '$fini' and '$ffin 23:59:59' 
group by vari.nombre,cn.abr) as consulta
group by consulta.nombre) as consulta) as consulta
on consulta.nombre=q.nombre
LEFT JOIN 
(select * from (select nombre,group_concat(abr,':',round(round(aporte,2)*100,0),'%') as causas from
(select vari.nombre,cn.abr,sum(b.valor/b.muestra)/tot.total as aporte
from table_qualities as a
inner join table_causes_no_select as b
on a.id=b.qualities_id
left join varieties as vari
on vari.id=a.variedad
left join causes_no_select cn
on cn.id=b.causa
left join (
select vari.nombre,sum(b.valor/b.muestra) as total
from table_qualities as a
inner join table_causes_no_select as b
on a.id=b.qualities_id
left join varieties as vari
on vari.id=a.variedad
left join causes_no_select cn
on cn.id=b.causa
where a.variedad=vari.id and a.fecha BETWEEN '$fini' and '$ffin 23:59:59' 
group by vari.nombre
) as tot
on tot.nombre=vari.nombre
where a.variedad=vari.id and a.fecha BETWEEN '$fini' and '$ffin 23:59:59' 
group by vari.nombre,cn.abr) as consulta
group by consulta.nombre) as consulta) as noselecto
on noselecto.nombre=q.nombre
$where
GROUP BY q.nombre,consulta.nombre
";
/* $query="
SELECT fecha,producto,nombre,round(avg(SEL),1) as SEL,round(avg(FAN),1) as FAN,round(avg(STD),1) as STD,round(avg(SHR),1) as SHR,round(avg(NAL),1) as NAL
FROM ( SELECT b.nombre,a.variedad,a.fecha,b.producto,
	round(sum(CASE WHEN c.grado='SELECT' OR c.grado='100' OR c.grado='090' OR c.grado='080' OR c.grado='070' THEN a.valor  ELSE 0 END)/sum(a.valor)*100,1) AS SEL,
	round(sum(CASE WHEN c.grado='FANCY' OR c.grado='060' THEN a.valor  ELSE 0 END)/sum(a.valor)*100,1) AS FAN,
	round(sum(CASE WHEN c.grado='STANDARD' OR c.grado='050' THEN a.valor  ELSE 0 END)/sum(a.valor)*100,1) AS STD,
	round(sum(CASE WHEN c.grado='SHORT' OR c.grado='040' OR c.grado='030' THEN a.valor  ELSE 0 END)/sum(a.valor)*100,1) AS SHR,
	round(sum(CASE WHEN c.grado='NACIONAL' THEN a.valor  ELSE 0 END)/sum(a.valor)*100,1) AS NAL
    FROM table_qualities as a
	LEFT JOIN varieties as b ON b.id=a.variedad
	LEFT JOIN grades as c ON c.id=a.grado
    group by b.nombre,a.fecha
    ) as a  
	$where   
GROUP BY nombre
"; */
$result=$conexion->query($query);

$response = array();

while($row = $result->fetch_assoc()){
    $response[]=$row;
}

echo json_encode($response);
?> 


