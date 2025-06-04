<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_evaluaciones.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "TRUNCATE TABLE table_evaluations";
$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){
		$fecha=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$variedad=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$item=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$valor=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$usuario=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();

		$sql="INSERT INTO table_evaluations (fecha,variedad,item,valor,usuario)";
		$sql=$sql." VALUES ('$fecha','$variedad','$item',$valor,'$usuario')";
		$result=$conexion->query($sql);
		
		if (!$result){
			die ("Query failed: ".$sql);
		}
	}
?>