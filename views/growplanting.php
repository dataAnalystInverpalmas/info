<?php
include_once('funciones/conexion.php');
require_once('vendor/autoload.php');

$controller = new \App\Controllers\GrowplantingController();
$controller->index();