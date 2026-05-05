<?php
if (is_file('funciones/conexion.php')) {
	include_once('funciones/conexion.php');
} else {
	include_once('../funciones/conexion.php');
}

$controller = new \App\Controllers\DashboardProyeccionesController();
$controller->index();
