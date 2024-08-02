<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
	$nombrearchivo='../archivos/tabla_temporadas.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlt = "TRUNCATE TABLE seasons";
$conexion->query($sqlt);

	for ($i=2;$i<=$numRows;$i++){

		$nombre=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$fiesta=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$ano=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$fecha_pico=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$fecha_fiesta=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$cod_temporada=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$tipo_temporada=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
		$ano_temporada=$objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();

		//$fecha_siembra=date('Y-m-d',$fechai);
		$sql="INSERT INTO seasons (nombre,fiesta,cod_temporada,año,fecha_pico,fecha_fiesta,tipo,ano_fiesta)";
		$sql=$sql." VALUES ('$nombre','$fiesta','$cod_temporada',$ano,'$fecha_pico','$fecha_fiesta','$tipo_temporada',$ano_temporada)";
		$result=$conexion->query($sql);

		  if (!$result){
			  echo $sql.$numRows;
			die ("Query failed");
		}
	}
?>