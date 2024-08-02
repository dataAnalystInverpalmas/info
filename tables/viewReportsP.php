<?php
				//traer conexion
require("../funciones/conexion.php");

$sqlT="TRUNCATE TABLE viewsowing";
$conexion->query($sqlT);
/////////////////////////////////////////PERDIDAS,INVENTARIO,RECIBIDO/////////////////////////////////////
$queryLabores=" INSERT INTO viewsowing 
	SELECT a.fecha,a.ubicacion AS finca,a.bloque,v.producto,a.variedad,b.año,
	a.temporada,a.tipo,b.fecha_pico,'' as tipo_siembra,'' as maquilador,sum(a.valor) as valor 
	FROM labors_sowing as a 
	LEFT JOIN seasons as b on b.nombre=a.temporada
	LEFT JOIN varieties AS v ON v.nombre=a.variedad
	WHERE a.valor>0 and v.producto IN ('CLAVEL','MINICLAVEL')
	GROUP by a.fecha,a.variedad,a.temporada,a.tipo,b.fecha_pico,a.ubicacion,a.bloque
";
//////////////////////////////////////TEO SIEMBRAS/////////////////////////////////////////////////////////
$queryTeoSiembra=" INSERT INTO viewsowing 
	SELECT a.fecha_siembra,a.finca,a.bloque as bloque,v.producto,a.variedad,b.año,
	a.temporada_obj,'TEO_SIEMBRA' AS tipo,b.fecha_pico,a.tipo as tipo_siembra,'' as maquilador,sum(a.plantas) as valor 
	FROM programf as a 
	LEFT JOIN seasons as b on b.nombre=a.temporada_obj
	LEFT JOIN varieties AS v ON v.nombre=a.variedad
	WHERE a.plantas>0 and v.producto IN ('CLAVEL','MINICLAVEL')
	GROUP by a.fecha_siembra,a.variedad,a.temporada_obj,a.tipo,b.fecha_pico,a.finca,a.bloque
";
/////////////////////////////////////////TEO SIEMBRAS//////////////////////////////////////////////////////
$queryTeoSiembraAdd=" INSERT INTO viewsowing 
	SELECT a.fecha_siembra,a.finca,a.bloque as bloque,v.producto,a.variedad,b.año,
	a.temporada_obj,'TEO_SIEMBRA' AS tipo,b.fecha_pico,a.tipo as tipo_siembra,'' as maquilador,sum(a.plantas) as valor 
	FROM program_add as a 
	LEFT JOIN seasons as b on b.nombre=a.temporada_obj
	LEFT JOIN varieties AS v ON v.nombre=a.variedad
	WHERE a.plantas>0 and v.producto IN ('CLAVEL','MINICLAVEL')
	GROUP by a.fecha_siembra,a.variedad,a.temporada_obj,a.tipo,b.fecha_pico,a.finca,a.bloque
";
//////////////////////////////////////TEO ENSARTE/////////////////////////////////////////////////////////
$queryTeoEnsarte=" INSERT INTO viewsowing 
	SELECT a.fecha_ensarte,'' AS finca,'0' as bloque,v.producto,a.variedad,b.año,
	a.temporada_obj,'TEO_ENSARTE' AS tipo,b.fecha_pico,a.tipo as tipo_siembra,br.nombre as maquilador,sum(a.plantas) as valor
	FROM program as a
	LEFT JOIN seasons as b on b.nombre=a.temporada_obj
	LEFT JOIN varieties AS v ON v.nombre=a.variedad
	LEFT JOIN breeders AS br ON br.id=a.casa_id
	WHERE a.plantas>0 and v.producto IN ('CLAVEL','MINICLAVEL')
	GROUP by a.fecha_ensarte,a.variedad,a.temporada_obj,a.tipo,b.fecha_pico,br.nombre
";
////////////////////////////////////////TEO COSECHA///////////////////////////////////////////////////////
$queryTeoCosecha=" INSERT INTO viewsowing 
	SELECT a.fecha_cosecha,'' AS finca,'0' as bloque,v.producto,a.variedad,b.año,
	a.temporada_obj,'TEO_COSECHA' AS tipo,b.fecha_pico,a.tipo as tipo_siembra,br.nombre as maquilador,sum(a.plantas) as valor 
	FROM program as a 
	LEFT JOIN seasons as b on b.nombre=a.temporada_obj
	LEFT JOIN varieties AS v ON v.nombre=a.variedad
	LEFT JOIN breeders AS br ON br.id=a.casa_id
	WHERE a.plantas>0 and v.producto IN ('CLAVEL','MINICLAVEL')
	GROUP BY a.fecha_cosecha,a.variedad,a.temporada_obj,a.tipo,b.fecha_pico,br.nombre
";
/////////////////////////////////////SIEMBRA REAL////////////////////////////////////////////////
$querySiembra=" INSERT INTO viewsowing 
	SELECT a.fecha_siembra,a.finca,a.bloque as bloque,v.producto,a.variedad,b.año,
	a.temporada,'SIEMBRA' as tipo,b.fecha_pico,a.tipo_siembra as tipo_siembra,maquilador,sum(a.plantas) as valor
	FROM hplane as a 
	LEFT JOIN seasons as b on b.nombre=a.temporada
	LEFT JOIN varieties AS v ON v.nombre=a.variedad
	WHERE a.plantas>0 and v.producto IN ('CLAVEL','MINICLAVEL')
	GROUP BY a.fecha_siembra,a.variedad,a.temporada,b.fecha_pico,a.finca,a.bloque,a.tipo_siembra
";

//Actualizar vista de perdidas
////////////////////////////////////////////////////////////////////////////////////////
$queryEnsartes="INSERT INTO viewsowing
	SELECT a.fecha_ensarte_r as fecha,'PROPAGACION' as finca,'0' as bloque,a.producto,a.variedad,a.programa,a.temporada_obj,
	'ENSARTE' AS tipo,
	a.fecha_pico,a.tipo as tipo_siembra,br.nombre as maquilador,a.esquejes_ensarte as valor from program as a
	LEFT JOIN breeders AS br ON br.id=a.casa_id
";

$queryCosechas="INSERT INTO viewsowing
	SELECT a.fecha_cosecha_r as fecha,'PROPAGACION' as finca,'0' as bloque, a.producto,a.variedad,a.programa,a.temporada_obj,'COSECHA' AS tipo,
	a.fecha_pico,a.tipo as tipo_siembra,br.nombre as maquilador,a.esquejes_cosecha as valor from program as a
	LEFT JOIN breeders AS br ON br.id=a.casa_id
";

/////////////////////////////////////////////////////////////////////////////////////////////////
$queryPerdidasP="INSERT INTO viewsowing
	select a.fecha,a.finca,'' as bloque,v.producto,a.variedad,'' as año,
	'' as temporada,'PERDIDAS_P' AS tipo,'0000-00-00' as fecha_pico,'' as tipo_siembra,maquilador,SUM(a.valor)/
	( SELECT sum(b.valor)
	FROM viewsowing as b
	WHERE b.finca=a.finca and b.variedad=a.variedad and b.fecha=a.fecha and b.tipo in('SIEMBRA','PERDIDAS')
	GROUP BY b.finca,b.variedad,b.fecha )
	from viewsowing as a
	LEFT JOIN varieties AS v ON v.nombre=a.variedad
	where a.tipo='PERDIDAS' and v.producto IN ('CLAVEL','MINICLAVEL')
	GROUP BY  a.finca,a.variedad,a.fecha
";

/////////////////////////////////////////////////////////////////////////////////////////////////
$queryCosechasP="INSERT INTO viewsowing
select fecha_cosecha_r as fecha,'PROPAGACION' AS finca,'0' as bloque,v.producto,a.variedad,b.año,
	a.temporada_obj as temporada,'COSECHA_P' AS tipo,b.fecha_pico as fecha_pico,
    a.tipo as tipo_siembra,br.nombre as maquilador,(case when a.esquejes_ensarte=0 then 0 else sum(a.esquejes_cosecha-a.esquejes_ensarte)/sum(a.esquejes_ensarte) end) as valor
	from program as a 
    LEFT JOIN breeders AS br ON br.id=a.casa_id
    LEFT JOIN seasons as b on b.nombre=a.temporada_obj
	LEFT JOIN varieties AS v ON v.nombre=a.variedad
	where v.producto IN ('CLAVEL','MINICLAVEL')
	GROUP BY a.variedad,a.fecha_cosecha_r,a.temporada_obj,br.nombre";

$resultLabores = $conexion->query($queryLabores);
$resultTeoSiembra = $conexion->query($queryTeoSiembra);
$resultTeoSiembraAdd = $conexion->query($queryTeoSiembraAdd);
$resultTeoEnsarte = $conexion->query($queryTeoEnsarte);
$resultTeoCosecha = $conexion->query($queryTeoCosecha);
$resultSiembra = $conexion->query($querySiembra);
$resultEnsarte = $conexion->query($queryEnsartes);
$resultCosecha = $conexion->query($queryCosechas);
$resultPerdidasP = $conexion->query($queryPerdidasP);
$resultCosechasP = $conexion->query($queryCosechasP);

//Para valores atipicos se pone 0
$update ="UPDATE viewsowing SET valor=0 WHERE tipo='PERDIDAS_P' AND valor=1";
$conexion->query($update);

$conexion->close();

?>