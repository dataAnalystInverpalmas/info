<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
	$nombrearchivo='../archivos/tabla_arreglos.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "TRUNCATE TABLE arrangements";
$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){
		$variedad=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$finca=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$tipo=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$aplicar=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$medidat=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$valor=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		
		$sql="INSERT INTO arrangements (variedad,finca,tipo,aplicar,medidat,valor)";
		$sql=$sql." VALUES ('$variedad','$finca','$tipo','$aplicar','$medidat',$valor)";
		$result=$conexion->query($sql);
		if (!$result){
			die ("Query failed".$sql);
		}
	}
?>