<?php
include dirname(__DIR__, 2) . '/funciones/conexion.php';

$sql = "SELECT id, nombre, nit, descuento_administrativo, activo, fecha_creacion FROM refri_proveedores WHERE activo = TRUE ORDER BY nombre ASC";
$resultado = $conexion->query($sql);

$proveedores = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $proveedores[] = $fila;
    }
    echo json_encode(['success' => true, 'data' => $proveedores]);
} else {
    echo json_encode(['success' => true, 'data' => []]);
}

$conexion->close();
?>
