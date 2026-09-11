<?php
namespace App\Controllers;

use App\Helpers\FechasReporte;
use App\Models\ReporteSiembraComparativa;

class ReporteComparativoController {

    private const DIMENSIONES_RESUMEN = [
        'finca' => 'Finca',
        'flor' => 'Flor',
        'variedad' => 'Variedad',
        'temporada' => 'Temporada',
        'tipo_siembra' => 'Tipo siembra',
        'programa' => 'Programa',
        'color' => 'Color',
    ];

    public function index() {
        $filtrosOpciones = ReporteSiembraComparativa::opcionesFiltro();
        $rangoFechasDefault = FechasReporte::rangoDesdeInicioAnoHastaDomingoAnterior();
        require_once __DIR__ . '/../Views/ReporteComparativo/index.php';
    }

    private static function extraerFiltros(array $get): array {
        return [
            'finca' => $get['finca'] ?? [],
            'bloque' => $get['bloque'] ?? [],
            'variedad' => $get['variedad'] ?? [],
            'temporada' => $get['temporada'] ?? [],
            'flor' => $get['flor'] ?? [],
            'tipo_siembra' => $get['tipo_siembra'] ?? [],
            'programa' => $get['programa'] ?? [],
            'color' => $get['color'] ?? [],
            'desde' => $get['desde'] ?? '',
            'hasta' => $get['hasta'] ?? '',
        ];
    }

    /**
     * Opciones de filtro en cascada: se recalculan a partir de los filtros ya
     * seleccionados (cada uno excluyéndose a sí mismo, ver ReporteSiembraComparativa::opcionesFiltro).
     */
    public static function opciones(array $get): array {
        return ReporteSiembraComparativa::opcionesFiltro(self::extraerFiltros($get));
    }

    public static function datos(array $get): array {
        $filtros = self::extraerFiltros($get);
        $resumenPor = array_values(array_intersect(
            (array)($get['resumen_por'] ?? []),
            array_keys(self::DIMENSIONES_RESUMEN)
        ));

        $filas = ReporteSiembraComparativa::obtenerDatos($filtros);

        return [
            'kpis' => self::construirKpis($filas),
            'chart1' => self::construirChartPorFinca($filas),
            'chart2' => self::construirChartPorSemana($filas),
            'chart3' => self::construirChartPorSemanaPico($filas),
            'resumenSemanal' => self::construirResumenSemanal($filas),
            'table' => self::construirTabla($filas),
            'resumen' => self::construirResumen($filas, $resumenPor),
        ];
    }

    private static function construirKpis(array $filas): array {
        $totalReal = 0;
        $totalTeorica = 0;
        $fincas = [];

        foreach ($filas as $fila) {
            $totalReal += (int)$fila['camas_real'];
            $totalTeorica += (int)$fila['camas_teorica'];
            $fincas[$fila['finca']] = true;
        }

        $cumplimiento = $totalTeorica > 0 ? round(($totalReal / $totalTeorica) * 100, 1) : null;

        return [
            ['label' => 'Camas reales', 'value' => number_format($totalReal, 0, ',', '.'), 'help' => count($filas) . ' registro(s)'],
            ['label' => 'Camas teóricas', 'value' => number_format($totalTeorica, 0, ',', '.'), 'help' => 'Según programa'],
            ['label' => 'Cumplimiento', 'value' => $cumplimiento !== null ? $cumplimiento . '%' : 'N/D', 'help' => 'Real / Teórica'],
            ['label' => 'Fincas', 'value' => (string)count($fincas), 'help' => 'Fincas con datos en el filtro'],
        ];
    }

    private static function construirChartPorFinca(array $filas): array {
        $porFinca = [];
        foreach ($filas as $fila) {
            $finca = $fila['finca'];
            if (!isset($porFinca[$finca])) {
                $porFinca[$finca] = ['real' => 0, 'teorica' => 0];
            }
            $porFinca[$finca]['real'] += (int)$fila['camas_real'];
            $porFinca[$finca]['teorica'] += (int)$fila['camas_teorica'];
        }
        uasort($porFinca, static function ($a, $b) {
            return $b['teorica'] <=> $a['teorica'];
        });

        return [
            'labels' => array_keys($porFinca),
            'datasets' => [
                [
                    'label' => 'Real',
                    'data' => array_values(array_column($porFinca, 'real')),
                    'backgroundColor' => '#00796B',
                ],
                [
                    'label' => 'Teórica',
                    'data' => array_values(array_column($porFinca, 'teorica')),
                    'backgroundColor' => '#B0BEC5',
                ],
            ],
        ];
    }

    private static function construirChartPorSemana(array $filas): array {
        return self::construirChartLineaPorSemana($filas, 'semana_yyww');
    }

    /**
     * Igual al gráfico por semana de siembra, pero agrupando por la semana (yyww)
     * de fecha_pico en vez de la semana de siembra.
     */
    private static function construirChartPorSemanaPico(array $filas): array {
        return self::construirChartLineaPorSemana($filas, 'semana_pico_yyww');
    }

    private static function construirChartLineaPorSemana(array $filas, string $campoSemana): array {
        $porSemana = [];
        foreach ($filas as $fila) {
            $semana = $fila[$campoSemana] ?? null;
            $semana = $semana !== null ? (string)$semana : 'N/D';
            if (!isset($porSemana[$semana])) {
                $porSemana[$semana] = ['real' => 0, 'teorica' => 0];
            }
            $porSemana[$semana]['real'] += (int)$fila['camas_real'];
            $porSemana[$semana]['teorica'] += (int)$fila['camas_teorica'];
        }
        ksort($porSemana);

        return [
            'labels' => array_keys($porSemana),
            'datasets' => [
                [
                    'label' => 'Real',
                    'data' => array_values(array_column($porSemana, 'real')),
                    'borderColor' => '#00796B',
                    'backgroundColor' => 'rgba(0, 121, 107, 0.15)',
                    'fill' => true,
                ],
                [
                    'label' => 'Teórica',
                    'data' => array_values(array_column($porSemana, 'teorica')),
                    'borderColor' => '#B0BEC5',
                    'backgroundColor' => 'rgba(176, 190, 197, 0.15)',
                    'fill' => true,
                ],
            ],
        ];
    }

    private static function construirTabla(array $filas): ?array {
        if (empty($filas)) {
            return null;
        }

        $columnas = [
            ['data' => 'flor', 'title' => 'Flor'],
            ['data' => 'variedad', 'title' => 'Variedad'],
            ['data' => 'color', 'title' => 'Color'],
            ['data' => 'finca', 'title' => 'Finca'],
            ['data' => 'bloque', 'title' => 'Bloque'],
            ['data' => 'temporada', 'title' => 'Temporada'],
            ['data' => 'tipo_siembra', 'title' => 'Tipo siembra'],
            ['data' => 'programa', 'title' => 'Programa'],
            ['data' => 'camas_real', 'title' => 'Camas reales'],
            ['data' => 'camas_teorica', 'title' => 'Camas teóricas'],
            ['data' => 'diferencia', 'title' => 'Diferencia'],
            ['data' => 'cumplimiento_pct', 'title' => 'Cumplimiento %'],
            ['data' => 'semana_yyww', 'title' => 'Semana (yyww ISO)'],
        ];

        return ['columns' => $columnas, 'rows' => $filas];
    }

    /**
     * Tabla fija por semana (yyww), siempre visible antes del Detalle: camas
     * teóricas/reales, diferencia absoluta y % de cumplimiento por semana.
     */
    private static function construirResumenSemanal(array $filas): ?array {
        if (empty($filas)) {
            return null;
        }

        $porSemana = [];
        foreach ($filas as $fila) {
            $semana = $fila['semana_yyww'] ?? null;
            $semana = $semana !== null ? (string)$semana : 'N/D';
            if (!isset($porSemana[$semana])) {
                $porSemana[$semana] = ['semana_yyww' => $semana, 'camas_real' => 0, 'camas_teorica' => 0];
            }
            $porSemana[$semana]['camas_real'] += (int)$fila['camas_real'];
            $porSemana[$semana]['camas_teorica'] += (int)$fila['camas_teorica'];
        }

        foreach ($porSemana as &$fila) {
            $fila['diferencia'] = $fila['camas_real'] - $fila['camas_teorica'];
            $fila['cumplimiento_pct'] = $fila['camas_teorica'] > 0
                ? round(($fila['camas_real'] / $fila['camas_teorica']) * 100, 1)
                : null;
        }
        unset($fila);

        ksort($porSemana);

        $columnas = [
            ['data' => 'semana_yyww', 'title' => 'Semana (yyww ISO)'],
            ['data' => 'camas_teorica', 'title' => 'Camas teóricas'],
            ['data' => 'camas_real', 'title' => 'Camas reales'],
            ['data' => 'diferencia', 'title' => 'Diferencia'],
            ['data' => 'cumplimiento_pct', 'title' => 'Cumplimiento %'],
        ];

        return ['columns' => $columnas, 'rows' => array_values($porSemana)];
    }

    /**
     * Resumen dinámico: agrupa el detalle ya calculado por 1 o varias dimensiones
     * elegidas por el usuario, sumando camas reales y teóricas. No vuelve a consultar la BD.
     */
    private static function construirResumen(array $filas, array $resumenPor): ?array {
        if (empty($resumenPor) || empty($filas)) {
            return null;
        }

        $grupos = [];
        foreach ($filas as $fila) {
            $clave = [];
            foreach ($resumenPor as $dimension) {
                $valor = trim((string)($fila[$dimension] ?? ''));
                $clave[] = $valor !== '' ? $valor : 'Sin dato';
            }
            $llave = implode('|', $clave);

            if (!isset($grupos[$llave])) {
                $grupos[$llave] = [];
                foreach ($resumenPor as $indice => $dimension) {
                    $grupos[$llave][$dimension] = $clave[$indice];
                }
                $grupos[$llave]['camas_real'] = 0;
                $grupos[$llave]['camas_teorica'] = 0;
            }
            $grupos[$llave]['camas_real'] += (int)$fila['camas_real'];
            $grupos[$llave]['camas_teorica'] += (int)$fila['camas_teorica'];
        }

        foreach ($grupos as &$grupo) {
            $grupo['diferencia'] = $grupo['camas_real'] - $grupo['camas_teorica'];
            $grupo['cumplimiento_pct'] = $grupo['camas_teorica'] > 0
                ? round(($grupo['camas_real'] / $grupo['camas_teorica']) * 100, 1)
                : null;
        }
        unset($grupo);

        usort($grupos, static function ($a, $b) {
            return $b['camas_teorica'] <=> $a['camas_teorica'];
        });

        $columnas = [];
        foreach ($resumenPor as $dimension) {
            $columnas[] = ['data' => $dimension, 'title' => self::DIMENSIONES_RESUMEN[$dimension]];
        }
        $columnas[] = ['data' => 'camas_real', 'title' => 'Camas reales'];
        $columnas[] = ['data' => 'camas_teorica', 'title' => 'Camas teóricas'];
        $columnas[] = ['data' => 'diferencia', 'title' => 'Diferencia'];
        $columnas[] = ['data' => 'cumplimiento_pct', 'title' => 'Cumplimiento %'];

        return ['columns' => $columnas, 'rows' => array_values($grupos)];
    }
}
