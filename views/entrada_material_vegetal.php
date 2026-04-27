<?php
include_once('funciones/conexion.php');

$controller = new \App\Controllers\EntradaMaterialVegetalController();
$controller->index();