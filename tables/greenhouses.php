<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
	$nombrearchivo='../archivos/tabla_bloques.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "TRUNCATE TABLE greenhouses";
$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){
		$finca=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$bloque=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$tabla=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$nave=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$cama=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$longitud=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
   	    $ancho=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();

		$sql="INSERT INTO greenhouses (finca_id,bloque,tabla,nave,cama,longitud,ancho)";
		$sql=$sql." VALUES ($finca,$bloque,'$tabla',$nave,$cama,$longitud,$ancho)";
		$result=$conexion->query($sql);

		if (!$result){
			die ("Query failed ".$sql);
		}
	}

?>