<?php
require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';

use App\Controllers\ComparaEscenariosController;

ComparaEscenariosController::handleDataRequest($_GET, $_SERVER);
