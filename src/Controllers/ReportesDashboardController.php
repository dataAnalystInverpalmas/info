<?php
namespace App\Controllers;

use App\Helpers\FechasReporte;
use App\Models\ReportePlane;

class ReportesDashboardController {

    private const DIMENSIONES_RESUMEN = [
        'finca' => 'Finca',
        'flor' => 'Flor',
        'variedad' => 'Variedad',
        'temporada' => 'Temporada',
        'ciclo' => 'Ciclo',
        'color' => 'Color',
    ];

    public function index() {
        $filtrosOpciones = ReportePlane::opcionesFiltro();
        $rangoFechasDefault = FechasReporte::rangoDesdeInicioAnoHastaDomingoAnterior();
        // La fecha desde por defecto arranca en la siembra de clavel/miniclavel más
        // antigua (ciclo largo), no en el inicio de año, para que el gráfico de
        // camas por edad muestre historial suficiente sin necesitar un filtro propio.
        $fechaMinimaClavel = ReportePlane::fechaMinimaClavel();
        if (!empty($fechaMinimaClavel)) {
            $rangoFechasDefault['desde'] = $fechaMinimaClavel;
        }
        require_once __DIR__ . '/../Views/ReportesDashboard/index.php';
    }

    private static function extraerFiltros(array $get): array {
        return [
            'finca' => $get['finca'] ?? [],
            'bloque' => $get['bloque'] ?? [],
            'variedad' => $get['variedad'] ?? [],
            'temporada' => $get['temporada'] ?? [],
            'flor' => $get['flor'] ?? [],
            'color' => $get['color'] ?? [],
            'desde' => $get['desde'] ?? '',
            'hasta' => $get['hasta'] ?? '',
        ];
    }

    public static function datos(array $get): array {
        $filtros = self::extraerFiltros($get);
        $resumenPor = array_values(array_intersect(
            (array)($get['resumen_por'] ?? []),
            array_keys(self::DIMENSIONES_RESUMEN)
        ));

        $filas = ReportePlane::obtenerDatos($filtros);

        return [
            'kpis' => self::construirKpis($filas),
            'chart1' => self::construirChartPorFinca($filas),
            'chart2' => self::construirChartPorSemana($filas),
            'chartEdad' => self::construirChartEdad($filas),
            'table' => self::construirTabla($filas),
            'resumen' => self::construirResumen($filas, $resumenPor),
        ];
    }

    /**
     * Opciones de filtro en cascada: se recalculan a partir de los filtros ya
     * seleccionados (cada uno excluyéndose a sí mismo, ver ReportePlane::opcionesFiltro).
     */
    public static function opciones(array $get): array {
        return ReportePlane::opcionesFiltro(self::extraerFiltros($get));
    }

    private static function construirKpis(array $filas): array {
        $totalCamas = 0;
        $totalPlantas = 0;
        $fincas = [];
        $variedades = [];

        foreach ($filas as $fila) {
            $totalCamas += (int)$fila['camas'];
            $totalPlantas += (int)$fila['plantas'];
            $fincas[$fila['finca']] = true;
            $variedades[$fila['variedad']] = true;
        }

        return [
            ['label' => 'Camas totales', 'value' => number_format($totalCamas, 0, ',', '.'), 'help' => count($filas) . ' registro(s)'],
            ['label' => 'Plantas totales', 'value' => number_format($totalPlantas, 0, ',', '.'), 'help' => 'Suma de plantas en el filtro'],
            ['label' => 'Fincas', 'value' => (string)count($fincas), 'help' => 'Fincas con datos en el filtro'],
            ['label' => 'Variedades', 'value' => (string)count($variedades), 'help' => 'Variedades distintas'],
        ];
    }

    private static function construirChartPorFinca(array $filas): array {
        $porFinca = [];
        foreach ($filas as $fila) {
            $finca = $fila['finca'];
            $porFinca[$finca] = ($porFinca[$finca] ?? 0) + (int)$fila['camas'];
        }
        arsort($porFinca);

        return [
            'labels' => array_keys($porFinca),
            'datasets' => [[
                'label' => 'Camas',
                'data' => array_values($porFinca),
                'backgroundColor' => '#00796B',
            ]],
        ];
    }

    private static function construirChartPorSemana(array $filas): array {
        $porSemana = [];
        foreach ($filas as $fila) {
            $semana = $fila['semana_yyww'] ?? null;
            $semana = $semana !== null ? (string)$semana : 'N/D';
            $porSemana[$semana] = ($porSemana[$semana] ?? 0) + (int)$fila['camas'];
        }
        ksort($porSemana);

        return [
            'labels' => array_keys($porSemana),
            'datasets' => [[
                'label' => 'Camas',
                'data' => array_values($porSemana),
                'borderColor' => '#00796B',
                'backgroundColor' => 'rgba(0, 121, 107, 0.15)',
                'fill' => true,
            ]],
        ];
    }

    /**
     * Camas agrupadas por edad (semanas desde fecha_siembra hasta hoy), reutilizando
     * las filas ya traídas por obtenerDatos() con los filtros generales del sidebar
     * (no hace una segunda consulta a la BD).
     */
    private static function construirChartEdad(array $filas): array {
        $hoy = new \DateTimeImmutable('today');
        $porEdad = [];

        foreach ($filas as $fila) {
            if (empty($fila['fecha_siembra'])) {
                continue;
            }
            $fechaSiembra = new \DateTimeImmutable((string)$fila['fecha_siembra']);
            if ($fechaSiembra > $hoy) {
                continue;
            }
            $edadSemanas = (int)floor($hoy->diff($fechaSiembra)->days / 7);
            $porEdad[$edadSemanas] = ($porEdad[$edadSemanas] ?? 0) + (int)$fila['camas'];
        }
        ksort($porEdad);

        return [
            'labels' => array_map('strval', array_keys($porEdad)),
            'datasets' => [[
                'label' => 'Camas',
                'data' => array_values($porEdad),
                'backgroundColor' => '#00796B',
            ]],
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
            ['data' => 'ciclo', 'title' => 'Ciclo'],
            ['data' => 'tipo_siembra', 'title' => 'Tipo siembra'],
            ['data' => 'camas', 'title' => 'Camas'],
            ['data' => 'semana_yyww', 'title' => 'Semana (yyww ISO)'],
        ];

        $rows = array_map(static function (array $fila): array {
            $fila['camas'] = (int)round((float)$fila['camas']);
            return $fila;
        }, $filas);

        return ['columns' => $columnas, 'rows' => $rows];
    }

    /**
     * Resumen dinámico: agrupa el detalle ya calculado por 1 o varias dimensiones
     * elegidas por el usuario, sumando camas. No vuelve a consultar la BD.
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
                $grupos[$llave]['camas'] = 0;
            }
            $grupos[$llave]['camas'] += (int)$fila['camas'];
        }

        usort($grupos, static function ($a, $b) {
            return $b['camas'] <=> $a['camas'];
        });

        $columnas = [];
        foreach ($resumenPor as $dimension) {
            $columnas[] = ['data' => $dimension, 'title' => self::DIMENSIONES_RESUMEN[$dimension]];
        }
        $columnas[] = ['data' => 'camas', 'title' => 'Camas'];

        return ['columns' => $columnas, 'rows' => array_values($grupos)];
    }
}
