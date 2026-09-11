<?php
namespace App\Models;

use App\Helpers\Database;

class ReportePlane {

    private static function construirWhere(array $filtros, ?string $excluirClave = null, bool $aplicarPlantasPositivas = true): array {
        $where = $aplicarPlantasPositivas ? ['plantas > 0'] : ['1 = 1'];
        $params = [];
        $types = '';

        $columnasFiltro = [
            'finca' => 'p.finca',
            'bloque' => 'p.bloque',
            'variedad' => 'p.variedad',
            'temporada' => 'p.temporada',
            'flor' => 'p.producto',
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
            $where[] = 'p.fecha_siembra >= ?';
            $params[] = $filtros['desde'];
            $types .= 's';
        }
        if (!empty($filtros['hasta'])) {
            $where[] = 'p.fecha_siembra <= ?';
            $params[] = $filtros['hasta'];
            $types .= 's';
        }

        return [implode(' AND ', $where), $params, $types];
    }

    /**
     * Detalle agregado por siembra (finca+bloque+variedad+temporada+fecha_siembra).
     * `camas` = número de registros de plane agrupados; `plantas` = suma de plantas.
     */
    public static function obtenerDatos(array $filtros = []): array {
        $conexion = Database::getConnection();
        [$where, $params, $types] = self::construirWhere($filtros);

        // Los catálogos (ld_variedades/ld_colores/varieties/dates) se pre-agregan a 1 fila
        // por llave antes de unirlos a plane: si hubiera nombres duplicados en el catálogo,
        // un LEFT JOIN directo multiplicaría (fan-out) cada fila de plane y las camas saldrían
        // infladas o descuadradas al combinar varias dimensiones en el resumen.
        $sql = "
            SELECT
                p.producto AS flor,
                p.variedad,
                p.finca,
                p.bloque,
                p.temporada,
                p.tipo_siembra,
                vt.ciclo,
                COUNT(*) AS camas,
                SUM(p.plantas) AS plantas,
                p.fecha_siembra,
                fd.semana_yyww,
                COALESCE(vc.color, 'Sin color') AS color
            FROM plane p
            LEFT JOIN (
                SELECT
                    UPPER(TRIM(lv.nombre)) AS variedad_norm,
                    MIN(lc.orden) AS color
                FROM ld_variedades lv
                LEFT JOIN ld_colores lc ON lc.codigo = lv.codgcol
                GROUP BY UPPER(TRIM(lv.nombre))
            ) vc ON vc.variedad_norm = UPPER(TRIM(p.variedad))
            LEFT JOIN (
                SELECT
                    UPPER(TRIM(nombre)) AS variedad_norm,
                    UPPER(TRIM(producto)) AS producto_norm,
                    MIN(ciclo) AS ciclo
                FROM varieties
                GROUP BY UPPER(TRIM(nombre)), UPPER(TRIM(producto))
            ) vt ON vt.variedad_norm = UPPER(TRIM(p.variedad)) AND vt.producto_norm = UPPER(TRIM(p.producto))
            LEFT JOIN (
                SELECT fecha, MIN(aass) AS semana_yyww
                FROM dates
                GROUP BY fecha
            ) fd ON fd.fecha = p.fecha_siembra
            WHERE $where
            GROUP BY p.producto, p.variedad, p.finca, p.bloque, p.temporada, p.tipo_siembra,
                     vt.ciclo, p.fecha_siembra, fd.semana_yyww, color
            ORDER BY p.fecha_siembra DESC, p.finca, p.bloque
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
            $data[] = $row;
        }
        $stmt->close();

        return $data;
    }

    /**
     * Opciones de cada filtro, en cascada: cada dimensión se calcula aplicando todos
     * los filtros activos EXCEPTO el suyo propio, para que al elegir p. ej. una finca
     * los demás selects (bloque, variedad, ...) muestren solo valores compatibles con
     * esa finca, sin restringir su propia lista por su propia selección.
     */
    public static function opcionesFiltro(array $filtros = []): array {
        $conexion = Database::getConnection();
        $opciones = ['fincas' => [], 'bloques' => [], 'variedades' => [], 'temporadas' => [], 'flores' => [], 'colores' => []];

        $columnasSimples = [
            'fincas' => ['clave' => 'finca', 'columna' => 'p.finca'],
            'bloques' => ['clave' => 'bloque', 'columna' => 'p.bloque'],
            'variedades' => ['clave' => 'variedad', 'columna' => 'p.variedad'],
            'temporadas' => ['clave' => 'temporada', 'columna' => 'p.temporada'],
            'flores' => ['clave' => 'flor', 'columna' => 'p.producto'],
        ];

        $joinColor = "
            LEFT JOIN (
                SELECT
                    UPPER(TRIM(lv.nombre)) AS variedad_norm,
                    MIN(lc.orden) AS color
                FROM ld_variedades lv
                LEFT JOIN ld_colores lc ON lc.codigo = lv.codgcol
                GROUP BY UPPER(TRIM(lv.nombre))
            ) vc ON vc.variedad_norm = UPPER(TRIM(p.variedad))
        ";

        foreach ($columnasSimples as $clave => $info) {
            [$where, $params, $types] = self::construirWhere($filtros, $info['clave'], false);
            $sql = "
                SELECT DISTINCT {$info['columna']}
                FROM plane p
                $joinColor
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
                // Forzar string: mysqli (mysqlnd) devuelve tipos nativos (p. ej. bloque
                // como int) y el JS del frontend compara los valores seleccionados
                // (siempre strings) contra estas opciones con igualdad estricta.
                $opciones[$clave][] = (string)$row[0];
            }
            $stmt->close();
        }

        [$where, $params, $types] = self::construirWhere($filtros, 'color', false);
        $sql = "
            SELECT DISTINCT vc.color
            FROM plane p
            $joinColor
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

    /**
     * Fecha de siembra más antigua para CLAVEL/MINICLAVEL (ciclo largo, se mantienen
     * sembrados mucho tiempo): usada como valor por defecto del filtro general
     * "Siembra desde", para que el histórico visible alcance a cubrir camas viejas
     * de clavel en vez de recortarse al inicio del año en curso.
     */
    public static function fechaMinimaClavel(): string {
        $conexion = Database::getConnection();
        $result = $conexion->query("
            SELECT MIN(fecha_siembra) AS minimo
            FROM plane
            WHERE plantas > 0 AND UPPER(TRIM(producto)) IN ('CLAVEL', 'MINICLAVEL')
        ");
        $fila = $result ? $result->fetch_assoc() : null;

        return $fila['minimo'] ?? '';
    }
}
