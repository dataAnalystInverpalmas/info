<?php
namespace App\Controllers;

use App\Models\Program;

class ProgramController {

    public function index() {
        $programas  = Program::getProgramas();
        $variedades = Program::getVariedades();
        $temporadas = Program::getTemporadas();

        extract([
            'programas'  => $programas,
            'variedades' => $variedades,
            'temporadas' => $temporadas,
        ]);

        require_once __DIR__ . '/../Views/Programs/index.php';
    }
}
