<?php
header('Content-Type: application/json; charset=utf-8');

include dirname(__DIR__, 2) . '/funciones/conexion.php';

if (!$conexion || $conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . ($conexion ? $conexion->connect_error : 'No se pudo crear el objeto de conexión')]));
}

echo json_encode(['ok' => true, 'mensaje' => 'Conexión exitosa']);
?>
