<?php
// Archivo PUENTE:
// Este codigo delega la responsabilidad al sistema MVC en /src,
// compatible con la ruta descriptiva (report=registra_floreros) y numérica (report=12).

include_once('funciones/conexion.php');

$controller = new \App\Controllers\FlowervaseController();
$controller->index();
