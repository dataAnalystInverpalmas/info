<?php
namespace App\Models;

use App\Helpers\Database;

class ReporteSiembraComparativa {

    private static function construirWhere(array $filtros, ?string $excluirClave = null): array {
        $where = ["v.tipo IN ('TEO_SIEMBRA', 'SIEMBRA')"];
        $params = [];
        $types = '';

        $columnasFiltro = [
            'finca' => 'v.finca',
            'bloque' => 'v.bloque',
            'variedad' => 'v.variedad',
            'temporada' => 's.cod_temporada',
            'flor' => 'v.producto',
            'tipo_siembra' => 'v.tipo_siembra',
            'programa' => 'v.año',
            'color' => 'vc.color',
        ];

        foreach ($columnasFiltro as $clave => $columna) {
            if ($clave === $excluirClave) {
                continue;
            }
            if (!empty($filtros[$clave])) {
                $valores = (array)$filtros[$clave];
                $placeholders = implode(',', array_fill(0, count($valores), '?'));
                $where[] = "$columna IN ($placeholders)";
                foreach ($valores as $valor) {
                    $params[] = $valor;
                    $types .= 's';
                }
            }
        }

        if (!empty($filtros['desde'])) {
            $where[] = 'v.fecha >= ?';
            $params[] = $filtros['desde'];
            $types .= 's';
        }
        if (!empty($filtros['hasta'])) {
            $where[] = 'v.fecha <= ?';
            $params[] = $filtros['hasta'];
            $types .= 's';
        }

        return [implode(' AND ', $where), $params, $types];
    }

    /**
     * Compara camas reales (tipo=SIEMBRA) vs teóricas (tipo=TEO_SIEMBRA) por
     * finca+bloque+producto+variedad+temporada+programa+semana.
     * `dates` se pre-agrega a 1 fila por fecha para evitar fan-out si hubiera
     * fechas duplicadas en el catálogo.
     */
    public static function obtenerDatos(array $filtros = []): array {
        $conexion = Database::getConnection();
        [$where, $params, $types] = self::construirWhere($filtros);

        // Color: se cruza por nombre de variedad (ld_variedades -> ld_colores), pre-agregado
        // a 1 fila por variedad para no repetir el fan-out de LEFT JOIN si hay duplicados.
        // El valor de color mostrado ES ld_colores.orden (no el nombre) — regla fija del proyecto.
        $sql = "
            SELECT
                fd.semana_yyww,
                fdp.semana_yyww AS semana_pico_yyww,
                v.finca,
                v.bloque,
                v.producto AS flor,
                v.tipo_siembra,
                s.cod_temporada AS temporada,
                v.variedad,
                v.año AS programa,
                v.fecha_pico,
                COALESCE(vc.color, 'Sin color') AS color,
                SUM(CASE WHEN v.tipo = 'SIEMBRA' THEN ROUND(v.valor / 960) ELSE 0 END) AS camas_real,
                SUM(CASE WHEN v.tipo = 'TEO_SIEMBRA' THEN ROUND(v.valor / 960) ELSE 0 END) AS camas_teorica
            FROM viewsowing v
            INNER JOIN seasons s ON s.nombre = v.temporada
            INNER JOIN (
                SELECT fecha, MIN(aass) AS semana_yyww
                FROM dates
                GROUP BY fecha
            ) fd ON fd.fecha = v.fecha
            LEFT JOIN (
                SELECT fecha, MIN(aass) AS semana_yyww
                FROM dates
                GROUP BY fecha
            ) fdp ON fdp.fecha = v.fecha_pico
            LEFT JOIN (
                SELECT
                    UPPER(TRIM(lv.nombre)) AS variedad_norm,
                    MIN(lc.orden) AS color
                FROM ld_variedades lv
                LEFT JOIN ld_colores lc ON lc.codigo = lv.codgcol
                GROUP BY UPPER(TRIM(lv.nombre))
            ) vc ON vc.variedad_norm = UPPER(TRIM(v.variedad))
            WHERE $where
            GROUP BY fd.semana_yyww, fdp.semana_yyww, v.finca, v.bloque, v.producto, v.tipo_siembra,
                     s.cod_temporada, v.variedad, v.año, v.fecha_pico, color
            ORDER BY fd.semana_yyww DESC, v.finca, v.bloque
        ";

        $stmt = $conexion->prepare($sql);
        if ($stmt === false) {
            return [];
        }
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $row['camas_real'] = (int)round((float)$row['camas_real']);
            $row['camas_teorica'] = (int)round((float)$row['camas_teorica']);
            $row['diferencia'] = $row['camas_real'] - $row['camas_teorica'];
            $row['cumplimiento_pct'] = $row['camas_teorica'] > 0
                ? round(($row['camas_real'] / $row['camas_teorica']) * 100, 1)
                : null;
            $data[] = $row;
        }
        $stmt->close();

        return $data;
    }

    /**
     * Opciones de cada filtro, en cascada: cada dimensión se calcula aplicando todos
     * los filtros activos EXCEPTO el suyo propio (ver misma lógica en ReportePlane).
     */
    public static function opcionesFiltro(array $filtros = []): array {
        $conexion = Database::getConnection();
        $opciones = [
            'fincas' => [], 'bloques' => [], 'variedades' => [], 'temporadas' => [],
            'flores' => [], 'tipos_siembra' => [], 'programas' => [], 'colores' => [],
        ];

        $columnasSimples = [
            'fincas' => ['clave' => 'finca', 'columna' => 'v.finca'],
            'bloques' => ['clave' => 'bloque', 'columna' => 'v.bloque'],
            'variedades' => ['clave' => 'variedad', 'columna' => 'v.variedad'],
            'temporadas' => ['clave' => 'temporada', 'columna' => 's.cod_temporada'],
            'flores' => ['clave' => 'flor', 'columna' => 'v.producto'],
            'tipos_siembra' => ['clave' => 'tipo_siembra', 'columna' => 'v.tipo_siembra'],
            'programas' => ['clave' => 'programa', 'columna' => 'v.año'],
        ];

        // El valor de color ES ld_colores.orden (regla fija del proyecto, no el nombre).
        $joinColor = "
            LEFT JOIN (
                SELECT
                    UPPER(TRIM(lv.nombre)) AS variedad_norm,
                    MIN(lc.orden) AS color
                FROM ld_variedades lv
                LEFT JOIN ld_colores lc ON lc.codigo = lv.codgcol
                GROUP BY UPPER(TRIM(lv.nombre))
            ) vc ON vc.variedad_norm = UPPER(TRIM(v.variedad))
        ";
        $fromJoin = "
            FROM viewsowing v
            INNER JOIN seasons s ON s.nombre = v.temporada
            $joinColor
        ";

        foreach ($columnasSimples as $clave => $info) {
            [$where, $params, $types] = self::construirWhere($filtros, $info['clave']);
            $sql = "
                SELECT DISTINCT {$info['columna']}
                $fromJoin
                WHERE $where AND {$info['columna']} IS NOT NULL AND {$info['columna']} != ''
                ORDER BY {$info['columna']}
            ";
            $stmt = $conexion->prepare($sql);
            if ($stmt === false) {
                continue;
            }
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_row()) {
                // Forzar string: mysqli (mysqlnd) devuelve tipos nativos (p. ej. programa/año
                // como int) y el JS del frontend compara los valores seleccionados (siempre
                // strings) contra estas opciones con igualdad estricta.
                $opciones[$clave][] = (string)$row[0];
            }
            $stmt->close();
        }

        [$where, $params, $types] = self::construirWhere($filtros, 'color');
        $sql = "
            SELECT DISTINCT vc.color
            $fromJoin
            WHERE $where AND vc.color IS NOT NULL
            ORDER BY vc.color
        ";
        $stmt = $conexion->prepare($sql);
        if ($stmt !== false) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_row()) {
                $opciones['colores'][] = (string)$row[0];
            }
            $stmt->close();
        }

        return $opciones;
    }
}
