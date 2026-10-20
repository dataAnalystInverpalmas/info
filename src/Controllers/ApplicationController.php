<?php
namespace App\Controllers;

use App\Models\Application;
use App\Helpers\Database;
use Carbon\Carbon;

class ApplicationController {

    public function index() {
        $conexion = Database::getConnection();

        try {
            $di = Carbon::now();
            $de = Carbon::now();
            $dateIni = $di->startOfWeek()->format('Y-m-d');
            $dateEnd = $de->endOfWeek()->format('Y-m-d');
        } catch (\Throwable $e) {
            $dateIni = date('Y-m-d', strtotime('monday this week'));
            $dateEnd = date('Y-m-d', strtotime('sunday this week'));
        }

        $where = " WHERE p.producto in('CLAVEL','MINICLAVEL','CLAVEL STANDARD','CLAVEL MINIATURA') ";
        $tipo  = '';
        $finca = '';

        if (!empty($_POST['xtipo']))  $tipo  = $_POST['xtipo'];
        if (!empty($_POST['xfinca'])) $finca = $_POST['xfinca'];

        $expr = Application::fechaAplicacionExpr();

        if (isset($_POST['buscar'])) {
            $dateIni = $_POST['dateIni'];
            $dateEnd = $_POST['dateEnd'];
            if ($tipo  !== '') $where .= " AND a.tipo='"  . $conexion->real_escape_string($tipo)  . "' ";
            if ($finca !== '') $where .= " AND a.finca='" . $conexion->real_escape_string($finca) . "' ";
            $where .= " AND $expr between '$dateIni' AND '$dateEnd' ";
        } else {
            $finca = 'INVERPALMAS';
            $tipo  = 'GIBERELINA';
            $where .= " AND a.finca='INVERPALMAS' AND a.tipo='GIBERELINA' AND $expr between '$dateIni' AND '$dateEnd' ";
        }

        $rows             = Application::getMain($where);
        $supplies         = Application::getSupplies($where);
        $materialsByBlock = Application::getMaterialsByBlock($where);
        $tipos            = Application::getTipos();
        $fincas           = Application::getFincas();

        extract([
            'rows'             => $rows,
            'supplies'         => $supplies,
            'materialsByBlock' => $materialsByBlock,
            'tipos'            => $tipos,
            'fincas'           => $fincas,
            'dateIni'          => $dateIni,
            'dateEnd'          => $dateEnd,
            'tipo'             => $tipo,
            'finca'            => $finca,
        ]);

        require_once __DIR__ . '/../Views/Applications/index.php';
    }
}
