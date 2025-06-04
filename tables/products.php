<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_productos.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	
	//Our SQL statement. This will empty / truncate the table "plane"
$sqlv = "TRUNCATE TABLE products";
$conexion->query($sqlv);
	
	for ($i=2;$i<=$numRows;$i++){
		
		$nombre=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$cod_nombre=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$grupo=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();

		//$fecha_siembra=date('Y-m-d',$fechai);
		$sql="INSERT INTO products(nombre,cod_nombre,grupo)";
		$sql=$sql." VALUES ('$nombre','$cod_nombre','$grupo')";
		$result=$conexion->query($sql);
		
		  if (!$result){
			  echo $sql.$numRows;
			die ("Query failed");
		}
    }
    $conexion->close();
?>