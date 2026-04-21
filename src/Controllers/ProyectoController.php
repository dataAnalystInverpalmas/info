<?php
namespace App\Controllers;

use App\Models\Proyecto;

class ProyectoController {

    public function index() {
        $proyectos = Proyecto::getAll();
        $categorias = Proyecto::getCategorias();
        extract(['proyectos' => $proyectos, 'categorias' => $categorias]);
        require_once __DIR__ . '/../Views/Proyectos/index.php';
    }
}
