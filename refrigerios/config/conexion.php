<?php
header('Content-Type: application/json; charset=utf-8');

$host = 'localhost';
$usuario = 'root';
$contraseña = '';
$bd = 'personal';

// Crear conexión
$conexion = new mysqli($host, $usuario, $contraseña, $bd, 3308);

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conexion->connect_error]));
}

// Configurar el charset
$conexion->set_charset("utf8");

?>
