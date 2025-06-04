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

$sqlSt="update varieties as a set estado=1 where a.nombre=(select variedad from plane as b where a.nombre=b.variedad group by 1)";
$update = $conexion->query($sqlSt);

$sqlvar1 ="update varieties as a set producto='VITRINAS MINICLAVEL' where a.nombre=(select variedad from plane as b where a.nombre=b.variedad and temporada='VITRINAS' AND producto='MINICLAVEL' group by 1)";
$sqlvar2 ="update varieties as a set producto='VITRINAS MINICLAVEL' where a.nombre=(select variedad from plane as b where a.nombre=b.variedad and temporada='VITRINAS' AND producto='MINICLAVEL' group by 1)";
$conexion->query($sqlvar1);
$conexion->query($sqlvar2);

for ($i=2022;$i<=2023;$i++){
	$sql_ciclo = "update varieties as v 
	inner join 
	(SELECT pr.variedad,pr.ciclo FROM program as pr
	inner join seasons as s
	on pr.temporada_obj=s.nombre and s.año=$i
	group by 1) as q
	on q.variedad=v.nombre
	set v.ciclo=q.ciclo
	where v.ciclo=0";

	$updateQ = $conexion->query($sql_ciclo);
}

if (!$update){
	echo $sql.$numRows;
	 die ("Update failed");
}

?>