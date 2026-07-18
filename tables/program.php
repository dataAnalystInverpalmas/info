<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;

///////////////////////////////////////////

	function normalizeTextCell($value) {
		if ($value === null) {
			return null;
		}

		$value = trim((string) $value);
		return $value === '' ? null : $value;
	}

	function normalizeNumericCell($value) {
		if ($value === null || $value === '') {
			return null;
		}

		if (is_string($value)) {
			$value = trim($value);
			if ($value === '') {
				return null;
			}
		}

		return is_numeric($value) ? $value + 0 : null;
	}

	function normalizeDateCell($sheet, $column, $row) {
		$cell = $sheet->getCell($column . $row);
		$value = $cell->getCalculatedValue();

		if ($value === null || $value === '') {
			return null;
		}

		if (SpreadsheetDate::isDateTime($cell)) {
			try {
				return SpreadsheetDate::excelToDateTimeObject($value)->format('Y-m-d');
			} catch (Exception $e) {
				return null;
			}
		}

		if (is_numeric($value)) {
			try {
				return SpreadsheetDate::excelToDateTimeObject($value)->format('Y-m-d');
			} catch (Exception $e) {
				return null;
			}
		}

		$value = trim((string) $value);
		if ($value === '') {
			return null;
		}

		$formats = array('Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y/m/d');
		foreach ($formats as $format) {
			$date = Carbon::createFromFormat($format, $value);
			if ($date !== false) {
				return $date->format('Y-m-d');
			}
		}

		return null;
	}

	$nombrearchivo='../archivos/tabla_presupuesto.xlsx';
	$objPHPExcel=IOFactory::load($nombrearchivo);
	$sheet = $objPHPExcel->setActiveSheetIndex(0);
	$numRows=$sheet->getHighestRow();
	$y=date("Y");
	$resumen = array(
		'insertados' => 0,
		'actualizados' => 0,
		'omitidos' => 0,
		'errores' => array(),
		'actualizaciones' => array(),
		'inserciones' => array()
	);

	for ($i=2;$i<=$numRows;$i++){

		$producto=normalizeTextCell($sheet->getCell('A'.$i)->getCalculatedValue());
		$color=normalizeTextCell($sheet->getCell('B'.$i)->getCalculatedValue());
		$variedad=normalizeTextCell($sheet->getCell('C'.$i)->getCalculatedValue());
		$ciclo=normalizeNumericCell($sheet->getCell('D'.$i)->getCalculatedValue());
		$fecha_siembra=normalizeDateCell($sheet, 'E', $i);
		$fecha_ensarte=normalizeDateCell($sheet, 'F', $i);
		$fecha_cosecha=normalizeDateCell($sheet, 'G', $i);
		$temporada_obj=normalizeTextCell($sheet->getCell('H'.$i)->getCalculatedValue());
		$ncamas=normalizeNumericCell($sheet->getCell('I'.$i)->getCalculatedValue());
		$programa=normalizeNumericCell($sheet->getCell('J'.$i)->getCalculatedValue());
		$casa_id=normalizeNumericCell($sheet->getCell('K'.$i)->getCalculatedValue());
		$raiz=normalizeNumericCell($sheet->getCell('L'.$i)->getCalculatedValue());
		$ferradica=normalizeTextCell($sheet->getCell('M'.$i)->getCalculatedValue());
		$cantidad_pedida=normalizeNumericCell($sheet->getCell('N'.$i)->getCalculatedValue());

		if ($producto === null && $variedad === null && $fecha_siembra === null && $casa_id === null) {
			$resumen['omitidos']++;
			continue;
		}

		if ($variedad === null || $ciclo === null || $fecha_siembra === null || $casa_id === null || $raiz === null) {
			$resumen['omitidos']++;
			$resumen['errores'][] = array(
				'fila' => $i,
				'motivo' => 'Campos clave incompletos',
				'variedad' => $variedad,
				'ciclo' => $ciclo,
				'fecha_siembra' => $fecha_siembra,
				'casa_id' => $casa_id,
				'raiz' => $raiz
			);
			continue;
		}

		$producto=$producto === null ? null : strtoupper($producto);
		$temporada_obj=$temporada_obj === null ? null : strtoupper($temporada_obj);
		$variedad=strtoupper($variedad);
		//$fecha_siembra=date('Y-m-d',$fechai);
		//consulta de registro actual
		$consulta = "SELECT id FROM program where estado=1 and variedad='$variedad' and ciclo=$ciclo and fecha_siembra='$fecha_siembra' and casa_id=$casa_id and raiz=$raiz group by variedad,fecha_siembra,casa_id,raiz,ciclo ";
		$query = $conexion->query($consulta);
		if (!$query) {
			$resumen['errores'][] = array('fila' => $i, 'motivo' => $conexion->error, 'sql' => $consulta);
			continue;
		}
		$cuenta = $query->fetch_row();
		$id = $cuenta['0'];

		if (($query->num_rows>0)) {

			$actualizar = "UPDATE program set ncamas=$ncamas,casa_id=$casa_id,ciclo=$ciclo, raiz=$raiz, ferradica='$ferradica' WHERE id=$id ";
			$qact = $conexion->query($actualizar);
			if (!$qact) {
				$resumen['errores'][] = array('fila' => $i, 'motivo' => $conexion->error, 'sql' => $actualizar);
				continue;
			}
			$resumen['actualizados']++;
			$resumen['actualizaciones'][] = array('fila' => $i, 'id' => $id, 'variedad' => $variedad, 'fecha_siembra' => $fecha_siembra, 'casa_id' => $casa_id, 'raiz' => $raiz);

		}else{

			$sql="INSERT INTO program (producto,color,variedad,ciclo,fecha_siembra,fecha_ensarte,fecha_cosecha,temporada_obj,ncamas,programa,casa_id,raiz,ferradica,cantidad_pedida)";
			$sql=$sql." VALUES (".
				($producto === null ? "NULL" : "'$producto'").",".
				($color === null ? "NULL" : "'$color'").",".
				"'$variedad',".
				$ciclo.",".
				"'$fecha_siembra',".
				($fecha_ensarte === null ? "NULL" : "'$fecha_ensarte'").",".
				($fecha_cosecha === null ? "NULL" : "'$fecha_cosecha'").",".
				($temporada_obj === null ? "NULL" : "'$temporada_obj'").",".
				($ncamas === null ? "NULL" : $ncamas).",".
				($programa === null ? "NULL" : $programa).",".
				$casa_id.",".
				$raiz.",".
				($ferradica === null ? "NULL" : "'$ferradica'").",".
				($cantidad_pedida === null ? "NULL" : $cantidad_pedida).")";
			$result=$conexion->query($sql);

			if (!$result){
				$resumen['errores'][] = array('fila' => $i, 'motivo' => $conexion->error, 'sql' => $sql);
				continue;
			}
			$resumen['insertados']++;
			$resumen['inserciones'][] = array('fila' => $i, 'variedad' => $variedad, 'fecha_siembra' => $fecha_siembra, 'casa_id' => $casa_id, 'raiz' => $raiz);

		}
	}

		$sqlnc = "DELETE FROM program WHERE ncamas<1 ";
		//eliminar las camas que no aportan
		//$conexion->query($sqlnc);
		//actualizar columnas que dependen de la tabla de temporadas
		$sqlUPD="UPDATE program SET ";//fecha_pico=(SELECT s.fecha_pico from seasons as s where s.nombre=temporada_obj)";
		//$sqlUPD=$sqlUPD.",ciclo=(SELECT ciclo from varieties WHERE `variedad`=varieties.nombre group by 1)";
		//$sqlUPD=$sqlUPD.",fecha_temporada=(SELECT s.fecha_fiesta from seasons as s where s.nombre=temporada_obj)";
		//$sqlUPD=$sqlUPD.",fecha_siembra=DATE_ADD((SELECT s.fecha_pico from seasons as s where s.nombre=temporada_obj), INTERVAL -(ciclo) WEEK)";
		//$sqlUPD=$sqlUPD.",fecha_ensarte=DATE_ADD((SELECT s.fecha_pico from seasons as s where s.nombre=temporada_obj), INTERVAL -(ciclo+4) WEEK)";
		//$sqlUPD=$sqlUPD.",fecha_cosecha=DATE_ADD(fecha_siembra, INTERVAL -(2) DAY)";
		$sqlUPD=$sqlUPD."plantas=ncamas*960";
		//$sqlUPD=$sqlUPD.",tipo=(SELECT s.tipo FROM seasons as s WHERE s.nombre=temporada_obj)";
		//$sqlUPD=$sqlUPD.",color=(SELECT v.color FROM varieties as v WHERE v.nombre=variedad)";

		$resultUPD=$conexion->query($sqlUPD);
		if (!$resultUPD){
		  die ("Query failed Update");
	  }
	  header('Content-Type: application/json; charset=utf-8');
	  echo json_encode(array(
			'archivo' => $nombrearchivo,
			'filas_excel' => $numRows - 1,
			'insertados' => $resumen['insertados'],
			'actualizados' => $resumen['actualizados'],
			'omitidos' => $resumen['omitidos'],
			'errores' => $resumen['errores'],
			'muestras_actualizadas' => array_slice($resumen['actualizaciones'], 0, 20),
			'muestras_insertadas' => array_slice($resumen['inserciones'], 0, 20)
		), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	  
?>