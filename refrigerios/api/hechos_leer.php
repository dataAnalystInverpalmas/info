<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';



$sql = "SELECT h.id, h.fecha, YEAR(h.fecha) as año, h.proveedor_id, h.seccion_id, h.refrigerio_id, h.jornada_id,
        h.cantidad, h.valor_unitario, h.valor_total, h.observaciones, h.fecha_creacion,
        p.nombre as proveedor, s.nombre as seccion, r.nombre as refrigerio, j.nombre as jornada
    FROM refri_hechos h
    JOIN refri_fechas f ON h.fecha = f.fecha
    JOIN refri_proveedores p ON h.proveedor_id = p.id
    JOIN refri_secciones s ON h.seccion_id = s.id
    JOIN refri_refrigerios r ON h.refrigerio_id = r.id
    JOIN refri_jornadas j ON h.jornada_id = j.id
    ORDER BY h.fecha DESC, h.fecha_creacion DESC";

$resultado = $conexion->query($sql);
$hechos = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $hechos[] = $fila;
    }
    echo json_encode(['success' => true, 'data' => $hechos]);
} else {
    echo json_encode(['success' => true, 'data' => []]);
}

$conexion->close();
?>
