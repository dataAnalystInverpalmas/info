<?php
/**
 * Endpoint AJAX para gestionar Tareas
 * Métodos: GET (listar, obtener), POST (crear), PUT (actualizar), DELETE (eliminar)
 */

require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';
require_once dirname(__DIR__) . '/bd/conexion.php';

use App\Controllers\TareaController;

header('Content-Type: application/json; charset=utf-8');

try {
    $metodo = $_SERVER['REQUEST_METHOD'];
    $accion = $_GET['accion'] ?? null;
    $id = $_GET['id'] ?? null;
    $proyecto_id = $_GET['proyecto_id'] ?? null;
    
    switch ($metodo) {
        case 'GET':
            if ($accion === 'pendientes') {
                echo json_encode(TareaController::pendientes());
            } elseif ($accion === 'imprevistas') {
                echo json_encode(TareaController::imprevistas());
            } elseif ($accion === 'proximas') {
                echo json_encode(TareaController::proximas());
            } elseif ($accion === 'por_proyecto' && $proyecto_id) {
                echo json_encode(TareaController::obtenerPorProyecto($proyecto_id));
            } elseif ($id) {
                echo json_encode(TareaController::obtener($id));
            } else {
                echo json_encode(TareaController::listar());
            }
            break;
            
        case 'POST':
            echo json_encode(TareaController::crear());
            break;
            
        case 'PUT':
            if (!$id) {
                echo json_encode(['error' => 'ID es requerido']);
                break;
            }
            if ($accion === 'cambiar_estado') {
                echo json_encode(TareaController::cambiarEstado($id));
            } else {
                echo json_encode(TareaController::actualizar($id));
            }
            break;
            
        case 'DELETE':
            if (!$id) {
                echo json_encode(['error' => 'ID es requerido']);
                break;
            }
            echo json_encode(TareaController::eliminar($id));
            break;
            
        default:
            echo json_encode(['error' => 'Método no permitido']);
    }
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
