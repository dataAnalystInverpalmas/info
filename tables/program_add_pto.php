<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

$nombrearchivo='../archivos/tabla_pto_adicionales.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	$y=date("Y");
	//Our SQL statement. This will empty / truncate the table "plane"
	$sqlp = "DELETE FROM program_add_pto WHERE programa>$y";
	$conexion->query($sqlp);

	for ($i=2;$i<=$numRows;$i++){	
		$producto=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$variedad=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$temporada_obj=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$matas=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$programa=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$fecha_siembra=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$casa_id=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
		$raiz=$objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
		$ciclo=$objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
		$producto=strtoupper($producto);
		$temporada_obj=strtoupper($temporada_obj);
		$variedad=strtoupper($variedad);
		$tipo = "REEMPLAZO";

		$sql="INSERT INTO program_add_pto (producto,variedad,ciclo,fecha_siembra,tipo,temporada_obj,plantas,programa,casa_id,raiz)";
		$sql=$sql." VALUES ('$producto','$variedad','$ciclo','$fecha_siembra','$tipo','$temporada_obj',$matas,'$programa',$casa_id,$raiz)";

		$result=$conexion->query($sql);

		if (!$result){
			die ("Query failed Insertt".$sql);
		}
	}
	
	//actualizar columnas que dependen de la tabla de temporadas
	//$sqlUPD="UPDATE program_add_pto SET ciclo=(SELECT ciclo from varieties WHERE `variedad`=varieties.nombre group by 1)";
	/*$sqlUPD="UPDATE program_add_pto SET ";
	$sqlUPD=$sqlUPD." fecha_pico=DATE_ADD(fecha_siembra, INTERVAL + (ciclo) WEEK)";
	$sqlUPD=$sqlUPD.",fecha_temporada=(SELECT s.fecha_fiesta from seasons as s where s.nombre=temporada_obj)";
	$sqlUPD=$sqlUPD.",fecha_ensarte=DATE_ADD(fecha_pico, INTERVAL -(ciclo+4) WEEK)";
	$sqlUPD=$sqlUPD.",fecha_cosecha=DATE_ADD(fecha_siembra, INTERVAL -(2) DAY)";
	$sqlUPD=$sqlUPD.",ncamas=plantas/960";
	$sqlUPD=$sqlUPD.",producto=(SELECT v.producto FROM varieties as v WHERE v.nombre=variedad)";
	$sqlUPD=$sqlUPD.",color=(SELECT v.color FROM varieties as v WHERE v.nombre=variedad)";*/
	$sqlUPD = "UPDATE program_add_pto SET fecha_pico=DATE_ADD(fecha_siembra, INTERVAL + (ciclo) WEEK),fecha_temporada=(SELECT s.fecha_fiesta from seasons as s where s.nombre=temporada_obj),fecha_ensarte=DATE_ADD(fecha_pico, INTERVAL -(ciclo+4) WEEK),fecha_cosecha=DATE_ADD(fecha_siembra, INTERVAL -(2) DAY),ncamas=plantas/960,producto=(SELECT v.producto FROM varieties as v WHERE v.nombre=variedad),color=(SELECT v.color FROM varieties as v WHERE v.nombre=variedad)";
	$resultUPD=$conexion->query($sqlUPD);
	if (!$resultUPD){
		die ("Query failed Update".$sqlUPD);
	}

?>