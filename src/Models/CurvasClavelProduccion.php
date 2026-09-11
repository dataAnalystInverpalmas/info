<?php
declare(strict_types=1);
namespace App\Models;

use App\Helpers\Database;

class CurvasClavelProduccion
{
    private const TABLE = 'ld_curvas_clavel';

    /**
     * Filtros soportados y su columna real en la tabla.
     * 'ciclo' es derivado (IF(edad<=35,1,2)), no existe como columna física.
     */
    private const FILTER_COLUMNS = [
        'finca'    => 'finca',
        'bloque'   => 'bloque',
        'flor'     => 'cflor',
        'variedad' => 'nvariedad',
        'cosecha'  => 'cosecha',
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
                edad,
                ROUND(AVG(f_m), 2) AS avg_f_m,
                ROUND(SUM(corte), 0) AS total_corte,
                SUM(matas) AS total_matas,
                COUNT(*) AS registros
             FROM " . self::TABLE . "
             $where
             GROUP BY edad
             ORDER BY edad ASC",
            $types,
            $params
        );

        $porAassRows = self::fetchAll(
            $conexion,
            "SELECT
                DATE_FORMAT(lunes, '%y%v') AS aass,
                MIN(lunes) AS lunes_ref,
                ROUND(AVG(f_m), 2) AS avg_f_m,
                ROUND(SUM(corte), 0) AS total_corte,
                SUM(matas) AS total_matas,
                COUNT(*) AS registros
             FROM " . self::TABLE . "
             $where
             GROUP BY aass
             ORDER BY MIN(lunes) ASC",
            $types,
            $params
        );

        return [
            'ok' => true,
            'filters' => $filters,
            'porEdad' => self::normalizeEdadRows($porEdadRows),
            'porAass' => self::normalizeAassRows($porAassRows),
            'acumuladoCiclo' => self::acumuladoPorCiclo($porEdadRows),
        ];
    }

    /**
     * Acumulado por ciclo, derivado de las filas ya agrupadas por edad:
     * - f_m: suma de los promedios por edad (acumulado del promedio)
     * - corte: suma de tallos
     * - matas: promedio de matas por edad
     * - registros: suma de registros
     */
    private static function acumuladoPorCiclo(array $porEdadRows): array
    {
        $acumulado = [
            1 => ['ciclo' => 1, 'acumulado_f_m' => 0.0, 'total_corte' => 0, 'promedio_matas' => 0.0, 'total_registros' => 0, 'edades' => 0],
            2 => ['ciclo' => 2, 'acumulado_f_m' => 0.0, 'total_corte' => 0, 'promedio_matas' => 0.0, 'total_registros' => 0, 'edades' => 0],
        ];

        foreach ($porEdadRows as $row) {
            $edad = (int)($row['edad'] ?? 0);
            $ciclo = $edad <= 35 ? 1 : 2;

            $acumulado[$ciclo]['acumulado_f_m'] += (float)($row['avg_f_m'] ?? 0);
            $acumulado[$ciclo]['total_corte'] += (int)($row['total_corte'] ?? 0);
            $acumulado[$ciclo]['promedio_matas'] += (float)($row['total_matas'] ?? 0);
            $acumulado[$ciclo]['total_registros'] += (int)($row['registros'] ?? 0);
            $acumulado[$ciclo]['edades'] += 1;
        }

        $resultado = [];
        foreach ($acumulado as $ciclo => $datos) {
            if ($datos['edades'] === 0) {
                continue;
            }
            $resultado[] = [
                'ciclo'           => $ciclo,
                'acumulado_f_m'   => round($datos['acumulado_f_m'], 2),
                'total_corte'     => $datos['total_corte'],
                'promedio_matas'  => round($datos['promedio_matas'] / $datos['edades'], 2),
                'total_registros' => $datos['total_registros'],
            ];
        }

        return $resultado;
    }

    public static function getFilterOptions(array $rawFilters = []): array
    {
        $conexion = Database::getConnection();
        if (!$conexion) {
            return ['fincas' => [], 'bloques' => [], 'flores' => [], 'variedades' => [], 'cosechas' => []];
        }

        $filters = self::normalizeFilters($rawFilters);

        return [
            'fincas'    => self::getDistinct($conexion, 'finca', $filters, 'finca'),
            'bloques'   => self::getDistinct($conexion, 'bloque', $filters, 'bloque'),
            'flores'    => self::getDistinct($conexion, 'cflor', $filters, 'flor'),
            'variedades' => self::getDistinct($conexion, 'nvariedad', $filters, 'variedad'),
            'cosechas'  => self::getDistinct($conexion, 'cosecha', $filters, 'cosecha'),
        ];
    }

    /**
     * Lista de valores distintos para una columna, aplicando todos los filtros
     * activos EXCEPTO el de esa misma dimension (para que el propio select no
     * se restrinja a si mismo, igual que en PlanoConsulta).
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
     * ReporteComparativo: el usuario puede marcar varios valores por dimensión.
     */
    private static function normalizeFilters(array $raw): array
    {
        $ciclo = self::cleanArray($raw['ciclo'] ?? []);
        $ciclo = array_values(array_intersect($ciclo, ['1', '2']));

        return [
            'finca'    => self::cleanArray($raw['finca'] ?? []),
            'bloque'   => self::cleanArray($raw['bloque'] ?? []),
            'flor'     => self::cleanArray($raw['flor'] ?? []),
            'variedad' => self::cleanArray($raw['variedad'] ?? []),
            'cosecha'  => self::cleanArray($raw['cosecha'] ?? []),
            'ciclo'    => $ciclo,
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
            'porEdad' => [],
            'porAass' => [],
            'acumuladoCiclo' => [],
        ];
    }
}
