<?php
namespace App\Controllers;

use App\Models\PlanoReemplazo;

class PlanoReemplazoController {

    public function index() {
        $rows = PlanoReemplazo::getBaseListado();

        // Obtener valores distintos para filtros
        $fincas = PlanoReemplazo::getDistinctValues('finca');
        $bloques = PlanoReemplazo::getDistinctValues('bloque');
        $tablas = PlanoReemplazo::getDistinctValues('tabla');
        $naves = PlanoReemplazo::getDistinctValues('nave');
        $tiposSiembra = PlanoReemplazo::getDistinctValues('tipo_siembra');
        $variedades = PlanoReemplazo::getDistinctValues('variedad');
        $cosechas = PlanoReemplazo::getDistinctValues('temporada');
        $semanasSiembra = PlanoReemplazo::getDistinctSemanasSiembra();

        extract([
            'rows' => $rows,
            'fincas' => $fincas,
            'bloques' => $bloques,
            'tablas' => $tablas,
            'naves' => $naves,
            'tiposSiembra' => $tiposSiembra,
            'variedades' => $variedades,
            'cosechas' => $cosechas,
            'semanasSiembra' => $semanasSiembra,
        ]);

        require_once __DIR__ . '/../Views/PlanoReemplazos/index.php';
    }

    public static function listar() {
        $filters = [];
        if (!empty($_GET['finca'])) {
            $filters['finca'] = $_GET['finca'];
        }
        if (!empty($_GET['bloque'])) {
            $filters['bloque'] = $_GET['bloque'];
        }
        if (!empty($_GET['tabla'])) {
            $filters['tabla'] = $_GET['tabla'];
        }
        if (!empty($_GET['nave'])) {
            $filters['nave'] = $_GET['nave'];
        }
        if (!empty($_GET['tipo_siembra'])) {
            $filters['tipo_siembra'] = $_GET['tipo_siembra'];
        }
        if (!empty($_GET['variedad'])) {
            $filters['variedad'] = $_GET['variedad'];
        }
        if (!empty($_GET['cosecha'])) {
            $filters['cosecha'] = $_GET['cosecha'];
        }
        if (!empty($_GET['semana_siembra'])) {
            $filters['semana_siembra'] = $_GET['semana_siembra'];
        }
        return ['data' => PlanoReemplazo::getBaseListado($filters)];
    }

    public static function catalogos() {
        return [
            'variedades' => PlanoReemplazo::getVariedadesActivas(),
            'temporadas' => PlanoReemplazo::getTemporadasActivas(),
        ];
    }
}
