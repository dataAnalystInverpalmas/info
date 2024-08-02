<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_variedadesAd.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	
	//Our SQL statement. This will empty / truncate the table "plane"
$sqlv = "TRUNCATE TABLE addxvariety";
$conexion->query($sqlv);
	
	for ($i=2;$i<=$numRows;$i++){
		
		$finca=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$variedad=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$porcentaje=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$sem=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();

		//$fecha_siembra=date('Y-m-d',$fechai);
		$sql="INSERT INTO addxvariety(finca,variedad,porcentaje,sem)";
		$sql=$sql." VALUES ('$finca','$variedad',$porcentaje,$sem)";
		$result=$conexion->query($sql);
		
		  if (!$result){
			  echo $sql.$numRows;
			die ("Query failed");
		}
    }
    $conexion->close();
?>