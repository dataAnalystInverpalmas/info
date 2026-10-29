<?php
declare(strict_types=1);
namespace App\Models;

use App\Helpers\Database;

class DashboardProyecciones
{
    private const TABLE_NAME = 'plane';

    public static function getDashboardData($rawFilters = [])
    {
        $conexion = Database::getConnection();

        if (!$conexion) {
            return self::errorResponse('No hay conexion activa a la base de datos.', 'Conexion mysqli no disponible');
        }

        $filters = self::normalizeFilters($rawFilters);
        $where = self::buildWhere($filters);

        $totals = self::fetchOne(
            $conexion,
            'SELECT '
            . 'COALESCE(SUM(plantas), 0) AS total_plantas, '
            . 'ROUND(COALESCE(SUM(plantas), 0) / 960, 0) AS total_camas '
            . 'FROM ' . self::qi(self::TABLE_NAME) . ' '
            . $where['sql'],
            $where['types'],
            $where['params']
        );

        if (!$totals) {
            return self::errorResponse('No fue posible consultar plane.', $conexion->error);
        }

        $totalPlantas = (float)($totals['total_plantas'] ?? 0);
        $totalCamas = (float)($totals['total_camas'] ?? 0);

        $camasPorFincaFlorRows = self::fetchAll(
            $conexion,
            'SELECT '
            . 'finca, '
            . "COALESCE(NULLIF(TRIM(producto), ''), 'SIN FLOR') AS flor, "
            . 'COALESCE(SUM(plantas), 0) AS total_plantas, '
            . 'ROUND(COALESCE(SUM(plantas), 0) / 960, 0) AS camas '
            . 'FROM ' . self::qi(self::TABLE_NAME) . ' '
            . $where['sql'] . ' '
            . 'GROUP BY finca, flor '
            . 'ORDER BY finca ASC, camas DESC',
            $where['types'],
            $where['params']
        );

        $camasPorFlorRows = self::fetchAll(
            $conexion,
            'SELECT '
            . "COALESCE(NULLIF(TRIM(producto), ''), 'SIN FLOR') AS flor, "
            . 'COALESCE(SUM(plantas), 0) AS total_plantas, '
            . 'ROUND(COALESCE(SUM(plantas), 0) / 960, 0) AS camas '
            . 'FROM ' . self::qi(self::TABLE_NAME) . ' '
            . $where['sql'] . ' '
            . 'GROUP BY flor',
            $where['types'],
            $where['params']
        );

        $whereSoloClavel = self::buildWhere($filters, [
            'UPPER(producto) LIKE ? AND UPPER(producto) NOT LIKE ?',
            'ss',
            ['%CLAVEL%', '%MINICLAVEL%'],
        ], false);

        $camasEdadClavelRows = self::fetchAll(
            $conexion,
            'SELECT '
            . 'finca, '
            . 'GREATEST(FLOOR(DATEDIFF(CURRENT_DATE, fecha_siembra) / 7), 0) AS edad_semanas, '
            . 'ROUND(COALESCE(SUM(plantas), 0) / 960, 0) AS camas '
            . 'FROM ' . self::qi(self::TABLE_NAME) . ' '
            . $whereSoloClavel['sql'] . ' '
            . 'GROUP BY finca, edad_semanas '
            . 'ORDER BY finca ASC, edad_semanas ASC',
            $whereSoloClavel['types'],
            $whereSoloClavel['params']
        );

        $whereMiniclavel = self::buildWhere($filters, [
            'UPPER(producto) LIKE ?',
            's',
            ['%MINICLAVEL%'],
        ], false);

        $camasEdadMiniclavelRows = self::fetchAll(
            $conexion,
            'SELECT '
            . 'finca, '
            . 'GREATEST(FLOOR(DATEDIFF(CURRENT_DATE, fecha_siembra) / 7), 0) AS edad_semanas, '
            . 'ROUND(COALESCE(SUM(plantas), 0) / 960, 0) AS camas '
            . 'FROM ' . self::qi(self::TABLE_NAME) . ' '
            . $whereMiniclavel['sql'] . ' '
            . 'GROUP BY finca, edad_semanas '
            . 'ORDER BY finca ASC, edad_semanas ASC',
            $whereMiniclavel['types'],
            $whereMiniclavel['params']
        );

        $catalogoFincasRows = self::fetchAll(
            $conexion,
            'SELECT DISTINCT finca FROM ' . self::qi(self::TABLE_NAME) . ' WHERE finca IS NOT NULL AND TRIM(finca) <> \'\' ORDER BY finca ASC',
            '',
            []
        );
        $fincasDisponibles = array_values(array_filter(array_map(function ($r) {
            return trim((string)($r['finca'] ?? ''));
        }, $catalogoFincasRows)));

        $catalogoFloresRows = self::fetchAll(
            $conexion,
            'SELECT DISTINCT COALESCE(NULLIF(TRIM(producto), \'\'), \'SIN FLOR\') AS flor FROM ' . self::qi(self::TABLE_NAME) . ' WHERE plantas > 0 ORDER BY flor ASC',
            '',
            []
        );
        $floresDisponibles = array_values(array_filter(array_map(function ($r) {
            return trim((string)($r['flor'] ?? ''));
        }, $catalogoFloresRows)));

        $picoClavel = null;
        foreach ($camasEdadClavelRows as $r) {
            $camasVal = (float)($r['camas'] ?? 0);
            if (!$picoClavel || $camasVal > (float)($picoClavel['camas'] ?? 0)) {
                $picoClavel = [
                    'semana' => (int)($r['edad_semanas'] ?? 0),
                    'finca' => (string)($r['finca'] ?? ''),
                    'camas' => $camasVal,
                ];
            }
        }

        $picoMiniclavel = null;
        foreach ($camasEdadMiniclavelRows as $r) {
            $camasVal = (float)($r['camas'] ?? 0);
            if (!$picoMiniclavel || $camasVal > (float)($picoMiniclavel['camas'] ?? 0)) {
                $picoMiniclavel = [
                    'semana' => (int)($r['edad_semanas'] ?? 0),
                    'finca' => (string)($r['finca'] ?? ''),
                    'camas' => $camasVal,
                ];
            }
        }

        // --- CÁLCULO DE COMPARATIVA E INDICADORES DE VARIACIÓN (Task 2) ---
        $prevFilters = $filters;
        $diffDays = 365;
        if (!empty($filters['fecha_desde']) && !empty($filters['fecha_hasta'])) {
            try {
                $d1 = new \DateTime($filters['fecha_desde']);
                $d2 = new \DateTime($filters['fecha_hasta']);
                $diffDays = max(1, (int)$d1->diff($d2)->days + 1);
                $prevEnd = (clone $d1)->modify('-1 day')->format('Y-m-d');
                $prevStart = (clone $d1)->modify("-{$diffDays} days")->format('Y-m-d');
                $prevFilters['fecha_desde'] = $prevStart;
                $prevFilters['fecha_hasta'] = $prevEnd;
            } catch (\Throwable $e) {
                $prevFilters['fecha_desde'] = date('Y-m-d', strtotime('-730 days'));
                $prevFilters['fecha_hasta'] = date('Y-m-d', strtotime('-366 days'));
            }
        } else {
            $prevFilters['fecha_desde'] = date('Y-m-d', strtotime('-730 days'));
            $prevFilters['fecha_hasta'] = date('Y-m-d', strtotime('-366 days'));
        }

        $wherePrev = self::buildWhere($prevFilters);
        $prevTotals = self::fetchOne(
            $conexion,
            'SELECT '
            . 'COALESCE(SUM(plantas), 0) AS total_plantas, '
            . 'ROUND(COALESCE(SUM(plantas), 0) / 960, 0) AS total_camas '
            . 'FROM ' . self::qi(self::TABLE_NAME) . ' '
            . $wherePrev['sql'],
            $wherePrev['types'],
            $wherePrev['params']
        );

        $prevPlantas = (float)($prevTotals['total_plantas'] ?? 0);
        $prevCamas = (float)($prevTotals['total_camas'] ?? 0);

        $deltaCamas = $totalCamas - $prevCamas;
        $pctCambioCamas = ($prevCamas > 0) ? round((($totalCamas - $prevCamas) / $prevCamas) * 100, 1) : null;
        $deltaPlantas = $totalPlantas - $prevPlantas;
        $pctCambioPlantas = ($prevPlantas > 0) ? round((($totalPlantas - $prevPlantas) / $prevPlantas) * 100, 1) : null;

        $densidadReal = ($totalCamas > 0) ? round($totalPlantas / $totalCamas, 0) : 960;
        $semanasRango = max(1, (int)round($diffDays / 7));
        $ritmoSiembraSemanal = round($totalCamas / $semanasRango, 1);

        // Finca Líder
        $fincaLider = null;
        $fincasTotales = [];
        foreach ($camasPorFincaFlorRows as $row) {
            $f = (string)($row['finca'] ?? '');
            if (!isset($fincasTotales[$f])) {
                $fincasTotales[$f] = 0;
            }
            $fincasTotales[$f] += (float)($row['camas'] ?? 0);
        }
        arsort($fincasTotales);
        if (!empty($fincasTotales)) {
            $topFincaName = array_key_first($fincasTotales);
            $topFincaCamas = $fincasTotales[$topFincaName];
            $topFincaPct = ($totalCamas > 0) ? round(($topFincaCamas / $totalCamas) * 100, 1) : 0;
            $fincaLider = [
                'finca' => $topFincaName,
                'camas' => $topFincaCamas,
                'porcentaje' => $topFincaPct,
            ];
        }

        // Variedad Top
        $florTop = null;
        if (!empty($camasPorFlorRows)) {
            $topRow = $camasPorFlorRows[0];
            $topFlorCamas = (float)($topRow['camas'] ?? 0);
            $topFlorPct = ($totalCamas > 0) ? round(($topFlorCamas / $totalCamas) * 100, 1) : 0;
            $florTop = [
                'flor' => (string)($topRow['flor'] ?? ''),
                'camas' => $topFlorCamas,
                'porcentaje' => $topFlorPct,
            ];
        }

        $comparativa = [
            'periodoPrevio' => [
                'fecha_desde' => $prevFilters['fecha_desde'] ?? null,
                'fecha_hasta' => $prevFilters['fecha_hasta'] ?? null,
                'camas' => $prevCamas,
                'plantas' => $prevPlantas,
            ],
            'variacionCamas' => [
                'delta' => $deltaCamas,
                'porcentaje' => $pctCambioCamas,
                'tendencia' => ($pctCambioCamas === null) ? 'neutro' : ($pctCambioCamas > 0 ? 'arriba' : ($pctCambioCamas < 0 ? 'abajo' : 'neutro')),
            ],
            'variacionPlantas' => [
                'delta' => $deltaPlantas,
                'porcentaje' => $pctCambioPlantas,
                'tendencia' => ($pctCambioPlantas === null) ? 'neutro' : ($pctCambioPlantas > 0 ? 'arriba' : ($pctCambioPlantas < 0 ? 'abajo' : 'neutro')),
            ],
            'densidadReal' => $densidadReal,
            'densidadTeorica' => 960,
            'ritmoSiembraSemanal' => $ritmoSiembraSemanal,
            'fincaLider' => $fincaLider,
            'florTop' => $florTop,
        ];

        return [
            'ok' => true,
            'message' => 'Datos de camas sembradas cargados correctamente.',
            'filters' => $filters,
            'totales' => [
                'plantas' => $totalPlantas,
                'camas' => $totalCamas,
            ],
            'comparativa' => $comparativa,
            'fincasDisponibles' => $fincasDisponibles,
            'floresDisponibles' => $floresDisponibles,
            'picoClavel' => $picoClavel,
            'picoMiniclavel' => $picoMiniclavel,
            'camasPorFincaFlor' => self::normalizeDetailRows($camasPorFincaFlorRows),
            'camasPorFlor' => self::normalizeFlorRows($camasPorFlorRows),
            'camasEdadClavel' => self::normalizeEdadRows($camasEdadClavelRows, 'CLAVEL'),
            'camasEdadMiniclavel' => self::normalizeEdadRows($camasEdadMiniclavelRows, 'MINICLAVEL'),
            'chartCamasEdadClavel' => self::toEdadByFincaPayload($camasEdadClavelRows),
            'chartCamasEdadMiniclavel' => self::toEdadByFincaPayload($camasEdadMiniclavelRows),
        ];
    }

    private static function normalizeFilters($raw)
    {
        $today = date('Y-m-d');
        $defaultFrom = date('Y-m-d', strtotime('-365 days'));

        $sinFiltroFechas = !empty($raw['sin_rango_fechas']) && ($raw['sin_rango_fechas'] === '1' || $raw['sin_rango_fechas'] === 'true');

        if ($sinFiltroFechas) {
            $fechaDesde = null;
            $fechaHasta = null;
        } else {
            $fechaDesde = self::normalizeDate(isset($raw['fecha_desde']) ? $raw['fecha_desde'] : '');
            $fechaHasta = self::normalizeDate(isset($raw['fecha_hasta']) ? $raw['fecha_hasta'] : '');

            if (!$fechaDesde && !$fechaHasta) {
                $fechaDesde = $defaultFrom;
                $fechaHasta = $today;
            } elseif ($fechaDesde && !$fechaHasta) {
                $fechaHasta = $today;
            } elseif (!$fechaDesde && $fechaHasta) {
                $fechaDesde = $defaultFrom;
            }

            if ($fechaDesde && $fechaHasta && $fechaDesde > $fechaHasta) {
                $tmp = $fechaDesde;
                $fechaDesde = $fechaHasta;
                $fechaHasta = $tmp;
            }
        }

        return [
            'finca' => self::cleanText(isset($raw['finca']) ? $raw['finca'] : ''),
            'producto' => self::cleanText(isset($raw['producto']) ? $raw['producto'] : ''),
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ];
    }

    private static function normalizeDate($value)
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        $dt = \DateTime::createFromFormat('Y-m-d', $value);
        if ($dt && $dt->format('Y-m-d') === $value) {
            return $value;
        }

        return null;
    }

    private static function cleanText($value)
    {
        return trim((string)$value);
    }

    private static function buildWhere($filters, $extraCondition = null, $applyDateRange = true)
    {
        $conditions = ['plantas > 0'];
        $types = '';
        $params = [];

        if ($applyDateRange && !empty($filters['fecha_desde']) && !empty($filters['fecha_hasta'])) {
            $conditions[] = 'fecha_siembra BETWEEN ? AND ?';
            $types .= 'ss';
            $params[] = $filters['fecha_desde'];
            $params[] = $filters['fecha_hasta'];
        }

        if ($filters['finca'] !== '') {
            $conditions[] = 'finca = ?';
            $types .= 's';
            $params[] = $filters['finca'];
        }

        if ($filters['producto'] !== '') {
            $conditions[] = 'UPPER(producto) = ?';
            $types .= 's';
            $params[] = strtoupper($filters['producto']);
        }

        if (is_array($extraCondition) && count($extraCondition) === 3) {
            $conditions[] = $extraCondition[0];
            $types .= (string)$extraCondition[1];
            foreach ((array)$extraCondition[2] as $extraParam) {
                $params[] = $extraParam;
            }
        }

        $sql = '';
        if (!empty($conditions)) {
            $sql = 'WHERE ' . implode(' AND ', $conditions);
        }

        return [
            'sql' => $sql,
            'types' => $types,
            'params' => $params,
        ];
    }

    private static function fetchOne($conexion, $sql, $types, $params)
    {
        $rows = self::fetchAll($conexion, $sql, $types, $params);
        if (empty($rows)) {
            return null;
        }

        return $rows[0];
    }

    private static function fetchAll($conexion, $sql, $types, $params)
    {
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            return [];
        }

        if ($types !== '' && !empty($params)) {
            $bindArgs = [$types];
            foreach ($params as $idx => $value) {
                $bindArgs[] = &$params[$idx];
            }
            call_user_func_array([$stmt, 'bind_param'], $bindArgs);
        }

        if (!$stmt->execute()) {
            $stmt->close();
            return [];
        }

        $result = $stmt->get_result();
        $rows = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $result->free();
        }

        $stmt->close();
        return $rows;
    }

    private static function normalizeDetailRows(array $rows): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $normalized[] = [
                'finca' => (string)($row['finca'] ?? ''),
                'flor' => (string)($row['flor'] ?? 'SIN FLOR'),
                'plantas' => (float)($row['total_plantas'] ?? 0),
                'camas' => (float)($row['camas'] ?? 0),
            ];
        }
        return $normalized;
    }

    private static function normalizeFlorRows(array $rows): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $normalized[] = [
                'flor' => (string)($row['flor'] ?? 'SIN FLOR'),
                'plantas' => (float)($row['total_plantas'] ?? 0),
                'camas' => (float)($row['camas'] ?? 0),
            ];
        }
        return $normalized;
    }

    private static function normalizeEdadRows(array $rows, string $flor): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $normalized[] = [
                'finca' => (string)($row['finca'] ?? ''),
                'flor' => $flor,
                'edad_semanas' => (int)($row['edad_semanas'] ?? 0),
                'camas' => (float)($row['camas'] ?? 0),
            ];
        }
        return $normalized;
    }

    private static function toEdadByFincaPayload(array $rows): array
    {
        $edades = [];
        $fincas = [];
        $matrix = [];

        foreach ($rows as $row) {
            $finca = trim((string)($row['finca'] ?? 'SIN FINCA'));
            $edad = (int)($row['edad_semanas'] ?? 0);
            $camas = (float)($row['camas'] ?? 0);

            $edades[$edad] = true;
            $fincas[$finca] = true;
            if (!isset($matrix[$finca])) {
                $matrix[$finca] = [];
            }
            $matrix[$finca][$edad] = $camas;
        }

        $maxEdad = empty($edades) ? 0 : max(array_keys($edades));
        $labels = range(0, $maxEdad);

        $datasets = [];
        foreach (array_keys($fincas) as $finca) {
            $serie = [];
            foreach ($labels as $edad) {
                $serie[] = isset($matrix[$finca][$edad]) ? (float)$matrix[$finca][$edad] : 0.0;
            }
            $datasets[] = [
                'label' => $finca,
                'data' => $serie,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }

    private static function errorResponse($message, $error)
    {
        return [
            'ok' => false,
            'message' => $message,
            'error' => $error,
            'totales' => ['plantas' => 0, 'camas' => 0],
            'camasPorFincaFlor' => [],
            'camasPorFlor' => [],
            'camasEdadClavel' => [],
            'camasEdadMiniclavel' => [],
            'chartCamasEdadClavel' => ['labels' => [], 'datasets' => []],
            'chartCamasEdadMiniclavel' => ['labels' => [], 'datasets' => []],
        ];
    }

    private static function qi($identifier)
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
