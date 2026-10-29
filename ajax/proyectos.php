<?php
/**
 * Endpoint AJAX para gestionar Proyectos
 * Métodos: GET (listar, obtener), POST (crear), PUT (actualizar), DELETE (eliminar)
 */

require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';


use App\Controllers\ProyectoController;

ProyectoController::handleRequest($_SERVER, $_GET);
