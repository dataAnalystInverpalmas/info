<?php
require_once dirname(__DIR__) . '/src/autoload.php';

$controller = new \App\Controllers\DashboardProyeccionesController();
$controller->index();
