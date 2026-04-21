<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$sql = "SELECT s.id, s.id_area, s.nombre, a.nombre as area_nombre, s.activo, s.fecha_creacion 
    FROM refri_secciones s 
    JOIN refri_areas a ON s.id_area = a.id 
    WHERE s.activo = TRUE 
    ORDER BY a.nombre, s.nombre ASC";
$resultado = $conexion->query($sql);

$secciones = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $secciones[] = $fila;
    }
    echo json_encode(['success' => true, 'data' => $secciones]);
} else {
    echo json_encode(['success' => true, 'data' => []]);
}

$conexion->close();
?>
