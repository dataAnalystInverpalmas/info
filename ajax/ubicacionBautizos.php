<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

//carga el plano de la finca para consulta de validacion  de ubicaciones y poder agrupar los resultados

$finca = $_POST['finca'] ?? '';
$bloque = $_POST['bloque'] ?? '';
$idfinca = $finca === 'INVERPALMAS' ? 1 : ($finca === 'PALERMO' ?  2 : 0);

$sql = "
    SELECT
        CONCAT(
            id_finca,
            bloque,
            tabla,
            LPAD(CAST(nave AS CHAR(2)), 2, '0'),
            LPAD(CAST(cama AS CHAR(2)), 2, '0')
        ) AS ubicacion,
        CASE
            WHEN id_finca = 1 THEN 'INVERPALMAS'
            WHEN id_finca = 2 THEN 'PALERMO'
        END AS finca,
        bloque,
        tabla,
        nave,
        cama
    FROM  siembras.ubicaciones_plano
    WHERE  id_finca = ? AND bloque = ? 
    ORDER BY ubicacion";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $idfinca, $bloque);
$stmt->execute();

$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);