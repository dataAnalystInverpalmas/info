<?php
namespace App\Controllers;

use App\Models\Greenhouse;

class GreenhouseController {
    /**
     * Método principal que inicializa la visualización de la tabla.
     */
    public function index() {
        // 1. Obtener la data (delegado al Modelo)
        $greenhouses = Greenhouse::getSummary();
        
        // 2. Extraer las variables para que esten disponibles en la vista
        extract(['greenhouses' => $greenhouses]);
        
        // 3. Cargar la "Vista" puramente de renderizado
        require_once __DIR__ . '/../Views/Greenhouse/index.php';
    }
}
