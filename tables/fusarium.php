<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
	$nombrearchivo='../archivos/tabla_fusarium.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "TRUNCATE TABLE fusarium";
$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){
		$finca=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$bloque=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$variedad=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$temporada=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$pico=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$fecha=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
   	    $plantas=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
 		$dato=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();

		$sql="INSERT INTO fusarium (finca,bloque,variedad,temporada,pico,fecha_siembra,plantas,dato)";
		$sql=$sql." VALUES ('$finca',$bloque,'$variedad','$temporada',$pico,$fecha,$plantas,$dato)";
		$result=$conexion->query($sql);

		if (!$result){
			die ("Query failed ".$sql);
		}
	}
	$UPD="UPDATE fusarium SET ntemporada=(SELECT s.nombre from seasons as s where s.cod_temporada=temporada)";
	$resultUPD=$conexion->query($UPD);

	if (!$resultUPD){
	  die ("Update failed");
  }
?>