<?php
namespace App\Controllers;

use App\Models\Bitacora;

class BitacoraController {

    public function index() {
        $registros = Bitacora::getAll();
        extract(['registros' => $registros]);
        require_once __DIR__ . '/../Views/Bitacora/index.php';
    }
}
