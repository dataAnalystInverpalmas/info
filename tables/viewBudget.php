<?php
//traer conexion
require("../funciones/conexion.php");

$sql="CREATE OR REPLACE VIEW budget AS
          SELECT p.finca, p.bloque, p.producto, p.variedad, p.temporada, MIN(v.ciclo) AS ciclo, MIN(v.codvari) AS codvari,
          MIN(v.color) AS color, MIN(s.cod_temporada) AS cod_temporada, p.tipo_siembra AS siembra, 
          p.fecha_siembra, 'PLANO' AS tipo, SUM(p.plantas) AS plantas
          FROM plane AS p
          LEFT JOIN varieties AS v ON p.variedad = v.nombre
          LEFT JOIN seasons AS s ON p.temporada = s.nombre
          GROUP BY p.finca, p.bloque, p.producto, p.variedad, p.temporada, p.tipo_siembra, p.fecha_siembra
          UNION ALL
          SELECT pf.finca, pf.bloque, pf.producto, pf.variedad, pf.temporada_obj AS temporada, MIN(pf.ciclo) AS ciclo, MIN(v.codvari) AS codvari,
          MIN(v.color) AS color, MIN(s.cod_temporada) AS cod_temporada, pf.tipo AS siembra,
          pf.fecha_siembra, CAST(pf.programa AS CHAR) AS tipo, SUM(pf.plantas) AS plantas
          FROM programf AS pf
          LEFT JOIN varieties AS v ON pf.variedad = v.nombre
          LEFT JOIN seasons AS s ON pf.temporada_obj = s.nombre
          WHERE pf.fecha_siembra >= DATE_ADD(CURDATE(), INTERVAL - DAYOFWEEK(CURDATE())-1 DAY) AND pf.estado = 1
          GROUP BY pf.finca, pf.bloque, pf.producto, pf.variedad, pf.temporada_obj, pf.tipo, pf.fecha_siembra, pf.programa";

$query=$conexion->query($sql);
//echo $sql;

$sql1 = "CREATE OR REPLACE VIEW print_budget AS 
    SELECT p.variedad, p.temporada_obj, p.producto, p.ciclo, p.fecha_siembra, p.fecha_pico, p.finca, p.bloque, f.abreviatura,
     sum(p.plantas) as plantas, ROUND(sum(p.plantas)/960, 0) as ncamas, tem.casa as casa, p.programa, p.estado as estado 
     FROM programf as p 
     LEFT JOIN farms AS f ON f.nombre = p.finca 
     LEFT JOIN seasons AS s ON s.nombre = p.temporada_obj
     LEFT JOIN ( 
        SELECT program.variedad, program.temporada_obj, GROUP_CONCAT(DISTINCT breeders.nombre SEPARATOR ',') as casa 
        FROM program 
        LEFT JOIN breeders ON breeders.id = program.casa_id 
        WHERE program.estado = 1 
        GROUP BY program.variedad, program.temporada_obj 
     ) AS tem ON tem.variedad = p.variedad AND tem.temporada_obj = p.temporada_obj
     WHERE p.plantas > 0 AND p.estado = 1 
     GROUP BY p.variedad, p.temporada_obj, p.producto, p.ciclo, p.fecha_siembra, p.fecha_pico, p.finca, p.bloque, f.abreviatura, tem.casa, p.programa, p.estado 
     ORDER BY p.fecha_siembra, p.producto, p.variedad ASC";

$query1=$conexion->query($sql1);
echo $sql1;

?>