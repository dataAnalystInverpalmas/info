<?php
namespace App\Controllers;

use App\Models\Proyecto;

class ProyectoController {

    public function index() {
        $usuario_id = $_SESSION['id'] ?? null;
        $proyectos = Proyecto::getAll($usuario_id);
        $categorias = Proyecto::getCategorias($usuario_id);
        extract(['proyectos' => $proyectos, 'categorias' => $categorias]);
        require_once __DIR__ . '/../Views/Proyectos/index.php';
    }
}
