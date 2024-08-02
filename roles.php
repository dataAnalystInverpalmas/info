<?php
//////////////conexion////////////////////
include ('funciones/conexion.php');
//funciones personalizadas
//carbon
require "vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Mpdf\Mpdf;
///////////////////////////////////////////
$nombrearchivo='archivos/tabla_roles.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

if($_SESSION['role']=='1')	
	//Our SQL statement. This will empty / truncate the table "plane"
$sqlv = "TRUNCATE TABLE roles";
$conexion->query($sqlv);
	
	for ($i=2;$i<=$numRows;$i++){
		
		$dir=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$users_role=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();

		//$fecha_siembra=date('Y-m-d',$fechai);
		$sql="INSERT INTO roles(dir,users_role)";
		$sql=$sql." VALUES ('$dir','$users_role')";
		$result=$conexion->query($sql);
		
		  if (!$result){
			  echo $sql.$numRows;
			die ("Query failed");
		}
		
    }
	$conexion->close();
?>