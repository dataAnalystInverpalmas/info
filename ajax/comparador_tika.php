<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';

$controller = new \App\Controllers\ComparadorTikaController();
$action = $_REQUEST['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'importar' : (isset($_GET['modelos']) ? 'comparador' : 'modelos'));

switch ($action) {
    case 'modelos':
        $controller->apiModelos();
        break;
    case 'importar':
        $controller->apiImportar();
        break;
    case 'comparador':
    default:
        $controller->apiComparador();
        break;
}

