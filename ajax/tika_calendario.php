<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/funciones/conexion.php';

$controller = new \App\Controllers\TikaCalendarioController();
$controller->handleAjax();
