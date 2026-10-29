<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

//consutla para devolver todos los encabezados de la  son los datos que van en la parte superior del bautizo

$finca = $_POST['finca'] ?? '';
$bloque = $_POST['bloque'] ?? '';
$variedad = $_POST['variedad'] ?? '';
$temporada = $_POST['temporada'] ?? '';
$tipo_siembra = $_POST['tipo_siembra'] ?? '';

$sql = "
    SELECT 
        a.finca,
        a.bloque,
        a.temporada,
	concat(c.fiesta, right(year(c.fecha_pico), 2)) as fiesta,
        a.variedad,
        a.producto,
        a.tabla,
        a.nave,
        a.cama,
        COUNT(a.cama) AS camas,
        SUM(a.plantas) AS plantas,
        53.30 AS plantasm2,
        c.fecha_pico,
        DATE_FORMAT(MAX(a.fecha_siembra), '%x%v') AS fecha_siembra_yyww,
        CASE
            WHEN a.tipo_siembra IN ('ADICIONAL' , 'REEMPLAZO', '1PICO') THEN 1
            WHEN a.tipo_siembra IN ('CONTINUA' , 'PICO') THEN 2
        END AS pico,
        IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) AS ciclo,
        a.tipo_siembra,
        a.cosecha_reem,
        a.variedad_reem,
        CASE WHEN b.variedad = a.variedad and b.temporada_obj = a.temporada   then concat_ws('/',
        b.fecha_siembra, DATE_FORMAT(b.fecha_siembra, '%v'))
        else
        CONCAT_WS('/',
                DATE_ADD(DATE_SUB(c.fecha_pico,
                        INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) WEEK),
                    INTERVAL (5 - DAYOFWEEK(DATE_SUB(c.fecha_pico,
                                INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) WEEK))) DAY),
                DATE_FORMAT(DATE_ADD(DATE_SUB(c.fecha_pico,
                                INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) WEEK),
                            INTERVAL (5 - DAYOFWEEK(DATE_SUB(c.fecha_pico,
                                        INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) WEEK))) DAY),
                        '%v'))end AS fecha_siembra_t,
        CONCAT_WS('/',
                MAX(a.fecha_siembra),
                DATE_FORMAT(MAX(a.fecha_siembra), '%v')) AS fecha_siembra_r,
        CONCAT_WS('-',
                DATE_FORMAT(DATE_ADD(a.fecha_siembra,
                            INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) - 3 WEEK),
                        '%v'),
                DATE_FORMAT(DATE_ADD(a.fecha_siembra,
                            INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) - 2 WEEK),
                        '%v'),
                DATE_FORMAT(DATE_ADD(a.fecha_siembra,
                            INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) - 1 WEEK),
                        '%v'),
                DATE_FORMAT(DATE_ADD(a.fecha_siembra,
                            INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) WEEK),
                        '%v')) AS semana_pico_t,
        '' AS semana_pico_r,
        e.nombre AS casa_comercial,
        a.origen,
        a.tipo_suelo,
        a.nmanguera,
        a.ferradica
    FROM
        plane AS a
            LEFT JOIN
        seasons AS c ON c.nombre = a.temporada
            LEFT JOIN
        program AS b ON a.variedad = b.variedad
            AND c.nombre = b.temporada_obj
            AND c.año = b.programa
            AND b.estado = 1
            AND b.ciclo = CASE
            WHEN a.tipo_siembra IN ('ADICIONAL' , 'REEMPLAZO', '1PICO') THEN 1
            WHEN a.tipo_siembra IN ('CONTINUA' , 'PICO') THEN 2
        END
            LEFT JOIN
        varieties AS d ON a.variedad = d.nombre
            LEFT JOIN
        breeders AS e ON d.casa_comercial = e.id
            LEFT JOIN
        (SELECT 
            variedad, temporada_obj, pico AS ciclo
        FROM
            program
        WHERE program.estado=1
        GROUP BY variedad, temporada_obj, pico) AS pr ON pr.variedad = a.variedad
            AND pr.temporada_obj = c.nombre
    WHERE a.finca = ? and a.bloque = ? and a.variedad = ? and a.temporada = ? and a.tipo_siembra = ?
    GROUP BY a.finca,
    a.bloque,
    a.temporada,
    a.variedad,
    a.producto,
    a.fecha_siembra,
    a.tabla,
    a.nave,
    a.cama,
    c.fecha_pico,
    d.ciclo,
    pr.ciclo,
    b.variedad,
    b.temporada_obj,
    b.fecha_siembra,
    b.ciclo,
    b.pico,
    e.nombre,
    a.origen,
    a.tipo_suelo,
    a.nmanguera,
    a.ferradica,
    a.tipo_siembra,
    a.cosecha_reem,
    a.variedad_reem,
    a.pico
";

$stmt = $conexion->prepare($sql);

//valida el resultado si no muestrar el error del porque no se obtienen los datos 
if (!$stmt) {
    die("Error en prepare(): " . $conexion->error); 
}

$stmt->bind_param("sssss", $finca, $bloque, $variedad, $temporada, $tipo_siembra); //por cada parametro adicional que se  adicione al where se debe poner una s  = 'String' y se pueda leer el dato
$stmt->execute();

$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
