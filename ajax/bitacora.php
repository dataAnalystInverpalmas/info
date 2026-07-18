<?php
/**
 * Endpoint AJAX para gestionar Bitácora
 * Métodos: GET (listar, obtener historial), POST (crear), PUT (actualizar), DELETE (eliminar)
 */

require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';
require_once dirname(__DIR__) . '/bd/conexion.php';

use App\Controllers\BitacoraController;

header('Content-Type: application/json; charset=utf-8');

try {
    $metodo = $_SERVER['REQUEST_METHOD'];
    $accion = $_GET['accion'] ?? null;
    $id = $_GET['id'] ?? null;
    $tarea_id = $_GET['tarea_id'] ?? null;
    $proyecto_id = $_GET['proyecto_id'] ?? null;
    
    switch ($metodo) {
        case 'GET':
            if ($accion === 'notas_proyecto' && $proyecto_id) {
                echo json_encode(BitacoraController::obtenerNotasProyecto($proyecto_id));
            } elseif ($accion === 'historial' && $tarea_id) {
                echo json_encode(BitacoraController::obtenerHistorialTarea($tarea_id));
            } elseif ($accion === 'reporte') {
                echo json_encode(BitacoraController::obtenerReporte());
            } elseif ($tarea_id) {
                echo json_encode(BitacoraController::obtenerPorTarea($tarea_id));
            } elseif ($id) {
                echo json_encode(BitacoraController::obtener($id));
            } else {
                echo json_encode(BitacoraController::listar());
            }
            break;
            
        case 'POST':
            echo json_encode(BitacoraController::crear());
            break;
            
        case 'PUT':
            if (!$id) {
                echo json_encode(['error' => 'ID es requerido']);
                break;
            }
            echo json_encode(BitacoraController::actualizar($id));
            break;
            
        case 'DELETE':
            if (!$id) {
                echo json_encode(['error' => 'ID es requerido']);
                break;
            }
            echo json_encode(BitacoraController::eliminar($id));
            break;
            
        default:
            echo json_encode(['error' => 'Método no permitido']);
    }
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
