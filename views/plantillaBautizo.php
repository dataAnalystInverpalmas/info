<?php
include_once(__DIR__ . '/../funciones/conexion.php');
$controller = new \App\Controllers\PlantillaBautizoController();
$controller->index();
