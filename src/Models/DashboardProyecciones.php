<?php
declare(strict_types=1);
namespace App\Models;

use App\Helpers\Database;

class DashboardProyecciones
{
    const TABLE_NAME = 'ld_proyecciones';
    const TABLE_ROWS_LIMIT = 500;
    const CHART_TOP_LIMIT = 20;

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
            'SELECT COUNT(*) AS total_rows, COALESCE(SUM(tallos), 0) AS total_tallos FROM ' . self::qi(self::TABLE_NAME) . ' ' . $where['sql'],
            $where['types'],
            $where['params']
        );

        if (!$totals) {
            return self::errorResponse('No fue posible consultar ld_proyecciones.', $conexion->error);
        }

        $totalRows = (int)($totals['total_rows'] ?? 0);
        $totalTallos = (float)($totals['total_tallos'] ?? 0);

        $emptyFlorCards = ['CLA' => ['RE' => 0, 'PT' => 0, 'AJ' => 0], 'CM0' => ['RE' => 0, 'PT' => 0, 'AJ' => 0], 'ROC' => ['RE' => 0, 'PT' => 0, 'AJ' => 0], 'ROS' => ['RE' => 0, 'PT' => 0, 'AJ' => 0]];

        if ($totalRows === 0) {
            return [
                'ok' => true,
                'message' => 'No hay datos para los filtros seleccionados.',
                'filters' => $filters,
                'florCards' => $emptyFlorCards,
                'kpis' => [
                    ['label' => 'Registros', 'value' => 0],
                    ['label' => 'Total tallos', 'value' => 0],
                ],
                'chartByType' => ['labels' => [], 'data' => []],
                'chartByVariety' => ['labels' => [], 'data' => []],
                'chartByDate' => ['labels' => [], 'data' => []],
                'chartByGroup' => ['labels' => [], 'data' => []],
                'chartByGroupFincaFlorTipo' => ['labels' => [], 'data' => []],
                'chartByGroupFlorTipo' => ['labels' => [], 'data' => []],
                'chartByGroupVariedadTipo' => ['labels' => [], 'data' => []],
                'tableColumns' => ['fecha', 'finca', 'flor', 'variedad', 'tipo', 'tallos'],
                'tableRows' => [],
            ];
        }

        // Flower cards: CLA, CM0, ROC, ROS x RE, PT, AJ
        $florCardExtra = "flor IN ('CLA','CM0','ROC','ROS') AND tipo IN ('RE','PT','AJ')";
        $florCardSql = ($where['sql'] === '')
            ? 'WHERE ' . $florCardExtra
            : $where['sql'] . ' AND ' . $florCardExtra;

        $florCardRows = self::fetchAll(
            $conexion,
            'SELECT flor, tipo, COALESCE(SUM(tallos), 0) AS total '
            . 'FROM ' . self::qi(self::TABLE_NAME) . ' '
            . $florCardSql . ' '
            . 'GROUP BY flor, tipo',
            $where['types'],
            $where['params']
        );

        $florCards = [
            'CLA' => ['RE' => 0, 'PT' => 0, 'AJ' => 0],
            'CM0' => ['RE' => 0, 'PT' => 0, 'AJ' => 0],
            'ROC' => ['RE' => 0, 'PT' => 0, 'AJ' => 0],
            'ROS' => ['RE' => 0, 'PT' => 0, 'AJ' => 0],
        ];
        foreach ($florCardRows as $row) {
            $flor = $row['flor'] ?? '';
            $tipo = $row['tipo'] ?? '';
            if (isset($florCards[$flor][$tipo])) {
                $florCards[$flor][$tipo] = (float)$row['total'];
            }
        }

        // RE se calcula desde quipus_vs_proy para evitar huecos de carga en ld_proyecciones.
        $whereQvp = self::buildQvpWhere($filters);
        $florCardRealRows = self::fetchAll(
            $conexion,
            'SELECT flor, COALESCE(SUM(`inverpalmas-real`), 0) AS total '
            . 'FROM quipus_vs_proy '
            . $whereQvp['sql'] . ' '
            . 'GROUP BY flor',
            $whereQvp['types'],
            $whereQvp['params']
        );

        foreach ($florCardRealRows as $row) {
            $flor = $row['flor'] ?? '';
            if (isset($florCards[$flor])) {
                $florCards[$flor]['RE'] = (float)$row['total'];
            }
        }

        // ==========================================
        // 1. PLANTAS SEMBRADAS POR FLOR (de tabla plane)
        // ==========================================
        // Mapea el producto o variedad_reem etc, pero típicamente 'producto' o sub-campo indica el tipo de flor.
        // O si no, podemos usar 'producto' directo.
        $plantasPorFlor = self::fetchAll(
            $conexion,
            "SELECT producto AS label, COALESCE(SUM(plantas), 0) AS total 
             FROM plane 
             GROUP BY producto 
             ORDER BY total DESC",
             "",
             []
        );

        // ==========================================
        // 2. EDADES Y CANTIDAD DE PLANTAS
        // Edad aproximada en semanas/meses o rangos de edad basados en la fecha_siembra.
        // Agrupamos por rangos de semanas de sembrado (Semanas transcurridas de fecha_siembra a hoy).
        // ==========================================
        $edadesYPlantas = self::fetchAll(
            $conexion,
            "SELECT 
                CASE 
                    WHEN DATEDIFF(CURRENT_DATE, fecha_siembra)/7 < 17 THEN '0-17 Semanas (Vegetativo)'
                    WHEN DATEDIFF(CURRENT_DATE, fecha_siembra)/7 < 35 THEN '18-35 Semanas (1Pico)'
                    WHEN DATEDIFF(CURRENT_DATE, fecha_siembra)/7 < 43 THEN '36-43 Semanas (Valle)'
                    WHEN DATEDIFF(CURRENT_DATE, fecha_siembra)/7 < 68 THEN '44-68 Semanas (2Pico)'
                    ELSE 'Más de 68 Semanas'
                END AS label,
                COALESCE(SUM(plantas), 0) AS total
             FROM plane
             WHERE fecha_siembra IS NOT NULL AND plantas > 0
             GROUP BY label
             ORDER BY MIN(DATEDIFF(CURRENT_DATE, fecha_siembra))",
             "",
             []
        );

        // ==========================================
        // 3. DISTRIBUCIÓN DE SIEMBRAS POR COLOR
        // ==========================================
        $distribucionColor = self::fetchAll(
            $conexion,
            "SELECT COALESCE(NULLIF(TRIM(v.color), ''), 'SIN COLOR') AS label, COALESCE(SUM(p.plantas), 0) AS total 
             FROM plane p
             LEFT JOIN varieties v ON v.nombre = p.variedad
             WHERE p.plantas > 0
             GROUP BY label 
             ORDER BY total DESC 
             LIMIT 8",
             "",
             []
        );

        return [
            'ok' => true,
            'message' => 'Datos cargados correctamente.',
            'filters' => $filters,
            'florCards' => $florCards,
            'plantasPorFlor' => self::toChartPayload($plantasPorFlor),
            'edadesYPlantas' => self::toChartPayload($edadesYPlantas),
            'distribucionColor' => self::toChartPayload($distribucionColor),
            // Compatibilidad temporal con el frontend previo
            'distribucionVariedad' => self::toChartPayload($distribucionColor),
        ];
    }

    private static function normalizeFilters($raw)
    {
        $today = date('Y-m-d');
        $defaultFrom = '2025-12-29';

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

        if ($fechaDesde > $fechaHasta) {
            $tmp = $fechaDesde;
            $fechaDesde = $fechaHasta;
            $fechaHasta = $tmp;
        }

        return [
            'finca' => self::cleanText(isset($raw['finca']) ? $raw['finca'] : ''),
            'flor' => self::cleanText(isset($raw['flor']) ? $raw['flor'] : ''),
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

    private static function buildWhere($filters)
    {
        $conditions = [];
        $types = '';
        $params = [];

        if (!empty($filters['fecha_desde']) && !empty($filters['fecha_hasta'])) {
            $conditions[] = 'DATE(fecha) BETWEEN ? AND ?';
            $types .= 'ss';
            $params[] = $filters['fecha_desde'];
            $params[] = $filters['fecha_hasta'];
        }

        if ($filters['finca'] !== '') {
            $conditions[] = 'finca = ?';
            $types .= 's';
            $params[] = $filters['finca'];
        }

        if ($filters['flor'] !== '') {
            $conditions[] = 'flor = ?';
            $types .= 's';
            $params[] = strtoupper($filters['flor']);
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

    private static function buildQvpWhere($filters)
    {
        $conditions = ["`inverpalmas-real` IS NOT NULL", "`inverpalmas-real` > 0", "flor IN ('CLA','CM0','ROC','ROS')"];
        $types = '';
        $params = [];

        if (!empty($filters['fecha_desde']) && !empty($filters['fecha_hasta'])) {
            $conditions[] = "STR_TO_DATE(CONCAT('20', FLOOR(sem_prod/100), ' ', sem_prod % 100, ' Monday'), '%Y %u %W') BETWEEN ? AND ?";
            $types .= 'ss';
            $params[] = $filters['fecha_desde'];
            $params[] = $filters['fecha_hasta'];
        }

        if ($filters['finca'] !== '') {
            $conditions[] = 'finca = ?';
            $types .= 's';
            $params[] = $filters['finca'];
        }

        if ($filters['flor'] !== '') {
            $conditions[] = 'flor = ?';
            $types .= 's';
            $params[] = strtoupper($filters['flor']);
        }

        return [
            'sql' => 'WHERE ' . implode(' AND ', $conditions),
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

    private static function toChartPayload($rows)
    {
        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $label = trim((string)($row['label'] ?? ''));
            $labels[] = ($label !== '') ? $label : 'Sin dato';
            $data[] = (float)($row['total'] ?? 0);
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    private static function calculateTypeDifference($chartByTypeRows)
    {
        if (count($chartByTypeRows) < 2) {
            return [
                'firstType' => isset($chartByTypeRows[0]['label']) ? $chartByTypeRows[0]['label'] : null,
                'secondType' => null,
                'difference' => 0,
            ];
        }

        $first = $chartByTypeRows[0];
        $second = $chartByTypeRows[1];

        return [
            'firstType' => $first['label'],
            'secondType' => $second['label'],
            'difference' => (float)$first['total'] - (float)$second['total'],
        ];
    }

    private static function errorResponse($message, $error)
    {
        return [
            'ok' => false,
            'message' => $message,
            'error' => $error,
            'kpis' => [],
            'chartByType' => ['labels' => [], 'data' => []],
            'chartByVariety' => ['labels' => [], 'data' => []],
            'chartByDate' => ['labels' => [], 'data' => []],
            'chartByGroup' => ['labels' => [], 'data' => []],
            'chartByGroupFincaFlorTipo' => ['labels' => [], 'data' => []],
            'chartByGroupFlorTipo' => ['labels' => [], 'data' => []],
            'chartByGroupVariedadTipo' => ['labels' => [], 'data' => []],
            'tableColumns' => [],
            'tableRows' => [],
        ];
    }

    private static function qi($identifier)
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
}
