<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$sql = "SELECT id, nombre, categoria, descripcion_categoria, descripcion, activo, fecha_creacion FROM refri_refrigerios WHERE activo = TRUE ORDER BY nombre ASC";
$resultado = $conexion->query($sql);

$refrigerios = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $refrigerios[] = $fila;
    }
    echo json_encode(['success' => true, 'data' => $refrigerios]);
} else {
    echo json_encode(['success' => true, 'data' => []]);
}

$conexion->close();
?>
