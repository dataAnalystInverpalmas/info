<?php
//traer conexion
require("../funciones/conexion.php");

$sql="CREATE or REPLACE VIEW budget as
          SELECT p.finca,p.bloque,p.producto,p.variedad,p.temporada,v.ciclo,v.codvari,
          v.color,s.cod_temporada,p.tipo_siembra as siembra, 
          p.fecha_siembra,'PLANO' AS tipo,sum(p.plantas) as plantas
          FROM plane as p
          LEFT JOIN varieties AS v
          ON p.variedad=v.nombre
          LEFT JOIN seasons as s
          ON p.temporada=s.nombre
          GROUP BY p.finca,p.bloque,p.variedad,p.temporada,p.fecha_siembra,p.tipo_siembra
          UNION
          SELECT pf.finca,pf.bloque,pf.producto,pf.variedad,pf.temporada_obj,pf.ciclo,v.codvari,
          v.color,s.cod_temporada,pf.tipo as siembra,
          pf.fecha_siembra, pf.programa as tipo,sum(pf.plantas)
          FROM programf as pf
          LEFT JOIN varieties AS v
          ON pf.variedad=v.nombre
          LEFT JOIN seasons as s
          ON pf.temporada_obj=s.nombre
          WHERE pf.fecha_siembra>=DATE_ADD(CURDATE(), INTERVAL - DAYOFWEEK(CURDATE())-1 DAY)
          GROUP BY pf.finca,pf.bloque,pf.variedad,pf.temporada_obj,pf.fecha_siembra,pf.tipo
          ";

$query=$conexion->query($sql);
//echo $sql;

$sql1 = "CREATE OR REPLACE VIEW print_budget AS 
    SELECT p.variedad,p.temporada_obj,p.producto,p.ciclo, p.fecha_siembra,p.fecha_pico,p.finca,p.bloque,f.abreviatura,
     sum(p.plantas) as plantas,ROUND(sum(p.plantas)/960,0) as ncamas, tem.casa as casa, s.año as programa 
     FROM programf as p 
     LEFT JOIN farms AS f ON f.nombre=p.finca 
     LEFT JOIN seasons AS s ON s.nombre=p.temporada_obj
     LEFT JOIN ( SELECT program.variedad,program.temporada_obj,GROUP_CONCAT(breeders.nombre) as casa FROM program left join breeders on breeders.id=program.casa_id group by 1,2 ) AS tem 
     ON tem.variedad=p.variedad AND tem.temporada_obj=p.temporada_obj
     WHERE p.plantas>0 GROUP BY p.variedad,p.temporada_obj,p.producto,p.fecha_siembra,p.ciclo,p.finca,p.bloque 
     ORDER BY p.fecha_temporada,p.fecha_siembra,p.producto,p.variedad ASC";

$query1=$conexion->query($sql1);
echo $sql1;

?>