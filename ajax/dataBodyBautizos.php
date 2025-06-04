<?php
include("../funciones/conexion.php");

//consulta para traer los datos de labores y aplicaciones

$finca = $_POST['finca'] ?? '';
$bloque = $_POST['bloque'] ?? '';
$variedad = $_POST['variedad'] ?? '';
$temporadad = $_POST['temporada'] ?? '';
$fecha_siembra = $_POST['fecha_siembra'] ?? '';

$sql = "
SELECT
    CASE
        WHEN 
            aa.seccion = 1 THEN 'LABORES'
        WHEN 
            aa.seccion = 2 THEN 'APLICACIONES'
    END AS seccion,
    a.tipo,
    a.aplicar,
    a.valor,
    p.fecha_siembra,
    IF(ISNULL(pr.ciclo), v.ciclo, pr.ciclo) AS ciclo,
    aa.calc_conciclo as cc,
    CASE
        WHEN
            p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL')
        THEN
            DATE_ADD(p.fecha_siembra,
                INTERVAL IF(ISNULL(pr.ciclo), v.ciclo, pr.ciclo) WEEK)
        ELSE s.fecha_pico
    END AS fecha_pico,
    CASE
        WHEN
            aa.calc_conciclo IN (0,1,3,5)
        THEN
            DATE_ADD(p.fecha_siembra,
                INTERVAL a.valor DAY)
        WHEN
            aa.calc_conciclo IN (4)
        THEN
            DATE_ADD(p.fecha_siembra,
            INTERVAL a.valor DAY)
        WHEN
            aa.calc_conciclo IN (7)
        THEN
            DATE_ADD(s.fecha_pico,
                INTERVAL a.valor DAY)
        WHEN
            aa.calc_conciclo IN (6,8)
        THEN
            DATE_SUB(s.fecha_pico,
                INTERVAL a.valor DAY)
    END AS  fecha_ejecucion,
    CASE
        WHEN 
            aa.calc_conciclo IN (0,1,3,5)
        THEN
            DATE_FORMAT(DATE_ADD(p.fecha_siembra,
                        INTERVAL a.valor DAY),
                        '%x%v')
        WHEN
            aa.calc_conciclo = 4
        THEN
            CONCAT_WS('/',
                DATE_ADD(p.fecha_siembra,
                    INTERVAL a.valor DAY),
                DATE_FORMAT(DATE_ADD(p.fecha_siembra,
                            INTERVAL a.valor DAY),
                        '%v'))
        WHEN
            aa.calc_conciclo IN (7)
        THEN
            DATE_FORMAT(DATE_ADD(s.fecha_pico,
                        INTERVAL a.valor DAY),
                    '%x%v')
        WHEN
            aa.calc_conciclo IN (6,8)
        THEN
            DATE_FORMAT(DATE_SUB(s.fecha_pico,
                        INTERVAL a.valor DAY),
                    '%x%v')
    END AS  fecha_formato
FROM
    informes.plane AS p
        LEFT JOIN
    informes.seasons AS s ON p.temporada = s.nombre
        LEFT JOIN
    informes.varieties AS v on p.variedad = v.nombre
        LEFT JOIN
    informes.arrangements AS a on p.finca = a.finca
        AND p.variedad = a.variedad
        LEFT JOIN
    (SELECT
        variedad, programa, pico as ciclo
    FROM
        informes.program
    GROUP BY 1,2,3) AS pr ON pr.variedad = p.variedad
        AND pr.programa = s.año
        LEFT JOIN
    informes.arrangement AS  aa on a.tipo = aa.tipo
        AND a.aplicar = aa.aplicar
    WHERE
        p.finca = ?
        AND p.bloque = ?
        AND p.variedad = ?
        AND p.temporada = ?
        AND p.fecha_siembra = ?
        AND aa.seccion IS NOT NULL
    GROUP BY a.tipo, a.aplicar, a.valor, p.fecha_siembra, aa.seccion, v.ciclo, aa.calc_conciclo, s.fecha_pico
    ORDER BY aa.seccion ASC, p.bloque ASC, aa.orden, a.valor ASC
";

$stmt = $conexion->prepare($sql);
//valdia los datos en caso contrario muestra error
if (!$stmt){
    die("Error en prepare(): " .$conexion->error);
}

$stmt->bind_param("sssss", $finca, $bloque, $variedad, $temporadad, $fecha_siembra); //por cada parametro adiconal que se adicione al where se debe poner una s = "STRING" para que pueda leer el dato
$stmt->execute();

$result = $stmt->get_result();

$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = $row;
}


echo json_encode($data);