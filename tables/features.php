<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_caracteristicas.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "TRUNCATE TABLE table_features";
$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){
		$variedad=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$item=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$medida=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$valor=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();

		$sql="INSERT INTO table_features (variedad,item,medida,valor)";
		$sql=$sql." VALUES ('$variedad','$item','$medida',$valor)";
		$result=$conexion->query($sql);
		
		if (!$result){
			die ("Query failed: ".$sql);
		}
	}	
?>