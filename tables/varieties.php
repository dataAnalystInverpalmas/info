<?php
//lamar conexion
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
	$nombrearchivo='../archivos/tabla_variedades.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlv = "TRUNCATE TABLE varieties";
$conexion->query($sqlv);

	for ($i=2;$i<=$numRows;$i++){

		$nombre=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$producto=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$color=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$ciclo=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$codvari=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$casa=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();

		//$fecha_siembra=date('Y-m-d',$fechai);
		$sql="INSERT INTO varieties (nombre,producto,color,ciclo,codvari,casa_comercial)";
		$sql=$sql." VALUES ('$nombre','$producto','$color',$ciclo,'$codvari',$casa)";
		$result=$conexion->query($sql);

		if (!$result){
	    	echo $sql.$numRows;
     		die ("Query failed");
		}
	echo $sql;
	}

$sqlSt0 = "UPDATE varieties AS a 
	SET a.estado = 1 
	WHERE a.nombre IN (
		SELECT DISTINCT variedad 
		FROM plane
	)";
$update0 = $conexion->query($sqlSt0);


$sqlSt1 = "UPDATE varieties AS a
           SET a.estado = 1
           WHERE EXISTS (
               SELECT 1 
               FROM program AS b  
               WHERE b.variedad = a.nombre 
                 AND DATE(b.fecha_siembra) > CURDATE()
           )";
$update1 = $conexion->query($sqlSt1);

$sqlvar1 = "UPDATE varieties AS a SET producto='VITRINAS MINICLAVEL' WHERE EXISTS (SELECT 1 FROM plane AS b WHERE b.variedad = a.nombre AND b.temporada='VITRINAS' AND b.producto='MINICLAVEL')";
$conexion->query($sqlvar1);

for ($i = 2025; $i <= 2026; $i++) {
	$sql_ciclo = "UPDATE varieties AS v 
	INNER JOIN 
	(SELECT pr.variedad, MIN(pr.ciclo) AS ciclo FROM program AS pr
	INNER JOIN seasons AS s ON pr.temporada_obj = s.nombre AND s.año = $i
	GROUP BY pr.variedad) AS q ON q.variedad = v.nombre
	SET v.ciclo = q.ciclo
	WHERE v.ciclo = 0";

	$updateQ = $conexion->query($sql_ciclo);
}

if (!$update){
	echo $sql.$numRows;
	 die ("Update failed");
}

?>