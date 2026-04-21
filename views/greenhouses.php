<?php
// Archivo PUENTE:
// Este código delega la responsabilidad a nuestro nuevo sistema MVC en la carpeta /src,
// permitiendo que no se rompan las llamadas AJAX o Inclusiones previas del software viejo.

include_once ('funciones/conexion.php');

$controller = new \App\Controllers\GreenhouseController();
$controller->index();
?>
