<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_calidades.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();


	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "TRUNCATE TABLE table_qualities";
$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){
		$finca=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$variedad=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$fuente=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$grado=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$fecha=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$valor=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();

		$sql="INSERT INTO table_qualities (finca,variedad,fuente,grado,fecha,valor)";
		$sql=$sql." VALUES ('$finca','$variedad','$fuente','$grado','$fecha',$valor)";
		$result=$conexion->query($sql);
		
		if (!$result){
			die ("Query failed: ".$sql);
		}
	}	
?>