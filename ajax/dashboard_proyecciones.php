<?php
require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';

use App\Controllers\DashboardProyeccionesController;

DashboardProyeccionesController::handleDataRequest($_GET, $_SERVER);
