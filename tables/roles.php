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

\App\Helpers\Auth::requireAdmin();

$sqlv = "TRUNCATE TABLE roles";
$conexion->query($sqlv);

$stmt = $conexion->prepare("INSERT INTO roles (dir, users_role) VALUES (?, ?)");
for ($i = 2; $i <= $numRows; $i++) {
    $dir = trim((string)$objPHPExcel->getActiveSheet()->getCell('A' . $i)->getCalculatedValue());
    $users_role = intval($objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue());

    if ($dir !== '') {
        $stmt->bind_param("si", $dir, $users_role);
        $stmt->execute();
    }
}
$stmt->close();
$conexion->close();
?>