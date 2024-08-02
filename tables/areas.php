<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_areas.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "TRUNCATE TABLE areas";
$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){
		$nombre=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();

		$sql="INSERT INTO areas (nombre)";
		$sql=$sql." VALUES ('$nombre')";
		$result=$conexion->query($sql);

		if (!$result){
			die ("Query failed");
		}
	}
?>