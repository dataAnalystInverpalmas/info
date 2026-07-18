<?php
/**
 * Script de prueba para validar el endpoint de reordenamiento de tareas
 * Uso: Abre este archivo en el navegador o llamalo via curl
 * 
 * Ejemplo curl:
 * curl -X POST http://localhost/info/test_reorder_api.php \
 *   -H "Content-Type: application/json" \
 *   -d '{"tareas":[{"id":1,"proyecto_id":0,"orden_ejecucion":1}]}'
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simular sesión si no existe
if (!isset($_SESSION['id'])) {
    $_SESSION['id'] = 1; // Usuario de prueba
    $_SESSION['usuario'] = 'Test User';
}

require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/funciones/conexion.php';
require_once __DIR__ . '/bd/conexion.php';

use App\Controllers\TareaController;

// Crear una solicitud POST simulada
$_SERVER['REQUEST_METHOD'] = 'POST';
$_GET['accion'] = 'reordenar';

// Verificar si hay datos JSON en el body
$input = file_get_contents('php://input');
if (empty($input)) {
    // Datos de prueba
    $input = json_encode([
        'tareas' => [
            ['id' => 1, 'proyecto_id' => 0, 'orden_ejecucion' => 1],
            ['id' => 2, 'proyecto_id' => 0, 'orden_ejecucion' => 2],
            ['id' => 3, 'proyecto_id' => 0, 'orden_ejecucion' => 3],
        ]
    ]);
}

// Mock del input stream
stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $input
    ]
]);

echo "<pre>";
echo "=== TEST DE REORDENAMIENTO DE TAREAS ===\n";
echo "Entrada JSON:\n";
echo json_encode(json_decode($input, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "Llamando a TareaController::handleRequest...\n";
echo "---\n";

// Llamar al controlador
TareaController::handleRequest($_SERVER, $_GET);

echo "\n---\n";
echo "Si ve {'success': true, ...}, el reordenamiento funciona.\n";
echo "</pre>";
?>
