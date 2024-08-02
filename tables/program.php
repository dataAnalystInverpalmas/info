<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;

///////////////////////////////////////////

	$nombrearchivo='../archivos/tabla_presupuesto.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$objPHPExcel->setActiveSheetIndex(0);
	$numRows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();
	$y=date("Y");

	for ($i=2;$i<=$numRows;$i++){

		$producto=$objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
		$variedad=$objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
		$ciclo=$objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
		$temporada_obj=$objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
		$ncamas=$objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
		$programa=$objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
		$casa_id=$objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
		$raiz=$objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
		$producto=strtoupper($producto);
		$temporada_obj=strtoupper($temporada_obj);
		$variedad=strtoupper($variedad);
		//$fecha_siembra=date('Y-m-d',$fechai);
		//consulta de registro actual
		$consulta = "SELECT id FROM program where variedad='$variedad' and temporada_obj='$temporada_obj' and casa_id=$casa_id and raiz=$raiz group by variedad,temporada_obj,casa_id,raiz ";
		$query = $conexion->query($consulta);
		$cuenta = $query->fetch_row();
		$id = $cuenta['0'];

		if (($query->num_rows>0)) {

			$actualizar = "UPDATE program set ncamas=$ncamas,casa_id=$casa_id,ciclo=$ciclo, raiz=$raiz WHERE id=$id ";
			$qact = $conexion->query($actualizar);

		}else{

			$sql="INSERT INTO program (producto,variedad,ciclo,temporada_obj,ncamas,programa,casa_id,raiz)";
			$sql=$sql." VALUES ('$producto','$variedad',$ciclo,'$temporada_obj',$ncamas,$programa,$casa_id,$raiz)";
			$result=$conexion->query($sql);

			if (!$result){
				die ("Query failed Insert".$sql);
			}

		}
		echo $actualizar;
		echo $insert;
	}

		$sqlnc = "DELETE FROM program WHERE ncamas<1 ";
		//eliminar las camas que no aportan
		$conexion->query($sqlnc);
		//actualizar columnas que dependen de la tabla de temporadas
		$sqlUPD="UPDATE program SET fecha_pico=(SELECT s.fecha_pico from seasons as s where s.nombre=temporada_obj)";
		//$sqlUPD=$sqlUPD.",ciclo=(SELECT ciclo from varieties WHERE `variedad`=varieties.nombre group by 1)";
		$sqlUPD=$sqlUPD.",fecha_temporada=(SELECT s.fecha_fiesta from seasons as s where s.nombre=temporada_obj)";
		$sqlUPD=$sqlUPD.",fecha_siembra=DATE_ADD((SELECT s.fecha_pico from seasons as s where s.nombre=temporada_obj), INTERVAL -(ciclo) WEEK)";
		$sqlUPD=$sqlUPD.",fecha_ensarte=DATE_ADD((SELECT s.fecha_pico from seasons as s where s.nombre=temporada_obj), INTERVAL -(ciclo+4) WEEK)";
		$sqlUPD=$sqlUPD.",fecha_cosecha=DATE_ADD(fecha_siembra, INTERVAL -(2) DAY)";
		$sqlUPD=$sqlUPD.",plantas=ncamas*960";
		$sqlUPD=$sqlUPD.",tipo=(SELECT s.tipo FROM seasons as s WHERE s.nombre=temporada_obj)";
		$sqlUPD=$sqlUPD.",color=(SELECT v.color FROM varieties as v WHERE v.nombre=variedad)";

		$resultUPD=$conexion->query($sqlUPD);
		if (!$resultUPD){
		  die ("Query failed Update");
	  }
	  echo $sqlUPD;
	  
?>