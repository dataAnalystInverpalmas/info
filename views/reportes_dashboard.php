<?php
include_once ('funciones/conexion.php');

$controller = new \App\Controllers\ReportesDashboardController();
$controller->index();
