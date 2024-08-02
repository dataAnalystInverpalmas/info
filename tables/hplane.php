<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_hplano.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	$y=date("Y");
	//$y=2022;
	//Our SQL statement. This will empty / truncate the table "plane"
	$sqlp = "delete from hplane where year(fecha_siembra)>=$y";
	$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){

		$finca=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$bloque=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$variedad=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$temporada=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$variedad_rem=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$temporada_rem=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$tipo_siembra=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
		$fecha_siembra=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
		$nprovee=$objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
		$plantas=$objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();

		//$fecha_siembra=date('Y-m-d',$fechai);
		$sql="INSERT INTO hplane (finca,bloque,variedad,temporada,variedad_rem,temporada_rem,tipo_siembra,fecha_siembra,plantas,maquilador)";
		$sql=$sql." VALUES ('$finca',$bloque,'$variedad','$temporada','$variedad_rem','$temporada_rem','$tipo_siembra','$fecha_siembra',$plantas,'$nprovee')";
		$result=$conexion->query($sql);
		
		echo $sql;
			if (!$result){
			die ("Query failed Insert".$sql);
		}
		/* echo $sql; */
	}
?>