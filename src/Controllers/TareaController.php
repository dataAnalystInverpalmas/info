<?php
namespace App\Controllers;

use App\Models\Tarea;
use App\Models\Proyecto;

class TareaController {

    public function index() {
        $usuario_id = $_SESSION['id'] ?? null;
        $tareas = Tarea::getAll($usuario_id);
        $proyectos = Proyecto::getAll($usuario_id);
        extract(['tareas' => $tareas, 'proyectos' => $proyectos]);
        require_once __DIR__ . '/../Views/Tareas/index.php';
    }
}
