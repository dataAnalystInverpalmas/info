<?php
declare(strict_types=1);
namespace App\Models;

use App\Helpers\Database;

class SeguimientoProduccion
{
    private const TABLE = 'ld_proyecciones';

    /** Orden preferido para mostrar/leyenda de tipos cuando estén presentes. */
    private const TIPO_ORDEN = ['RE', 'AJ', 'IN'];

    /** Tipos seleccionados por defecto al cargar el reporte. */
    public const TIPOS_POR_DEFECTO = ['RE', 'AJ'];

    /**
     * Filtros soportados y su columna real en la tabla.
     * 'ciclo' es derivado (IF(edad<=35,1,2)), no existe como columna física.
     */
    private const FILTER_COLUMNS = [
        'finca'    => 'finca',
        'bloque'   => 'bloque',
        'flor'     => 'flor',
        'variedad' => 'variedad',
        'cosecha'  => 'cosecha',
        'tipo'     => 'tipo',
    ];

    public static function getTrendData(array $rawFilters = []): array
    {
        $conexion = Database::getConnection();
        if (!$conexion) {
            return self::errorResponse('No hay conexion activa a la base de datos.');
        }

        $filters = self::normalizeFilters($rawFilters);
        [$where, $types, $params] = self::buildWhere($filters);

        $porEdadRows = self::fetchAll(
            $conexion,
            "SELECT
                tipo,
                edad,
                ROUND(AVG(tallos / NULLIF(matas, 0)), 2) AS avg_f_m,
                ROUND(SUM(tallos), 0) AS total_corte,
                SUM(matas) AS total_matas,
                COUNT(*) AS registros
             FROM " . self::TABLE . "
             $where
             GROUP BY tipo, edad
             ORDER BY tipo ASC, edad ASC",
            $types,
            $params
        );

        $porAassRows = self::fetchAll(
            $conexion,
            "SELECT
                tipo,
                RIGHT(DATE_FORMAT(fecha, '%x%v'), 4) AS aass,
                MIN(fecha) AS fecha_ref,
                ROUND(AVG(tallos / NULLIF(matas, 0)), 2) AS avg_f_m,
                ROUND(SUM(tallos), 0) AS total_corte,
                SUM(matas) AS total_matas,
                COUNT(*) AS registros
             FROM " . self::TABLE . "
             $where
             GROUP BY tipo, aass
             ORDER BY tipo ASC, MIN(fecha) ASC",
            $types,
            $params
        );

        $tiposPresentes = self::ordenarTipos(array_unique(array_merge(
            array_column($porEdadRows, 'tipo'),
            array_column($porAassRows, 'tipo')
        )));

        return [
            'ok' => true,
            'filters' => $filters,
            'tipos' => $tiposPresentes,
            'porEdad' => self::agruparPorTipo(self::normalizeEdadRows($porEdadRows)),
            'porAass' => self::agruparPorTipo(self::normalizeAassRows($porAassRows)),
            'acumuladoCiclo' => self::acumuladoPorCiclo($porEdadRows),
        ];
    }

    /**
     * Ordena una lista de tipos siguiendo TIPO_ORDEN y agrega al final
     * cualquier código no contemplado, en orden alfabético.
     */
    private static function ordenarTipos(array $tipos): array
    {
        $tipos = array_values(array_filter(array_map('strval', $tipos), static function ($t) {
            return $t !== '';
        }));

        $conocidos = array_values(array_intersect(self::TIPO_ORDEN, $tipos));
        $desconocidos = array_values(array_diff($tipos, self::TIPO_ORDEN));
        sort($desconocidos);

        return array_merge($conocidos, $desconocidos);
    }

    /**
     * Agrupa una lista plana de filas (ya normalizadas, con clave 'tipo') en
     * un mapa tipo => filas, preservando el orden de aparición.
     */
    private static function agruparPorTipo(array $rows): array
    {
        $grouped = [];
        foreach ($rows as $row) {
            $tipo = $row['tipo'];
            unset($row['tipo']);
            $grouped[$tipo][] = $row;
        }
        return $grouped;
    }

    /**
     * Acumulado por ciclo y tipo, derivado de las filas crudas agrupadas por
     * tipo+edad:
     * - f_m: suma de los promedios por edad (acumulado del promedio)
     * - corte: suma de tallos
     * - matas: promedio de matas por edad
     * - registros: suma de registros
     */
    private static function acumuladoPorCiclo(array $porEdadRows): array
    {
        $acumulado = [];

        foreach ($porEdadRows as $row) {
            $tipo = (string)($row['tipo'] ?? '');
            $edad = (int)($row['edad'] ?? 0);
            $ciclo = $edad <= 35 ? 1 : 2;

            if (!isset($acumulado[$tipo][$ciclo])) {
                $acumulado[$tipo][$ciclo] = [
                    'ciclo' => $ciclo,
                    'acumulado_f_m' => 0.0,
                    'total_corte' => 0,
                    'promedio_matas' => 0.0,
                    'total_registros' => 0,
                    'edades' => 0,
                ];
            }

            $acumulado[$tipo][$ciclo]['acumulado_f_m'] += (float)($row['avg_f_m'] ?? 0);
            $acumulado[$tipo][$ciclo]['total_corte'] += (int)($row['total_corte'] ?? 0);
            $acumulado[$tipo][$ciclo]['promedio_matas'] += (float)($row['total_matas'] ?? 0);
            $acumulado[$tipo][$ciclo]['total_registros'] += (int)($row['registros'] ?? 0);
            $acumulado[$tipo][$ciclo]['edades'] += 1;
        }

        $resultado = [];
        foreach ($acumulado as $tipo => $ciclos) {
            foreach ($ciclos as $ciclo => $datos) {
                if ($datos['edades'] === 0) {
                    continue;
                }
                $resultado[$tipo][] = [
                    'ciclo'           => $ciclo,
                    'acumulado_f_m'   => round($datos['acumulado_f_m'], 2),
                    'total_corte'     => $datos['total_corte'],
                    'promedio_matas'  => round($datos['promedio_matas'] / $datos['edades'], 2),
                    'total_registros' => $datos['total_registros'],
                ];
            }
        }

        return $resultado;
    }

    public static function getFilterOptions(array $rawFilters = []): array
    {
        $conexion = Database::getConnection();
        if (!$conexion) {
            return ['fincas' => [], 'bloques' => [], 'flores' => [], 'variedades' => [], 'cosechas' => [], 'tipos' => []];
        }

        $filters = self::normalizeFilters($rawFilters);

        return [
            'fincas'     => self::getDistinct($conexion, 'finca', $filters, 'finca'),
            'bloques'    => self::getDistinct($conexion, 'bloque', $filters, 'bloque'),
            'flores'     => self::getDistinct($conexion, 'flor', $filters, 'flor'),
            'variedades' => self::getDistinct($conexion, 'variedad', $filters, 'variedad'),
            'cosechas'   => self::getDistinct($conexion, 'cosecha', $filters, 'cosecha'),
            'tipos'      => self::ordenarTipos(self::getDistinct($conexion, 'tipo', $filters, 'tipo')),
        ];
    }

    /**
     * Lista de valores distintos para una columna, aplicando todos los filtros
     * activos EXCEPTO el de esa misma dimension (para que el propio select no
     * se restrinja a si mismo, igual que en CurvasClavelProduccion).
     */
    private static function getDistinct($conexion, string $column, array $filters, string $excludeKey): array
    {
        [$where, $types, $params] = self::buildWhere($filters, $excludeKey);

        $sql = "SELECT DISTINCT `$column` AS valor
                FROM " . self::TABLE . "
                $where
                AND `$column` IS NOT NULL AND `$column` <> ''
                ORDER BY `$column` ASC";

        $rows = self::fetchAll($conexion, $sql, $types, $params);
        return array_map(static function ($row) {
            return $row['valor'];
        }, $rows);
    }

    /**
     * Todos los filtros son multi-selección (arrays), igual que en
     * CurvasClavelProduccion. 'tipo' incluye por defecto RE y AJ desde el
     * frontend; aquí, si no llega nada, no se restringe (se listan todos).
     * 'fecha_desde'/'fecha_hasta' acotan por fecha real (no por el yyww
     * derivado, que al ser una cadena no es fiable para comparar rangos que
     * cruzan fin de año).
     */
    private static function normalizeFilters(array $raw): array
    {
        $ciclo = self::cleanArray($raw['ciclo'] ?? []);
        $ciclo = array_values(array_intersect($ciclo, ['1', '2']));

        return [
            'finca'       => self::cleanArray($raw['finca'] ?? []),
            'bloque'      => self::cleanArray($raw['bloque'] ?? []),
            'flor'        => self::cleanArray($raw['flor'] ?? []),
            'variedad'    => self::cleanArray($raw['variedad'] ?? []),
            'cosecha'     => self::cleanArray($raw['cosecha'] ?? []),
            'tipo'        => self::cleanArray($raw['tipo'] ?? []),
            'ciclo'       => $ciclo,
            'fecha_desde' => self::cleanFecha($raw['fecha_desde'] ?? ''),
            'fecha_hasta' => self::cleanFecha($raw['fecha_hasta'] ?? ''),
        ];
    }

    private static function cleanFecha($value): string
    {
        $value = trim((string)$value);
        if ($value === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return '';
        }
        return $value;
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
            $conditions[] = "`$column` IN ($placeholders)";
            foreach ($filters[$key] as $valor) {
                $types .= 's';
                $params[] = $valor;
            }
        }

        if ($excludeKey !== 'ciclo' && !empty($filters['ciclo'])) {
            $placeholders = implode(',', array_fill(0, count($filters['ciclo']), '?'));
            $conditions[] = "IF(edad <= 35, 1, 2) IN ($placeholders)";
            foreach ($filters['ciclo'] as $valor) {
                $types .= 'i';
                $params[] = (int)$valor;
            }
        }

        if (!empty($filters['fecha_desde'])) {
            $conditions[] = 'fecha >= ?';
            $types .= 's';
            $params[] = $filters['fecha_desde'];
        }

        if (!empty($filters['fecha_hasta'])) {
            $conditions[] = 'fecha <= ?';
            $types .= 's';
            $params[] = $filters['fecha_hasta'];
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

    private static function normalizeEdadRows(array $rows): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $normalized[] = [
                'tipo'        => (string)($row['tipo'] ?? ''),
                'edad'        => (int)($row['edad'] ?? 0),
                'avg_f_m'     => (float)($row['avg_f_m'] ?? 0),
                'total_corte' => (int)($row['total_corte'] ?? 0),
                'matas'       => (int)($row['total_matas'] ?? 0),
                'registros'   => (int)($row['registros'] ?? 0),
            ];
        }
        return $normalized;
    }

    private static function normalizeAassRows(array $rows): array
    {
        $normalized = [];
        foreach ($rows as $row) {
            $normalized[] = [
                'tipo'        => (string)($row['tipo'] ?? ''),
                'aass'        => (string)($row['aass'] ?? ''),
                'avg_f_m'     => (float)($row['avg_f_m'] ?? 0),
                'total_corte' => (int)($row['total_corte'] ?? 0),
                'matas'       => (int)($row['total_matas'] ?? 0),
                'registros'   => (int)($row['registros'] ?? 0),
            ];
        }
        return $normalized;
    }

    private static function errorResponse(string $message): array
    {
        return [
            'ok' => false,
            'message' => $message,
            'tipos' => [],
            'porEdad' => [],
            'porAass' => [],
            'acumuladoCiclo' => [],
        ];
    }
}
