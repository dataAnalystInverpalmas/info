<?php
require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';
header('Content-Type: application/json; charset=utf-8');

use App\Controllers\BitacoraController;

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'create':
        $result = BitacoraController::crear();
        echo json_encode($result);
        break;

    case 'delete':
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID inválido']);
            exit;
        }
        $result = BitacoraController::eliminar($id);
        echo json_encode($result);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Acción inválida']);
        break;
}
