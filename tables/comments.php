<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_comentarios.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "TRUNCATE TABLE table_comments";
$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){
		$fecha=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$usuario=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$variedad=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$post=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$comentario=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();

		$sql="INSERT INTO table_comments (fecha,usuario,variedad,post,comentario)";
		$sql=$sql." VALUES ('$fecha','$usuario','$variedad','$post','$comentario')";
		$result=$conexion->query($sql);
		
		if (!$result){
			die ("Query failed: ".$sql);
		}
	}
?>