<?php
declare(strict_types=1);
namespace App\Models;

use App\Helpers\Database;

/**
 * Comparación de escenarios de producción (tika_compara_escenarios) cruzada con
 * `dates` (semana/año) y con variedad -> color (ld_variedades / ld_colores).
 *
 * Devuelve filas ya agregadas (tallos) para que el frontend haga el resto de la
 * interacción (filtros en cascada, agrupaciones, gráficos y comparaciones) sin
 * volver al servidor.
 */
class ComparaEscenarios
{
    public const SEMANAS_ATRAS_POR_DEFECTO = 4;
    public const SEMANAS_ADELANTE_POR_DEFECTO = 52;

    /**
     * `dates` y `ld_colores` se pre-agregan a 1 fila por llave para que un catálogo
     * con duplicados no multiplique los tallos (fan-out de los LEFT JOIN).
     * El valor de color mostrado ES ld_colores.orden (regla fija del proyecto).
     */
    private const BASE_FROM = "tika_compara_escenarios ce
        LEFT JOIN (
            SELECT fecha, MIN(`año`) AS anio, MIN(aass) AS aass
            FROM dates
            GROUP BY fecha
        ) d ON d.fecha = ce.fecha
        LEFT JOIN (
            SELECT UPPER(TRIM(lv.nombre)) AS variedad_norm, MIN(lc.orden) AS color
            FROM ld_variedades lv
            LEFT JOIN ld_colores lc ON lc.codigo = lv.codgcol
            GROUP BY UPPER(TRIM(lv.nombre))
        ) c ON c.variedad_norm = UPPER(TRIM(REPLACE(ce.variedad, '_', ' ')))";

    /**
     * @param array $raw  semana_desde, semana_hasta (yyww) y granularidad (semana|dia)
     * @return array{ok:bool,rows:array,granularidad:string,message?:string}
     */
    public static function getRows(array $raw = []): array
    {
        $conexion = Database::getConnection();
        if (!$conexion) {
            return ['ok' => false, 'message' => 'No hay conexion activa a la base de datos.', 'rows' => []];
        }

        $granularidad = ($raw['granularidad'] ?? 'semana') === 'dia' ? 'dia' : 'semana';
        $desde = trim((string)($raw['semana_desde'] ?? ''));
        $hasta = trim((string)($raw['semana_hasta'] ?? ''));

        $conditions = ['1 = 1'];
        $types = '';
        $params = [];
        if ($desde !== '') {
            $conditions[] = 'd.aass >= ?';
            $types .= 's';
            $params[] = $desde;
        }
        if ($hasta !== '') {
            $conditions[] = 'd.aass <= ?';
            $types .= 's';
            $params[] = $hasta;
        }
        $where = 'WHERE ' . implode(' AND ', $conditions);

        $fechaSelect = $granularidad === 'dia' ? 'ce.fecha AS f,' : '';
        $fechaGroup = $granularidad === 'dia' ? 'ce.fecha,' : '';

        $sql = "SELECT
                    $fechaSelect
                    d.aass AS s,
                    d.anio AS a,
                    ce.variedad AS v,
                    ce.fuente AS u,
                    ce.tipo AS t,
                    ce.flor AS l,
                    c.color AS c,
                    SUM(ce.tallos) AS n
                FROM " . self::BASE_FROM . "
                $where
                GROUP BY $fechaGroup d.aass, d.anio, ce.variedad, ce.fuente, ce.tipo, ce.flor, c.color
                ORDER BY d.aass ASC";

        $rows = self::fetchAll($conexion, $sql, $types, $params);

        foreach ($rows as &$row) {
            $row['n'] = (float)$row['n'];
            $row['s'] = $row['s'] !== null ? (string)$row['s'] : 'N/D';
            $row['a'] = $row['a'] !== null ? (string)$row['a'] : '';
            $row['c'] = $row['c'] !== null ? (string)$row['c'] : 'Sin color';
            $row['v'] = (string)($row['v'] ?? '');
            $row['u'] = (string)($row['u'] ?? '');
            $row['t'] = (string)($row['t'] ?? '');
            $row['l'] = (string)($row['l'] ?? '');
        }
        unset($row);

        return ['ok' => true, 'granularidad' => $granularidad, 'rows' => $rows];
    }

    /** Todas las semanas (yyww) con datos, ordenadas, para poblar los selects de rango. */
    public static function getSemanasDisponibles(): array
    {
        $conexion = Database::getConnection();
        if (!$conexion) {
            return [];
        }
        $rows = self::fetchAll(
            $conexion,
            "SELECT DISTINCT d.aass AS valor FROM " . self::BASE_FROM . " WHERE d.aass IS NOT NULL ORDER BY d.aass ASC",
            '',
            []
        );
        return array_map(static function ($r) {
            return (string)$r['valor'];
        }, $rows);
    }

    public static function semanaIso(int $timestamp): string
    {
        return substr(date('o', $timestamp), -2) . date('W', $timestamp);
    }

    /** @return array{0:string,1:string} semana desde / hasta por defecto */
    public static function getRangoPorDefecto(): array
    {
        $ahora = time();
        return [
            self::semanaIso(strtotime('-' . self::SEMANAS_ATRAS_POR_DEFECTO . ' weeks', $ahora)),
            self::semanaIso(strtotime('+' . self::SEMANAS_ADELANTE_POR_DEFECTO . ' weeks', $ahora)),
        ];
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
}
