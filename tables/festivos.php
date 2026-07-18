<?php
include ('../funciones/conexion.php');
require "../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

$nombrearchivo = '../archivos/tabla_festivos.xlsx';

if (!file_exists($nombrearchivo)) {
    die('No se encontro el archivo tabla_festivos.xlsx en la carpeta archivos.');
}

$objPHPExcel = IOFactory::load($nombrearchivo);
$objPHPExcel->setActiveSheetIndex(0);
$numRows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();

$fechasUnicas = array();
$filasLeidas = 0;
$filasValidas = 0;

for ($i = 2; $i <= $numRows; $i++) {
    $filasLeidas++;
    $valorFecha = $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getCalculatedValue();

    if ($valorFecha === null || $valorFecha === '') {
        continue;
    }

    $fechaNormalizada = null;

    if (is_numeric($valorFecha)) {
        try {
            $fechaNormalizada = ExcelDate::excelToDateTimeObject($valorFecha)->format('Y-m-d');
        } catch (Exception $e) {
            $fechaNormalizada = null;
        }
    } else {
        $texto = trim((string)$valorFecha);
        $timestamp = strtotime($texto);
        if ($timestamp !== false) {
            $fechaNormalizada = date('Y-m-d', $timestamp);
        }
    }

    if ($fechaNormalizada === null) {
        continue;
    }

    $fechasUnicas[$fechaNormalizada] = 1;
    $filasValidas++;
}

if (count($fechasUnicas) === 0) {
    die('No se encontraron fechas validas en la columna A.');
}

$fechasActualizadas = 0;
$fechasIgnoradas = 0;

foreach (array_keys($fechasUnicas) as $fecha) {
    $sqlExiste = "SELECT COUNT(*) AS c FROM dates WHERE DATE(fecha) = '$fecha'";
    $queryExiste = $conexion->query($sqlExiste);

    if (!$queryExiste) {
        die('Error consultando dates: ' . $sqlExiste);
    }

    $rowExiste = $queryExiste->fetch_assoc();
    $cantidad = intval($rowExiste['c']);

    if ($cantidad > 0) {
        $sqlUpdate = "UPDATE dates SET festivo = 1 WHERE DATE(fecha) = '$fecha'";
        $resultUpdate = $conexion->query($sqlUpdate);

        if (!$resultUpdate) {
            die('Error actualizando festivo para fecha ' . $fecha);
        }

        $fechasActualizadas++;
    } else {
        $fechasIgnoradas++;
    }
}

echo 'Carga de festivos finalizada. '; 
echo 'Filas leidas: ' . $filasLeidas . '. ';
echo 'Filas con fecha valida: ' . $filasValidas . '. ';
echo 'Fechas unicas procesadas: ' . count($fechasUnicas) . '. ';
echo 'Fechas actualizadas en dates: ' . $fechasActualizadas . '. ';
echo 'Fechas ignoradas (no existen en dates): ' . $fechasIgnoradas . '.';
