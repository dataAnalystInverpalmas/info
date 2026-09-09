<?php
namespace App\Controllers;

use App\Models\Program;

class ProgramController {

    public function index() {
        $programas  = Program::getProgramas();
        $variedades = Program::getVariedades();
        $temporadas = Program::getTemporadas();
        $productos  = Program::getProductos();
        $colores    = Program::getColores();

        extract([
            'programas'  => $programas,
            'variedades' => $variedades,
            'temporadas' => $temporadas,
            'productos'  => $productos,
            'colores'    => $colores,
        ]);

        require_once __DIR__ . '/../Views/Programs/index.php';
    }
}
