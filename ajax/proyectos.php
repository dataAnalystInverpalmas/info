<?php
/**
 * Endpoint AJAX para gestionar Proyectos
 * Métodos: GET (listar, obtener), POST (crear), PUT (actualizar), DELETE (eliminar)
 */

require_once dirname(__DIR__) . '/src/autoload.php';
require_once dirname(__DIR__) . '/funciones/conexion.php';
require_once dirname(__DIR__) . '/bd/conexion.php';

use App\Controllers\ProyectoController;

header('Content-Type: application/json; charset=utf-8');

try {
    $metodo = $_SERVER['REQUEST_METHOD'];
    $accion = $_GET['accion'] ?? null;
    $id = $_GET['id'] ?? null;
    
    switch ($metodo) {
        case 'GET':
            if ($accion === 'estadisticas' && $id) {
                echo json_encode(ProyectoController::estadisticas($id));
            } elseif ($id) {
                echo json_encode(ProyectoController::obtener($id));
            } else {
                echo json_encode(ProyectoController::listar());
            }
            break;
            
        case 'POST':
            echo json_encode(ProyectoController::crear());
            break;
            
        case 'PUT':
            if (!$id) {
                echo json_encode(['error' => 'ID es requerido']);
                break;
            }
            echo json_encode(ProyectoController::actualizar($id));
            break;
            
        case 'DELETE':
            if (!$id) {
                echo json_encode(['error' => 'ID es requerido']);
                break;
            }
            echo json_encode(ProyectoController::eliminar($id));
            break;
            
        default:
            echo json_encode(['error' => 'Método no permitido']);
    }
    
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
