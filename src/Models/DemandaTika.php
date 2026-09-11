<?php
declare(strict_types=1);
namespace App\Models;

use App\Helpers\Database;

/**
 * Demanda semanal (tika_demanda) cruzada con variedad/flor y color, para el
 * reporte pivote "Demanda Semanal".
 */
class DemandaTika
{
    private const BASE_FROM = "tika_demanda d
        INNER JOIN ld_variedades v ON REPLACE(d.item_demanda, '_', ' ') = v.nombre
        INNER JOIN ld_colores c ON c.codigo = v.codgcol";

    /** Columnas reales (calificadas) para cada filtro de multi-selección. */
    private const FILTER_COLUMNS = [
        'flor'          => 'v.codflor',
        'mercado'       => 'd.mercado',
        'submercado'    => 'd.submercado',
        'grupo_demanda' => 'd.grupo_demanda',
        'item'          => 'd.item_demanda',
    ];

    /** Columnas permitidas para agrupar el gráfico resumen (whitelist). */
    private const RESUMEN_COLUMNAS = [
        'color'         => 'c.orden',
        'mercado'       => 'd.mercado',
        'submercado'    => 'd.submercado',
        'grupo_demanda' => 'd.grupo_demanda',
    ];

    public const RESUMEN_POR_DEFECTO = 'mercado';

    /** Cuántas semanas hacia adelante desde la semana actual cubre el rango por defecto. */
    public const SEMANAS_ADELANTE_POR_DEFECTO = 52;

    public static function getPivotData(array $rawFilters = [], string $agruparResumen = self::RESUMEN_POR_DEFECTO): array
    {
        $conexion = Database::getConnection();
        if (!$conexion) {
            return self::errorResponse('No hay conexion activa a la base de datos.');
        }

        $filters = self::normalizeFilters($rawFilters);
        [$where, $types, $params] = self::buildWhere($filters);

        $filasRaw = self::fetchAll(
            $conexion,
            "SELECT
                v.codflor AS flor,
                c.orden AS color,
                d.item_demanda AS item,
                d.semana AS semana,
                ROUND(SUM(d.cantidad), 2) AS cantidad
             FROM " . self::BASE_FROM . "
             $where
             GROUP BY v.codflor, c.orden, d.item_demanda, d.semana
             ORDER BY v.codflor ASC, c.orden ASC, d.item_demanda ASC, d.semana ASC",
            $types,
            $params
        );

        $semanas = array_values(array_unique(array_column($filasRaw, 'semana')));
        usort($semanas, [self::class, 'compararSemanas']);

        // Filas al detalle (flor + color + ítem) y su acumulado por flor + color,
        // derivadas ambas de la misma consulta para no duplicar el viaje a BD.
        $itemsPorClave = [];
        $ordenItems = [];
        $coloresPorClave = [];
        $ordenColores = [];

        foreach ($filasRaw as $row) {
            $flor = (string)$row['flor'];
            $color = (string)$row['color'];
            $semana = (string)$row['semana'];
            $cantidad = (float)$row['cantidad'];

            $claveItem = $flor . '|' . $color . '|' . $row['item'];
            if (!isset($itemsPorClave[$claveItem])) {
                $itemsPorClave[$claveItem] = [
                    'flor' => $flor,
                    'color' => $color,
                    'item' => (string)$row['item'],
                    'valores' => [],
                    'total' => 0.0,
                ];
                $ordenItems[] = $claveItem;
            }
            $itemsPorClave[$claveItem]['valores'][$semana] = $cantidad;
            $itemsPorClave[$claveItem]['total'] += $cantidad;

            $claveColor = $flor . '|' . $color;
            if (!isset($coloresPorClave[$claveColor])) {
                $coloresPorClave[$claveColor] = [
                    'flor' => $flor,
                    'color' => $color,
                    'valores' => [],
                    'total' => 0.0,
                ];
                $ordenColores[] = $claveColor;
            }
            $coloresPorClave[$claveColor]['valores'][$semana] = ($coloresPorClave[$claveColor]['valores'][$semana] ?? 0.0) + $cantidad;
            $coloresPorClave[$claveColor]['total'] += $cantidad;
        }

        $totalesPorSemana = array_fill_keys($semanas, 0.0);
        $totalGeneral = 0.0;
        $filas = [];
        foreach ($ordenItems as $clave) {
            $fila = $itemsPorClave[$clave];
            foreach ($semanas as $semana) {
                $totalesPorSemana[$semana] += $fila['valores'][$semana] ?? 0.0;
            }
            $totalGeneral += $fila['total'];
            $filas[] = $fila;
        }

        $resumenFlorColor = [];
        foreach ($ordenColores as $clave) {
            $resumenFlorColor[] = $coloresPorClave[$clave];
        }

        $columnaResumen = self::RESUMEN_COLUMNAS[$agruparResumen] ?? self::RESUMEN_COLUMNAS[self::RESUMEN_POR_DEFECTO];
        if (!isset(self::RESUMEN_COLUMNAS[$agruparResumen])) {
            $agruparResumen = self::RESUMEN_POR_DEFECTO;
        }

        $resumenRaw = self::fetchAll(
            $conexion,
            "SELECT
                $columnaResumen AS grupo,
                d.semana AS semana,
                ROUND(SUM(d.cantidad), 2) AS cantidad
             FROM " . self::BASE_FROM . "
             $where
             GROUP BY grupo, d.semana
             ORDER BY grupo ASC, d.semana ASC",
            $types,
            $params
        );

        $resumen = [];
        foreach ($resumenRaw as $row) {
            $grupo = trim((string)($row['grupo'] ?? ''));
            if ($grupo === '') {
                continue;
            }
            $resumen[$grupo][] = [
                'semana' => (string)$row['semana'],
                'cantidad' => (float)$row['cantidad'],
            ];
        }

        return [
            'ok' => true,
            'filters' => $filters,
            'semanas' => $semanas,
            'filas' => $filas,
            'resumenFlorColor' => $resumenFlorColor,
            'totalesPorSemana' => $totalesPorSemana,
            'totalGeneral' => round($totalGeneral, 2),
            'agrupador' => $agruparResumen,
            'gruposResumen' => array_keys($resumen),
            'resumen' => $resumen,
        ];
    }

    public static function getFilterOptions(array $rawFilters = []): array
    {
        $conexion = Database::getConnection();
        if (!$conexion) {
            return ['flores' => [], 'mercados' => [], 'submercados' => [], 'gruposDemanda' => [], 'items' => [], 'semanas' => []];
        }

        $filters = self::normalizeFilters($rawFilters);

        return [
            'flores'        => self::getDistinct($conexion, 'v.codflor', $filters, 'flor'),
            'mercados'      => self::getDistinct($conexion, 'd.mercado', $filters, 'mercado'),
            'submercados'   => self::getDistinct($conexion, 'd.submercado', $filters, 'submercado'),
            'gruposDemanda' => self::getDistinct($conexion, 'd.grupo_demanda', $filters, 'grupo_demanda'),
            'items'         => self::getDistinct($conexion, 'd.item_demanda', $filters, 'item'),
            'semanas'       => self::getSemanasDisponibles($conexion, $filters),
        ];
    }

    /**
     * Rango [desde, hasta] por defecto: desde la semana ISO actual hasta
     * SEMANAS_ADELANTE_POR_DEFECTO semanas adelante (no depende de qué
     * semanas ya tengan datos, porque tika_demanda incluye demanda futura).
     */
    public static function getRangoSemanasPorDefecto(): array
    {
        $ahora = time();
        $desde = self::semanaIso($ahora);
        $hasta = self::semanaIso(strtotime('+' . self::SEMANAS_ADELANTE_POR_DEFECTO . ' weeks', $ahora));
        return [$desde, $hasta];
    }

    /** Semana ISO de una fecha en el mismo formato usado por tika_demanda (aayy → p.ej. "2637"). */
    private static function semanaIso(int $timestamp): string
    {
        return substr(date('o', $timestamp), -2) . date('W', $timestamp);
    }

    /**
     * Combina una lista de semanas (p.ej. las disponibles en BD) con valores
     * adicionales (p.ej. los del rango por defecto) y devuelve la unión
     * ordenada, para que esos valores siempre aparezcan como opción aunque
     * aún no tengan datos.
     */
    public static function combinarSemanas(array $listaBase, array $extra): array
    {
        $extra = array_values(array_filter(array_map('strval', $extra), static function ($v) {
            return $v !== '';
        }));
        $todas = array_values(array_unique(array_merge(array_map('strval', $listaBase), $extra)));
        usort($todas, [self::class, 'compararSemanas']);
        return $todas;
    }

    private static function getSemanasDisponibles($conexion, array $filters): array
    {
        [$where, $types, $params] = self::buildWhere($filters, 'semana');

        $sql = "SELECT DISTINCT d.semana AS valor
                FROM " . self::BASE_FROM . "
                $where
                AND d.semana IS NOT NULL AND d.semana <> ''";

        $rows = self::fetchAll($conexion, $sql, $types, $params);
        $valores = array_map(static function ($row) {
            return (string)$row['valor'];
        }, $rows);

        usort($valores, [self::class, 'compararSemanas']);
        return $valores;
    }

    /**
     * Compara dos valores de semana: si ambos son numéricos (p.ej. "2537"),
     * ordena numéricamente; si no, alfabéticamente.
     */
    private static function compararSemanas(string $a, string $b): int
    {
        if (is_numeric($a) && is_numeric($b)) {
            return $a <=> $b;
        }
        return strcmp($a, $b);
    }

    private static function getDistinct($conexion, string $columnExpr, array $filters, string $excludeKey): array
    {
        [$where, $types, $params] = self::buildWhere($filters, $excludeKey);

        $sql = "SELECT DISTINCT $columnExpr AS valor
                FROM " . self::BASE_FROM . "
                $where
                AND $columnExpr IS NOT NULL AND $columnExpr <> ''
                ORDER BY $columnExpr ASC";

        $rows = self::fetchAll($conexion, $sql, $types, $params);
        return array_map(static function ($row) {
            return $row['valor'];
        }, $rows);
    }

    private static function normalizeFilters(array $raw): array
    {
        return [
            'flor'          => self::cleanArray($raw['flor'] ?? []),
            'mercado'       => self::cleanArray($raw['mercado'] ?? []),
            'submercado'    => self::cleanArray($raw['submercado'] ?? []),
            'grupo_demanda' => self::cleanArray($raw['grupo_demanda'] ?? []),
            'item'          => self::cleanArray($raw['item'] ?? []),
            'semana_desde'  => trim((string)($raw['semana_desde'] ?? '')),
            'semana_hasta'  => trim((string)($raw['semana_hasta'] ?? '')),
        ];
    }

    private static function cleanArray($value): array
    {
        $values = is_array($value) ? $value : ($value === '' || $value === null ? [] : [$value]);
        $limpio = [];
        foreach ($values as $v) {
            $v = trim((string)$v);
            if ($v !== '') {
                $limpio[] = $v;
            }
        }
        return array_values(array_unique($limpio));
    }

    /**
     * @return array{0:string,1:string,2:array} [sql WHERE, tipos bind_param, params]
     */
    private static function buildWhere(array $filters, ?string $excludeKey = null): array
    {
        $conditions = ['1 = 1'];
        $types = '';
        $params = [];

        foreach (self::FILTER_COLUMNS as $key => $column) {
            if ($key === $excludeKey || empty($filters[$key])) {
                continue;
            }
            $placeholders = implode(',', array_fill(0, count($filters[$key]), '?'));
            $conditions[] = "$column IN ($placeholders)";
            foreach ($filters[$key] as $valor) {
                $types .= 's';
                $params[] = $valor;
            }
        }

        if ($excludeKey !== 'semana') {
            if ($filters['semana_desde'] !== '') {
                $conditions[] = 'd.semana >= ?';
                $types .= 's';
                $params[] = $filters['semana_desde'];
            }
            if ($filters['semana_hasta'] !== '') {
                $conditions[] = 'd.semana <= ?';
                $types .= 's';
                $params[] = $filters['semana_hasta'];
            }
        }

        return ['WHERE ' . implode(' AND ', $conditions), $types, $params];
    }

    private static function fetchAll($conexion, string $sql, string $types, array $params): array
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

    private static function errorResponse(string $message): array
    {
        return [
            'ok' => false,
            'message' => $message,
            'semanas' => [],
            'filas' => [],
            'resumenFlorColor' => [],
            'totalesPorSemana' => [],
            'totalGeneral' => 0,
            'agrupador' => self::RESUMEN_POR_DEFECTO,
            'gruposResumen' => [],
            'resumen' => [],
        ];
    }
}
