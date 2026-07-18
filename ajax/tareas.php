<?php
/**
 * Endpoint AJAX para gestionar Tareas
 * Métodos: GET (listar, obtener), POST (crear), PUT (actualizar), DELETE (eliminar)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';
require_once dirname(__DIR__) . '/bd/conexion.php';

use App\Controllers\TareaController;

TareaController::handleRequest($_SERVER, $_GET);
