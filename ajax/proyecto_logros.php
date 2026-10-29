<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';


use App\Controllers\ProyectoLogroController;

ProyectoLogroController::handleRequest($_SERVER, $_GET);
