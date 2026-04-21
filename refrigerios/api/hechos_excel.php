<?php
// Exporta hechos filtrados a Excel
include dirname(__DIR__, 2) . '/funciones/conexion.php';
require dirname(__DIR__, 2) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$año = $_GET['año'] ?? date('Y');
$mes = $_GET['mes'] ?? date('m');
$quincena = $_GET['quincena'] ?? '';

$sql = "SELECT h.id, f.fecha, f.año, f.mes, f.quincena, p.nombre as proveedor, s.nombre as seccion, r.nombre as refrigerio, j.nombre as jornada, h.cantidad, h.valor_unitario, h.valor_total, h.cuenta_cobro, h.observaciones
        FROM refri_hechos h
        JOIN refri_fechas f ON h.fecha = f.fecha
        JOIN refri_proveedores p ON h.proveedor_id = p.id
        JOIN refri_secciones s ON h.seccion_id = s.id
        JOIN refri_refrigerios r ON h.refrigerio_id = r.id
        JOIN refri_jornadas j ON h.jornada_id = j.id
        WHERE f.año = ? AND f.mes = ?";
$params = [$año, $mes];
$types = "ii";
if (!empty($quincena)) {
    $sql .= " AND f.quincena = ?";
    $params[] = $quincena;
    $types .= "i";
}
$sql .= " ORDER BY f.fecha ASC, p.nombre ASC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados
$headers = ['ID', 'Fecha', 'Año', 'Mes', 'Quincena', 'Proveedor', 'Sección', 'Refrigerio', 'Jornada', 'Cantidad', 'Valor Unitario', 'Valor Total', 'Cuenta Cobro', 'Observaciones'];
$sheet->fromArray($headers, NULL, 'A1');

$row = 2;
while ($data = $result->fetch_assoc()) {
    $sheet->fromArray(array_values($data), NULL, 'A' . $row);
    $row++;
}

$filename = 'hechos_' . $año . '_' . $mes . ($quincena ? '_Q' . $quincena : '') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
