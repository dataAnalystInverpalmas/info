<?php
namespace App\Controllers;

use App\Models\Programf;

class ProgramfController {

    public function index() {
        $programas  = Programf::getProgramas();
        $variedades = Programf::getVariedades();
        $temporadas = Programf::getTemporadas();
        $productos  = Programf::getProductos();
        $fincas     = Programf::getFincas();
        $bloques    = Programf::getBloques();

        extract([
            'programas'  => $programas,
            'variedades' => $variedades,
            'temporadas' => $temporadas,
            'productos'  => $productos,
            'fincas'     => $fincas,
            'bloques'    => $bloques,
        ]);

        require_once __DIR__ . '/../Views/Programsf/index.php';
    }
}
