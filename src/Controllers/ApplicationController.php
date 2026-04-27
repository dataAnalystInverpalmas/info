<?php
namespace App\Controllers;

use App\Models\Application;
use App\Helpers\Database;
use Carbon\Carbon;

class ApplicationController {

    public function index() {
        require_once __DIR__ . '/../../dist/Barcode39.php';

        $conexion = Database::getConnection();

        $di = Carbon::now();
        $de = Carbon::now();
        $dateIni = $di->startOfWeek()->format('Y-m-d');
        $dateEnd = $de->endOfWeek()->format('Y-m-d');

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

        $rows        = Application::getMain($where);
        $rowsByBlock = Application::getByBlock($where);
        $summary     = Application::getSummary($where);
        $supplies    = Application::getSupplies($where);
        $tipos       = Application::getTipos();
        $fincas      = Application::getFincas();

        extract([
            'rows'        => $rows,
            'rowsByBlock' => $rowsByBlock,
            'summary'     => $summary,
            'supplies'    => $supplies,
            'tipos'       => $tipos,
            'fincas'      => $fincas,
            'dateIni'     => $dateIni,
            'dateEnd'     => $dateEnd,
            'tipo'        => $tipo,
            'finca'       => $finca,
        ]);

        require_once __DIR__ . '/../Views/Applications/index.php';
    }
}
