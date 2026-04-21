<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$sql = "SELECT id, nombre, hora, activo, fecha_creacion FROM refri_jornadas WHERE activo = TRUE ORDER BY nombre ASC";
$resultado = $conexion->query($sql);

$jornadas = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $jornadas[] = $fila;
    }
    echo json_encode(['success' => true, 'data' => $jornadas]);
} else {
    echo json_encode(['success' => true, 'data' => []]);
}

$conexion->close();
?>
