<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';

//consulta resumen de los bautizos disponibles, esta es la consulta que cargan las tarjetas en su version resumida

$finca    = $_POST['finca'] ?? [];
$bloque   = $_POST['bloque'] ?? [];
$variedad = $_POST['variedad'] ?? [];
$siembra  = $_POST['siembra'] ?? [];
$modo     = $_POST['modo'] ?? '';

//se construye el where dinamicamente
$where = [];
$where[] = "variedad != ''";
$where[] = "temporada != ''";

if (!empty($finca)) {
    $fincaStr = "'" . implode("','", array_map('addslashes', $finca)) . "'";
    $where[] = "finca IN ($fincaStr)";
}
if (!empty($bloque)) {
    $bloqueStr = "'" . implode("','", array_map('addslashes', $bloque)) . "'";
    $where[] = "bloque IN ($bloqueStr)";
}
if (!empty($variedad)) {
    $variedadStr = "'" . implode("','", array_map('addslashes', $variedad)) . "'";
    $where[] = "variedad IN ($variedadStr)";
}
if (!empty($siembra)) {
    $siembraStr = "'" . implode("','", array_map('addslashes', $siembra)) . "'";
    $where[] = "temporada IN ($siembraStr)";
}

if ($modo === 'barcode') {
    $sql = "
        SELECT
            p.finca,
            LPAD(p.bloque, 2, '0') AS bloque,
            p.variedad,
            p.temporada,
            DATE_FORMAT(p.fecha_siembra, '%x%v') AS siembra_yyww,
            SUM(p.plantas) AS matas,
            CASE
                WHEN NULLIF(TRIM(lv.codigo), '') IS NULL THEN ''
                ELSE LPAD(TRIM(lv.codigo), 4, '0')
            END AS codigo_variedad
        FROM informes.plane AS p
        LEFT JOIN informes.ld_variedades AS lv ON UPPER(TRIM(lv.nombre)) = UPPER(TRIM(p.variedad))
    ";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    $sql .= " GROUP BY p.finca, LPAD(p.bloque, 2, '0'), p.variedad, p.temporada, DATE_FORMAT(p.fecha_siembra, '%x%v'),
              CASE
                  WHEN NULLIF(TRIM(lv.codigo), '') IS NULL THEN ''
                  ELSE LPAD(TRIM(lv.codigo), 4, '0')
              END
              ORDER BY p.finca ASC, LPAD(p.bloque, 2, '0') ASC, p.variedad ASC, p.temporada ASC, siembra_yyww ASC";
} else {
    //para cambiar el tiempo cuando un bautizo es nuevo solo cambie donde dice  <= 7 <- por el nuevo valor de tiempo
    $sql = "
        SELECT 
            id,
            finca,
            bloque,
            variedad,
            temporada,
            tipo_siembra,
            fecha_siembra as f_siembra,
            CONCAT(fecha_siembra, ' / ', DATE_FORMAT(fecha_siembra, '%v')) AS fecha_siembra,
            COUNT(bloque) AS camas,
            SUM(plantas) AS plantas,
            CASE 
                WHEN DATEDIFF(NOW(), fecha_siembra) <= 7 THEN 'NUEVO'
                ELSE 'ANTIGUO'
            END AS estado
        FROM informes.plane
    ";

    //aqui se pasa el where consturido
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }

    //agrupo y ordeno los resultados
    $sql .= " GROUP BY finca, bloque, variedad, temporada, tipo_siembra
              ORDER BY finca ASC, bloque ASC, fecha_siembra DESC";
}

//se realiza la consulta de los resultados con  el query ya construido
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Error en consulta']);
    exit;
}
$stmt->execute();
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
