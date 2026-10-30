<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/src/autoload.php';
require_once dirname(__DIR__, 2) . '/funciones/conexion.php';

$controller = new \App\Controllers\ComparadorTikaController();
$controller->apiComparador();

