<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_asistencias.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	
	//Our SQL statement. This will empty / truncate the table "plane"
//$sqlv = "TRUNCATE TABLE assistances";
//$conexion->query($sqlv);
	
	for ($i=2;$i<=$numRows;$i++){
		
		$fecha=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$codigo=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$id=$codigo.$fecha;
		//$fecha_siembra=date('Y-m-d',$fechai);
		$sql="INSERT IGNORE INTO assistances (id,fecha,codigo)";
		$sql=$sql." VALUES ('$id','$fecha','$codigo')";
		$result=$conexion->query($sql);
		
		  if (!$result){
			  echo $sql.$numRows;
			die ("Query failed");
		}
    }
    $conexion->close();
 ?>   