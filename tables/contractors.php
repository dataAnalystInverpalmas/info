<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_contratistas.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();


	//Our SQL statement. This will empty / truncate the table "plane"

$conexion->query($sqlp);

    for ($i=2;$i<=$numRows;$i++)
    {
		$cc=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$cod=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$nom=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$sex=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$finca=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$jefe=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$area=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
        $ruta=0;
        $estado=1;

		$sql="INSERT INTO employees (cc,codigo,nombre,sexo,finca,jefe_inmediato,area,ruta,estado)";
		$sql=$sql." VALUES ($cc,$cod,'$nom','$sex','$finca','$jefe','$area',$ruta,$estado)";
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