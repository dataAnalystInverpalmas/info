<?php
// Archivo PUENTE:
// Este codigo delega la responsabilidad al sistema MVC en /src,
// preservando la ruta original usada en el enrutador (report=12).

include_once('funciones/conexion.php');

$controller = new \App\Controllers\FlowervaseController();
$controller->index();
