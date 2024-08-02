<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;
///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_curvas.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "TRUNCATE TABLE table_curves";
$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){
		$variedad=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$edad=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$fmata=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();

		$sql="INSERT INTO table_curves (variedad,edad,fmata)";
		$sql=$sql." VALUES ('$variedad',$edad,$fmata)";
		$result=$conexion->query($sql);
		
		if (!$result){
			die ("Query failed: ".$sql);
		}
	}	
?>