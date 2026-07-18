<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once dirname(__DIR__) . '/funciones/conexion.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Controllers\KanbanController;

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if (!in_array($accion, ['export_excel', 'export_pdf'], true)) {
    header('Content-Type: application/json; charset=utf-8');
}

switch ($accion) {

    case 'list':
        $desde = $_POST['desde'] ?? '';
        $hasta = $_POST['hasta'] ?? '';
        $result = KanbanController::listar($desde, $hasta);
        echo json_encode($result);
        break;

    case 'export_excel':
        $desde = $_GET['desde'] ?? $_POST['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? $_POST['hasta'] ?? '';
        KanbanController::exportExcel($desde, $hasta);
        break;

    case 'export_pdf':
        $desde = $_GET['desde'] ?? $_POST['desde'] ?? '';
        $hasta = $_GET['hasta'] ?? $_POST['hasta'] ?? '';
        KanbanController::exportPdf($desde, $hasta);
        break;

    case 'update_estado':
        $id = (int)($_POST['id'] ?? 0);
        $estado = $_POST['estado'] ?? '';
        $estados_validos = ['pendiente', 'en_progreso', 'completada', 'cancelada'];

        if ($id <= 0 || !in_array($estado, $estados_validos, true)) {
            echo json_encode(['success' => false, 'mensaje' => 'Datos inválidos']);
            exit;
        }

        $result = KanbanController::actualizarEstado($id, $estado);
        echo json_encode($result);
        break;

    case 'update_porcentaje':
        $id = (int)($_POST['id'] ?? 0);
        $porcentaje = (int)($_POST['porcentaje'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['success' => false, 'mensaje' => 'ID inválido']);
            exit;
        }

        $result = KanbanController::updatePorcentaje($id, $porcentaje);
        echo json_encode($result);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Acción inválida']);
        break;
}
