<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_labores_siembra.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	$y=date("Y");
	//$y=2022;

	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "DELETE FROM labors_sowing WHERE fecha>='2023-02-1' ";
$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){
		$variedad=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$temporada=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$tipo=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$fecha=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$observacion=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$valor=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$programa=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
		$ubicacion=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
		$bloque = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();

		//$fecha=date($fecha);
		//$formated_DATE = date_format($fecha, 'Y-m-d');
		$fecha = gmdate("Y-m-d ",($fecha- 25569) * 86400 );
		$sql="INSERT INTO labors_sowing (variedad,temporada,tipo,fecha,comentario,valor,programa,ubicacion,bloque)";
		$sql=$sql." VALUES ('$variedad','$temporada','$tipo','$fecha','$observacion',$valor,'$programa','$ubicacion',$bloque)";
		$result=$conexion->query($sql);

		if (!$result){
			die ("Query failed".$sql);
		}
	}
?>