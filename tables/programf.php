<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////
$nombrearchivo='../archivos/tabla_asignacion.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	$y=date("Y");
	//Our SQL statement. This will empty / truncate the table "plane"
	$sqlp = "DELETE FROM programf WHERE programa>$y";
	$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){

		$finca=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$bloque=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$producto=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$variedad=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$temporada_obj=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$matas=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$programa=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
		$ncamas = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
		$ciclo = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
		$fsiembra = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
		$producto=strtoupper($producto);
		$temporada_obj=strtoupper($temporada_obj);
		$variedad=strtoupper($variedad);
		//$fecha_siembra=date('Y-m-d',$fechai);
		$sql="INSERT INTO programf (producto,variedad,temporada_obj,plantas,programa,finca,bloque,ncamas,ciclo,fecha_siembra)";
		$sql=$sql." VALUES ('$producto','$variedad','$temporada_obj',$matas,$programa,'$finca',$bloque,$ncamas,$ciclo,'$fsiembra')";
		$result=$conexion->query($sql);
		
		if (!$result){		
			die ("Query failed Insert .$i");
		}
	}
		//actualizar columnas que dependen de la tabla de temporadas
		//$sqlUPD="UPDATE programf SET fecha_pico=(SELECT s.fecha_pico from seasons as s where s.nombre=temporada_obj)";
		//$sqlUPD=$sqlUPD.",ciclo=(SELECT program.ciclo 
		//						FROM program 
		//						WHERE programf.programa=program.programa
		//						AND programf.variedad=program.variedad
		//						GROUP BY program.variedad)";
		//$sqlUPD=$sqlUPD.",fecha_temporada=(SELECT s.fecha_fiesta from seasons as s where s.nombre=temporada_obj)";
		//$sqlUPD=$sqlUPD.",fecha_siembra=DATE_ADD((SELECT s.fecha_pico from seasons as s where s.nombre=temporada_obj), INTERVAL -(ciclo) WEEK)";
		//$sqlUPD=$sqlUPD.",fecha_ensarte=DATE_ADD((SELECT s.fecha_pico from seasons as s where s.nombre=temporada_obj), INTERVAL -(ciclo+4) WEEK)";
		//$sqlUPD=$sqlUPD.",fecha_cosecha=DATE_ADD(fecha_siembra, INTERVAL -(2) DAY)";
		//$sqlUPD=$sqlUPD."UPDATE programf SET ncamas=plantas/960";
		//$sqlUPD=$sqlUPD.",tipo=(SELECT s.tipo FROM seasons as s WHERE s.nombre=temporada_obj)";
		$sqlUPD=$sqlUPD = "UPDATE programf SET color=(SELECT v.color FROM varieties as v WHERE v.nombre=variedad)";

		$resultUPD=$conexion->query($sqlUPD);
		if (!$resultUPD){
			die ("Query failed Update");
		}
?>