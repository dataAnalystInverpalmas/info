<?php
include_once 'conexion.php';

try {
    $conexion = Conexion::Conectar();
    echo "Conexión exitosa a la base de datos.";
} catch (Exception $e) {
    echo "Error al conectar: " . $e->getMessage();
}
