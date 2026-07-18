<?php
include_once ('funciones/conexion.php');

$controller = new \App\Controllers\GestionDashboardController();
$controller->index();
?>