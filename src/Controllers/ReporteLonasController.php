<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ReporteLonas;

class ReporteLonasController {

    public function index(): void {
        $opciones = ReporteLonas::opcionesFiltro([]);
        $anios = ReporteLonas::obtenerAniosDisponibles();
        $anioDefault = 2026;

        require_once __DIR__ . '/../Views/ReporteLonas/index.php';
    }

    private static function extraerFiltros(array $params): array {
        return [
            'finca'          => isset($params['finca']) && $params['finca'] !== '' ? (string)$params['finca'] : '',
            'bloque'         => isset($params['bloque']) && $params['bloque'] !== '' ? (string)$params['bloque'] : '',
            'variedad'       => isset($params['variedad']) && $params['variedad'] !== '' ? (string)$params['variedad'] : '',
            'semana_siembra' => isset($params['semana_siembra']) && $params['semana_siembra'] !== '' ? (string)$params['semana_siembra'] : '',
            'cosecha'        => isset($params['cosecha']) && $params['cosecha'] !== '' ? (string)$params['cosecha'] : '',
        ];
    }

    public static function opciones(array $params): array {
        $filtros = self::extraerFiltros($params);
        return ReporteLonas::opcionesFiltro($filtros);
    }

    public static function datos(array $params): array {
        $filtros = self::extraerFiltros($params);
        $año = isset($params['año']) && is_numeric($params['año']) ? (int)$params['año'] : 2026;

        $encabezado = ReporteLonas::obtenerEncabezado($filtros);
        $matriz = ReporteLonas::obtenerMatrizLonas($filtros, $año);

        return [
            'encabezado' => $encabezado,
            'matriz'     => $matriz,
        ];
    }
}
