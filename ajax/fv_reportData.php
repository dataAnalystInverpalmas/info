<?php
if (is_file("funciones/conexion.php")){
	  include ("funciones/conexion.php");
	}
	else {
	  include ("../funciones/conexion.php");
  }
$where = "WHERE 1 ";
$params = [];
$types = "";

if ($_POST){
	$fini = $_POST['fini'] ?? '';
	$ffin = $_POST['ffin'] ?? '';
	$flor = $_POST['flor'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');
    $granel = $_POST['granel'] ?? '';

    if ($flor !== "") {
        $where .= "AND v.nombre = ? ";
        $types .= "s";
        $params[] = $flor;
    }

    if ($descripcion !== "") {
        $where .= "AND f.grupo_descripcion LIKE ? ";
        $types .= "s";
        $params[] = '%' . $descripcion . '%';
    }

    if ($granel !== "") {
        $where .= "AND f.guarde_granel = ? ";
        $types .= "i";
        $params[] = (int)$granel;
    }

    if ($fini !== "" && $ffin !== "") {
        $where .= "AND f.fecha_florero BETWEEN ? AND ? ";
        $types .= "ss";
        $params[] = $fini;
        $params[] = $ffin;
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
    flower_vases AS f
        LEFT JOIN
    fv_causes AS c ON f.id = c.fv_id
        LEFT JOIN
    fv_causes_items AS ci ON ci.id = c.causes_item_id
        LEFT JOIN
    fv_evaluations AS e ON e.fv_id = f.id
        LEFT JOIN
    fv_evaluation_items AS ei ON ei.id = e.evaluation_item_id
        LEFT JOIN
    varieties AS v ON v.id = f.variedad_id
        LEFT JOIN
    fv_origens AS o ON o.id = f.origen_id
        LEFT JOIN
    fv_kinds AS k ON k.id = f.tipo_id
        LEFT JOIN
    (SELECT 
        fv_id AS id,
            ROUND(SUM(ca.dias / 15 * ca.cantidad / fl.tallos) * 15) AS vida
    FROM
        fv_causes AS ca
    INNER JOIN flower_vases AS fl ON fl.id = ca.fv_id
    GROUP BY fv_id) AS fin ON fin.id = f.id
        LEFT JOIN
    (SELECT 
        fv_id, ROUND(AVG(t2.puntaje) / 3 * 100) AS puntaje
    FROM
        fv_evaluations AS t1
    INNER JOIN fv_evaluation_items AS t2 ON t2.id = t1.evaluation_item_id
    GROUP BY 1) AS eval ON eval.fv_id = f.id
        LEFT JOIN
    (SELECT 
        t.id,
        GROUP_CONCAT(t.inconformidad) AS inconformidad
    FROM
        (SELECT 
            f.id,
            CONCAT_WS(' - ', CONCAT(fc.nombre, ' ', CONCAT(ROUND(AVG(c.cantidad) / NULLIF(SUM(f.tallos), 0) * 100, 0), '%')), ROUND(AVG(c.dias), 0)) AS inconformidad
        FROM
            flower_vases AS f
        LEFT JOIN varieties AS v ON v.id = f.variedad_id
        LEFT JOIN fv_causes AS c ON c.fv_id = f.id
        LEFT JOIN fv_causes_items AS fc ON fc.id = c.causes_item_id
        WHERE
            fc.id <> 1
        GROUP BY f.id, c.id, fc.id, fc.nombre) AS t
    GROUP BY t.id) AS subquery_incf ON subquery_incf.id = f.id
        LEFT JOIN
    (SELECT 
        f.id,
            MAX(eval.CONSISTENCIA_COLOR) AS CONSISTENCIA_COLOR,
            MAX(eval.PETALOS) AS PETALOS,
            MAX(eval.VELOCIDAD_APERTURA) AS VELOCIDAD_APERTURA,
            MAX(eval.FORMA_APERTURA) AS FORMA_APERTURA,
            MAX(eval.TALLO) AS TALLO,
            MAX(eval.FOLLAJE) AS FOLLAJE,
            CONCAT(COALESCE(ROUND(SUM(CASE WHEN fc.id = 1 THEN c.cantidad ELSE 0 END) / 
                NULLIF(SUM(CASE WHEN fc.id = 1 THEN f.tallos ELSE 0 END), 0) * 100, 0), 0), '%') AS FINAL_OPTIMO,
            CONCAT(COALESCE(MAX(t.sum_incof), 0), '%') AS NO_CONFORME
    FROM
        flower_vases AS f
    LEFT JOIN fv_causes AS c ON c.fv_id = f.id
    LEFT JOIN (SELECT 
        f.id,
            SUM(ROUND(c.cantidad / NULLIF(f.tallos, 0) * 100, 0)) AS sum_incof
    FROM
        flower_vases AS f
    LEFT JOIN fv_causes AS c ON c.fv_id = f.id
    LEFT JOIN fv_causes_items AS fc ON fc.id = c.causes_item_id
    WHERE
        fc.id <> 1
    GROUP BY f.id) AS t ON t.id = f.id
    LEFT JOIN fv_causes_items AS fc ON fc.id = c.causes_item_id
    LEFT JOIN (SELECT 
        fv_id,
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
        fv_evaluations AS eval
    LEFT JOIN fv_evaluation_items AS items ON items.id = eval.evaluation_item_id
    GROUP BY fv_id) AS eval ON eval.fv_id = f.id
    GROUP BY f.id) AS subquery ON subquery.id = f.id
$where
GROUP BY f.id , f.fecha_corte , f.fecha_florero , f.pcorte , f.pempaque , f.pflorero , f.grupo_descripcion , k.nombre , v.nombre , o.nombre , f.simulacion , f.guarde_granel , f.observacion
ORDER BY f.id DESC
";
$stmt = $conexion->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$response = array();

while($row = $result->fetch_assoc()){
    $response[]=$row;
}

echo json_encode($response);

 ?> 
