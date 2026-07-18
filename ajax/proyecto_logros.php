<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';
require_once dirname(__DIR__) . '/bd/conexion.php';

use App\Controllers\ProyectoLogroController;

ProyectoLogroController::handleRequest($_SERVER, $_GET);
