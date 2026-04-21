<?php
namespace App\Controllers;

use App\Models\Tarea;
use App\Models\Proyecto;

class TareaController {

    public function index() {
        $tareas = Tarea::getAll();
        $proyectos = Proyecto::getAll();
        extract(['tareas' => $tareas, 'proyectos' => $proyectos]);
        require_once __DIR__ . '/../Views/Tareas/index.php';
    }
}
