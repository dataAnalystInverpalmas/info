<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_supervisores.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();


	//Our SQL statement. This will empty / truncate the table "plane"
$sqlp = "TRUNCATE TABLE supervisors";
$conexion->query($sqlp);

    for ($i=2;$i<=$numRows;$i++)
    {
		$cod=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$nom=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();

		$sql="INSERT INTO supervisors (codigo,nombre)";
		$sql=$sql." VALUES ($cod,'$nom')";
		$result=$conexion->query($sql);
		
        if (!$result)
        {
			die ("Query failed: ".$sql);
        }
        else
        {

        }
	}	
?>