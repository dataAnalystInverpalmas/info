<?php
include("../funciones/conexion.php");

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
    a.variedad,
    a.producto,
    a.tabla,
    a.nave,
    a.cama,
    COUNT(a.cama) AS camas,
    SUM(a.plantas) AS plantas,
    53.30 AS plantasm2,
    c.fecha_pico,
    CASE
        WHEN a.tipo_siembra IN ('ADICIONAL' , 'REEMPLAZO', '1PICO') THEN 1
        WHEN a.tipo_siembra IN ('CONTINUA' , 'PICO') THEN 2
    END AS pico,
    IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) AS ciclo,
    a.tipo_siembra,
    a.cosecha_reem,
    a.variedad_reem,
    CONCAT_WS('/',
            DATE_ADD(DATE_SUB(c.fecha_pico,
                    INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) WEEK),
                INTERVAL (5 - DAYOFWEEK(DATE_SUB(c.fecha_pico,
                            INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) WEEK))) DAY),
            DATE_FORMAT(DATE_ADD(DATE_SUB(c.fecha_pico,
                            INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) WEEK),
                        INTERVAL (5 - DAYOFWEEK(DATE_SUB(c.fecha_pico,
                                    INTERVAL IF(ISNULL(pr.ciclo), d.ciclo, pr.ciclo) WEEK))) DAY),
                    '%v')) AS fecha_siembra_t,
    CONCAT_WS('/',
            MAX(a.fecha_siembra),
            DATE_FORMAT(a.fecha_siembra, '%v')) AS fecha_siembra_r,
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
    informes.plane AS a
        LEFT JOIN
    informes.seasons AS c ON c.nombre = a.temporada
        LEFT JOIN
    informes.program AS b ON a.variedad = b.variedad
        AND c.nombre = b.temporada_obj
        AND c.año = b.programa
        AND b.ciclo = CASE
            WHEN a.tipo_siembra IN ('ADICIONAL' , 'REEMPLAZO', '1PICO') THEN 1
            WHEN a.tipo_siembra IN ('CONTINUA' , 'PICO') THEN 2
        END
        LEFT JOIN
    informes.varieties AS d ON a.variedad = d.nombre
        LEFT JOIN
    informes.breeders AS e ON d.casa_comercial = e.id
        LEFT JOIN
    (SELECT 
        variedad, programa, pico AS ciclo
    FROM
        informes.program
    GROUP BY 1 , 2 , 3) AS pr ON pr.variedad = a.variedad
        AND pr.programa = c.año
    where a.finca = ? and a.bloque = ? and a.variedad = ? and a.temporada = ? and a.tipo_siembra = ?
GROUP BY a.finca ,
 a.bloque ,
 a.temporada ,
 a.variedad ,
 a.producto ,
 a.tabla ,
 a.nave ,
 a.cama ,
 b.ciclo ,
 b.pico ,
 e.nombre ,
 a.origen ,
 a.tipo_suelo ,
 a.nmanguera ,
 a.ferradica,
 IF(a.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'), a.pico, a.tipo_siembra),
 IF(a.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'), a.cosecha_reem, a.tipo_siembra),
 IF(a.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'), a.variedad_reem, a.tipo_siembra)
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
