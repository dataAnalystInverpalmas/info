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
		$where .= "and v.producto='$flor' and fecha_florero BETWEEN '$fini' and '$ffin' ";
	}elseif ($fini != "" & $ffin != ""){
		$where .= "and fecha_florero BETWEEN '$fini' and '$ffin' ";
	}elseif ($flor != ""){
		$where .= "and v.producto='$flor'";
	}else{
		$where .= "";
	}
}

$query="
SELECT 
    f.id,
    f.fecha_corte,
    f.fecha_florero,
    f.pcorte,
    f.pempaque,
    f.pflorero,
    f.grupo_descripcion,
    k.nombre AS tipo,
    v.nombre AS variedad,
    o.nombre AS origen,
    f.simulacion AS viaje,
    f.guarde_granel AS granel,
    f.observacion,
    fin.vida AS vida,
    eval.puntaje AS puntaje,
    subquery.CONSISTENCIA_COLOR,
    subquery.PETALOS,
    subquery.VELOCIDAD_APERTURA,
    subquery.FORMA_APERTURA,
    subquery.TALLO,
    subquery.FOLLAJE,
    subquery.FINAL_OPTIMO,
    subquery.NO_CONFORME,
   if(subquery_incf.inconformidad is null, 'No hay inconformidades', subquery_incf.inconformidad) as inconformidad_florero
FROM
    informes.flower_vases AS f
        LEFT JOIN
    informes.fv_causes AS c ON f.id = c.fv_id
        LEFT JOIN
    informes.fv_causes_items AS ci ON ci.id = c.causes_item_id
        LEFT JOIN
    informes.fv_evaluations AS e ON e.id = c.fv_id
        LEFT JOIN
    informes.fv_evaluation_items AS ei ON ei.id = e.evaluation_item_id
        LEFT JOIN
    informes.varieties AS v ON v.id = f.variedad_id
        LEFT JOIN
    informes.fv_origens AS o ON o.id = f.origen_id
        LEFT JOIN
    informes.fv_kinds AS k ON k.id = f.tipo_id
        LEFT JOIN
    (SELECT 
        fv_id AS id,
            ROUND(SUM(ca.dias / 15 * ca.cantidad / fl.tallos) * 15) AS vida
    FROM
        informes.fv_causes AS ca
    INNER JOIN informes.flower_vases AS fl ON fl.id = ca.fv_id
    GROUP BY fv_id) AS fin ON fin.id = f.id
        LEFT JOIN
    (SELECT 
        fv_id, ROUND(AVG(t2.puntaje) / 3 * 100) AS puntaje
    FROM
        informes.fv_evaluations AS t1
    INNER JOIN informes.fv_evaluation_items AS t2 ON t2.id = t1.evaluation_item_id
    GROUP BY 1) AS eval ON eval.fv_id = f.id
        LEFT JOIN
    (SELECT 
        t.id,
            fcid,
            cid,
            GROUP_CONCAT(t.inconformidad) AS inconformidad
    FROM
        (SELECT 
        f.id,
            c.id AS cid,
            fc.id AS fcid,
            CONCAT_WS(' - ', CONCAT(fc.nombre, ' ', CONCAT(ROUND(AVG(c.cantidad) / SUM(f.tallos) * 100, 0), '%')), ROUND(AVG(c.dias), 0)) AS inconformidad
    FROM
        informes.flower_vases AS f
    LEFT JOIN informes.varieties AS v ON v.id = f.variedad_id
    LEFT JOIN informes.fv_causes AS c ON c.fv_id = f.id
    LEFT JOIN informes.fv_causes_items AS fc ON fc.id = c.causes_item_id
    WHERE
        fc.id <> 1
    GROUP BY c.id , fc.id) AS t
    GROUP BY t.id   desc) AS subquery_incf ON subquery_incf.id = f.id
        LEFT JOIN
    (SELECT 
        f.id,
            eval.CONSISTENCIA_COLOR,
            eval.PETALOS,
            eval.VELOCIDAD_APERTURA,
            eval.FORMA_APERTURA,
            eval.TALLO,
            eval.FOLLAJE,
            t.sum_incof,
            (CASE
                WHEN fc.id = 1 THEN CONCAT(ROUND(AVG(c.cantidad) / SUM(f.tallos) * 100, 0), '%')
                ELSE 0
            END) FINAL_OPTIMO,
            (CASE
                WHEN fc.id != 1 THEN CONCAT(sum_incof, '%')
                ELSE CONCAT(if(sum_incof is null, 0 ,sum_incof ), '%') 
            END) AS NO_CONFORME
    FROM
        informes.flower_vases AS f
    LEFT JOIN informes.varieties AS v ON v.id = f.variedad_id
    LEFT JOIN informes.fv_causes AS c ON c.fv_id = f.id
    LEFT JOIN (SELECT 
        f.id,
            SUM(ROUND(c.cantidad / f.tallos * 100, 0)) AS sum_incof
    FROM
        informes.flower_vases AS f
    LEFT JOIN informes.fv_causes AS c ON c.fv_id = f.id
    LEFT JOIN informes.fv_causes_items AS fc ON fc.id = c.causes_item_id
    WHERE
        fc.id <> 1
    GROUP BY c.fv_id) AS t ON t.id = f.id
    LEFT JOIN informes.fv_causes_items AS fc ON fc.id = c.causes_item_id
    LEFT JOIN (SELECT 
        fv_id,
            items.nombre,
            puntaje,
            SUM(CASE
                WHEN items.nombre LIKE '%CONSISTENCIA EN COLOR%' THEN puntaje
                ELSE 0
            END) AS CONSISTENCIA_COLOR,
            SUM(CASE
                WHEN items.nombre LIKE '%PETALOS%' THEN puntaje
                ELSE 0
            END) PETALOS,
            SUM(CASE
                WHEN items.nombre LIKE '%VELOCIDAD APERTURA%' THEN puntaje
                ELSE 0
            END) AS VELOCIDAD_APERTURA,
            SUM(CASE
                WHEN items.nombre LIKE '%FORMA APERTURA%' THEN puntaje
                ELSE 0
            END) AS FORMA_APERTURA,
            SUM(CASE
                WHEN items.nombre LIKE '%TALLO%' THEN puntaje
                ELSE 0
            END) AS TALLO,
            SUM(CASE
                WHEN items.nombre LIKE '%FOLLAJE%' THEN puntaje
                ELSE 0
            END) AS FOLLAJE
    FROM
        informes.fv_evaluations AS eval
    LEFT JOIN informes.fv_evaluation_items AS items ON items.id = eval.evaluation_item_id
    GROUP BY fv_id
    ORDER BY fv_id DESC) AS eval ON eval.fv_id = f.id
    GROUP BY c.id , fc.id
     order by fc.id desc) AS subquery ON subquery.id = f.id
$where
GROUP BY f.fecha_corte , f.fecha_florero , f.pcorte , f.pempaque , f.pflorero , f.grupo_descripcion , k.nombre , v.nombre , o.nombre , f.simulacion , f.guarde_granel , f.observacion
ORDER BY f.id DESC
";
$result=$conexion->query($query);

$response = array();

while($row = $result->fetch_assoc()){
    $response[]=$row;
}

echo json_encode($response);

 ?> 