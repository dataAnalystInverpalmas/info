<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$año = $_POST['año'] ?? date('Y');
$mes = $_POST['mes'] ?? date('m');
$quincena = $_POST['quincena'] ?? '';


$sql = "SELECT h.id, h.fecha, h.proveedor_id, h.seccion_id, h.refrigerio_id, h.jornada_id,
           h.cantidad, h.valor_unitario, h.valor_total, h.cuenta_cobro, h.observaciones, h.fecha_creacion,
           p.nombre as proveedor, s.nombre as seccion, r.nombre as refrigerio, j.nombre as jornada, a.nombre as area
    FROM refri_hechos h
    JOIN refri_fechas f ON h.fecha = f.fecha
    JOIN refri_proveedores p ON h.proveedor_id = p.id
    JOIN refri_secciones s ON h.seccion_id = s.id
    JOIN refri_areas a ON s.id_area = a.id
    JOIN refri_refrigerios r ON h.refrigerio_id = r.id
    JOIN refri_jornadas j ON h.jornada_id = j.id
    WHERE f.año = ? AND f.mes = ?";

$params = [$año, $mes];
$types = "ii";

if (!empty($quincena)) {
    if ($quincena == 1) {
        $sql .= " AND DAY(h.fecha) <= 15";
    } else if ($quincena == 2) {
        $sql .= " AND DAY(h.fecha) >= 16";
    }
}

$sql .= " ORDER BY h.fecha ASC, p.nombre ASC";

$stmt = $conexion->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$resultado = $stmt->get_result();

$hechos = [];
$resumen_proveedor = [];
$resumen_area = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $hechos[] = $fila;
        
        // Resumen por proveedor
        $prov = $fila['proveedor'];
        if (!isset($resumen_proveedor[$prov])) {
            $resumen_proveedor[$prov] = 0;
        }
        $resumen_proveedor[$prov] += $fila['valor_total'];
        
        // Resumen por área
        $area = $fila['area'];
        if (!isset($resumen_area[$area])) {
            $resumen_area[$area] = 0;
        }
        $resumen_area[$area] += $fila['valor_total'];
    }
}

echo json_encode([
    'success' => true,
    'data' => $hechos,
    'resumen_proveedor' => $resumen_proveedor,
    'resumen_area' => $resumen_area,
    'total_general' => array_sum($resumen_proveedor)
]);

$stmt->close();
$conexion->close();
?>
