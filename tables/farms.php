<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_fincas.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	
	//Our SQL statement. This will empty / truncate the table "plane"
$sqlv = "TRUNCATE TABLE farms";
$conexion->query($sqlv);

for ($i=2;$i<=$numRows;$i++)
{

	$nombre=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
	$abreviatura=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();

		//$fecha_siembra=date('Y-m-d',$fechai);
	$sql="INSERT INTO farms(nombre,abreviatura)";
	$sql=$sql." VALUES ('$nombre','$abreviatura')";
	$result=$conexion->query($sql);
		
	if (!$result)
	{
		echo $sql.$numRows;
		die ("Query failed");
	}
}
    $conexion->close();
?>