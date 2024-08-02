<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;


/* if ($_GLOBALS['src'] == "http://172.10.18.220:4444"){
	$q = "DELETE FROM plane WHERE finca='INVERPALMAS' ";
	$sql_del = $conexion->query($q);
}else{
	$q = "DELETE FROM plane WHERE finca='PALERMO' ";
	$sql_del = $conexion->query($q);
} */

	///////////////////////////////////////////
    //leemos el archivo Excel en una estructura mas manejable
	$nombrearchivo='../archivos/Plantas Sembradas ld5000.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();	

	for ($i=2;$i<=$numRows;$i++){
		$finca=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$bloque=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$nave=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$cama=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$tabla=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$producto=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$variedad=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
		$tipo_suelo=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
		$temporada=$objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
		$plantas= $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
		//$tipo_plantas = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
		$fecha_siembra=$objPHPExcel->getActiveSheet()->getCell('M'.$i)->getCalculatedValue();
		$tipo_siembra=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
		$origen=$objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();
		$cosecha_reem=$objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue();
		$variedad_reem=$objPHPExcel->getActiveSheet()->getCell('O'.$i)->getCalculatedValue();
		$pico=$objPHPExcel->getActiveSheet()->getCell('P'.$i)->getCalculatedValue();
		$nmanguera=$objPHPExcel->getActiveSheet()->getCell('Q'.$i)->getCalculatedValue();
		//variables que vienen vacias solo para LD5000
		//$cama=$camaa.$tabla;
		//$area = 0;

		if($i==2){
			$q = "DELETE FROM plane WHERE finca='$finca' ";
			$sql_del = $conexion->query($q);
		}
		
		$origen=addslashes($origen);
		
		$sql="INSERT INTO plane (finca,bloque,tabla,nave,cama,producto,variedad,origen,tipo_suelo,temporada,fecha_siembra,plantas,tipo_siembra,cosecha_reem,variedad_reem,pico,nmanguera)";
		$sql=$sql." VALUES ('$finca',$bloque,'$tabla',$nave,'$cama','$producto','$variedad','$origen','$tipo_suelo','$temporada','$fecha_siembra',$plantas,'$tipo_siembra','$cosecha_reem','$variedad_reem','$pico','$nmanguera')";
		$result=$conexion->query($sql);
		echo $sql;
	}

	$sqlSt="update varieties as a set estado=1 where a.nombre=(select variedad from plane as b where a.nombre=b.variedad group by 1)";
	$update = $conexion->query($sqlSt);

 ?> 