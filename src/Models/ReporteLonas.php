<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;

class ReporteLonas {

    /**
     * Mapea el nombre de la finca con sus posibles códigos o abreviaturas en ld_lonas
     * (por ejemplo: 'INVERPALMAS' -> ['INVERPALMAS', '001'], 'PALERMO' -> ['PALERMO', '002'])
     */
    public static function mapearFincaCodigos(string $finca): array {
        $finca = trim($finca);
        if ($finca === '') {
            return [];
        }

        $conexion = Database::getConnection();
        $codigos = [$finca];

        $stmt = $conexion->prepare("
            SELECT abreviatura, nombre 
            FROM farms 
            WHERE UPPER(TRIM(nombre)) = UPPER(?) OR abreviatura = ?
        ");
        if ($stmt) {
            $stmt->bind_param('ss', $finca, $finca);
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                while ($r = $res->fetch_assoc()) {
                    if (!empty($r['abreviatura'])) {
                        $codigos[] = (string)$r['abreviatura'];
                    }
                    if (!empty($r['nombre'])) {
                        $codigos[] = (string)$r['nombre'];
                    }
                }
            }
            $stmt->close();
        }

        return array_values(array_unique($codigos));
    }

    /**
     * Opciones de filtro en cascada desde la tabla `plane`.
     * Cada dimensión se calcula aplicando todos los filtros activos EXCEPTO el suyo propio,
     * permitiendo que los selectores se vayan segmentando dinámicamente según lo elegido.
     */
    public static function opcionesFiltro(array $filtros = []): array {
        $conexion = Database::getConnection();

        $dimensiones = [
            'finca'          => 'p.finca',
            'bloque'         => 'p.bloque',
            'variedad'       => 'p.variedad',
            'semana_siembra' => "SUBSTRING(YEARWEEK(p.fecha_siembra, 3), 3, 4)",
            'cosecha'        => "COALESCE(NULLIF(p.pico, ''), p.temporada)",
        ];

        $mapKeys = [
            'finca'          => 'fincas',
            'bloque'         => 'bloques',
            'variedad'       => 'variedades',
            'semana_siembra' => 'semanas_siembra',
            'cosecha'        => 'cosechas',
        ];

        $opciones = [
            'fincas'          => [],
            'bloques'         => [],
            'variedades'      => [],
            'semanas_siembra' => [],
            'cosechas'        => [],
        ];

        foreach ($dimensiones as $campo => $expr) {
            $where = ['p.plantas > 0', 'p.fecha_siembra IS NOT NULL'];
            $params = [];
            $types = '';

            foreach ($dimensiones as $otroCampo => $otraExpr) {
                if ($otroCampo === $campo) {
                    continue;
                }
                if (!empty($filtros[$otroCampo])) {
                    $where[] = "$otraExpr = ?";
                    $params[] = (string)$filtros[$otroCampo];
                    $types .= 's';
                }
            }

            $whereSql = implode(' AND ', $where);

            if ($campo === 'bloque') {
                $order = "CAST($expr AS UNSIGNED) ASC";
            } elseif ($campo === 'semana_siembra') {
                $order = "$expr DESC";
            } else {
                $order = "$expr ASC";
            }

            $sql = "
                SELECT DISTINCT $expr AS val 
                FROM plane p 
                WHERE $whereSql AND $expr IS NOT NULL AND $expr != '' 
                ORDER BY $order
            ";

            $stmt = $conexion->prepare($sql);
            if ($stmt === false) {
                continue;
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    $opciones[$mapKeys[$campo]][] = (string)$row['val'];
                }
            }
            $stmt->close();
        }

        return $opciones;
    }

    /**
     * Obtiene la información de encabezado del lote/siembra desde `plane`.
     */
    public static function obtenerEncabezado(array $filtros): array {
        $conexion = Database::getConnection();

        $info = [
            'finca'          => (string)($filtros['finca'] ?? ''),
            'bloque'         => (string)($filtros['bloque'] ?? ''),
            'variedad'       => (string)($filtros['variedad'] ?? ''),
            'semana_siembra' => (string)($filtros['semana_siembra'] ?? ''),
            'cosecha'        => (string)($filtros['cosecha'] ?? ''),
            'producto'       => '',
            'fecha_siembra'  => '',
            'camas'          => 0,
            'plantas'        => 0,
        ];

        $where = ['p.plantas > 0'];
        $params = [];
        $types = '';

        if (!empty($filtros['finca'])) {
            $where[] = "p.finca = ?";
            $params[] = (string)$filtros['finca'];
            $types .= 's';
        }
        if (!empty($filtros['bloque'])) {
            $where[] = "p.bloque = ?";
            $params[] = (string)$filtros['bloque'];
            $types .= 's';
        }
        if (!empty($filtros['variedad'])) {
            $where[] = "p.variedad = ?";
            $params[] = (string)$filtros['variedad'];
            $types .= 's';
        }
        if (!empty($filtros['semana_siembra'])) {
            $where[] = "SUBSTRING(YEARWEEK(p.fecha_siembra, 3), 3, 4) = ?";
            $params[] = (string)$filtros['semana_siembra'];
            $types .= 's';
        }
        if (!empty($filtros['cosecha'])) {
            $where[] = "COALESCE(NULLIF(p.pico, ''), p.temporada) = ?";
            $params[] = (string)$filtros['cosecha'];
            $types .= 's';
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT 
                p.finca,
                p.bloque,
                p.variedad,
                p.producto,
                SUBSTRING(YEARWEEK(p.fecha_siembra, 3), 3, 4) AS semana_siembra,
                MIN(p.fecha_siembra) AS fecha_siembra,
                COALESCE(NULLIF(p.pico, ''), p.temporada) AS cosecha,
                COUNT(*) AS camas,
                SUM(p.plantas) AS plantas
            FROM plane p
            WHERE $whereSql
            GROUP BY p.finca, p.bloque, p.variedad, p.producto, semana_siembra, cosecha
            LIMIT 1
        ";

        $stmt = $conexion->prepare($sql);
        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            if ($stmt->execute()) {
                $res = $stmt->get_result();
                if ($r = $res->fetch_assoc()) {
                    $info['finca']          = $r['finca'] ?? $info['finca'];
                    $info['bloque']         = (string)($r['bloque'] ?? $info['bloque']);
                    $info['variedad']       = $r['variedad'] ?? $info['variedad'];
                    $info['producto']       = $r['producto'] ?? '';
                    $info['semana_siembra'] = $r['semana_siembra'] ?? $info['semana_siembra'];
                    $info['fecha_siembra']  = $r['fecha_siembra'] ?? '';
                    $info['cosecha']        = $r['cosecha'] ?? $info['cosecha'];
                    $info['camas']          = (int)($r['camas'] ?? 0);
                    $info['plantas']        = (int)($r['plantas'] ?? 0);
                }
            }
            $stmt->close();
        }

        return $info;
    }

    /**
     * Construye la matriz de semanas 1 a 53 con días Lunes a Domingo para el año ISO indicado.
     * Llena los tallos desde `ld_lonas`. Si no hay dato, la celda queda como null (vacía).
     */
    public static function obtenerMatrizLonas(array $filtros, int $año = 2026): array {
        $conexion = Database::getConnection();

        // 1. Obtener el calendario de fechas, semanas y días para el año ISO desde la tabla `dates`
        $sqlDates = "
            SELECT 
                fecha,
                semana,
                dia_semana,
                dia,
                lunes
            FROM dates 
            WHERE año = ? 
            ORDER BY fecha ASC
        ";
        $stmtDates = $conexion->prepare($sqlDates);
        $dateToWeekDay = [];
        $semanaRango = [];
        $minFecha = null;
        $maxFecha = null;

        if ($stmtDates) {
            $stmtDates->bind_param('i', $año);
            if ($stmtDates->execute()) {
                $res = $stmtDates->get_result();
                while ($r = $res->fetch_assoc()) {
                    $fStr = substr((string)$r['fecha'], 0, 10);
                    $w = (int)$r['semana'];
                    $d = (int)$r['dia_semana']; // 1=Lunes .. 7=Domingo

                    $dateToWeekDay[$fStr] = [
                        'semana'     => $w,
                        'dia_semana' => $d,
                        'dia_nombre' => $r['dia'] ?? '',
                    ];

                    if (!isset($semanaRango[$w])) {
                        $semanaRango[$w] = ['inicio' => $fStr, 'fin' => $fStr];
                    } else {
                        $semanaRango[$w]['fin'] = $fStr;
                    }

                    if ($minFecha === null || $fStr < $minFecha) {
                        $minFecha = $fStr;
                    }
                    if ($maxFecha === null || $fStr > $maxFecha) {
                        $maxFecha = $fStr;
                    }
                }
            }
            $stmtDates->close();
        }

        // Si la tabla dates no tuviera datos para este año, calcular fechas ISO 8601 por código
        if (empty($dateToWeekDay) || $minFecha === null) {
            $fechaInicioIso = (new \DateTimeImmutable())->setISODate($año, 1, 1)->format('Y-m-d');
            $fechaFinIso = (new \DateTimeImmutable())->setISODate($año, 53, 7)->format('Y-m-d');
            $minFecha = $fechaInicioIso;
            $maxFecha = $fechaFinIso;

            $cur = new \DateTimeImmutable($fechaInicioIso);
            $fin = new \DateTimeImmutable($fechaFinIso);
            while ($cur <= $fin) {
                $fStr = $cur->format('Y-m-d');
                $w = (int)$cur->format('W');
                $d = (int)$cur->format('N');
                $dateToWeekDay[$fStr] = [
                    'semana'     => $w,
                    'dia_semana' => $d,
                    'dia_nombre' => '',
                ];
                if (!isset($semanaRango[$w])) {
                    $semanaRango[$w] = ['inicio' => $fStr, 'fin' => $fStr];
                } else {
                    $semanaRango[$w]['fin'] = $fStr;
                }
                $cur = $cur->modify('+1 day');
            }
        }

        // 2. Consultar `ld_lonas` para este lote en el rango de fechas del año ISO
        $dailyData = [];
        $hasAnyFilter = !empty($filtros['finca']) || !empty($filtros['bloque']) || !empty($filtros['variedad']);

        if ($hasAnyFilter) {
            $whereLonas = ["fecha >= ? AND fecha <= ?"];
            $paramsLonas = ["$minFecha 00:00:00", "$maxFecha 23:59:59"];
            $typesLonas = 'ss';

            if (!empty($filtros['finca'])) {
                $fincaCodigos = self::mapearFincaCodigos((string)$filtros['finca']);
                $placeholders = implode(',', array_fill(0, count($fincaCodigos), '?'));
                $whereLonas[] = "finca IN ($placeholders)";
                foreach ($fincaCodigos as $fc) {
                    $paramsLonas[] = $fc;
                    $typesLonas .= 's';
                }
            }

            if (!empty($filtros['bloque'])) {
                $whereLonas[] = "bloque = ?";
                $paramsLonas[] = (int)$filtros['bloque'];
                $typesLonas .= 'i';
            }

            if (!empty($filtros['variedad'])) {
                $whereLonas[] = "UPPER(TRIM(variedad)) = UPPER(TRIM(?))";
                $paramsLonas[] = (string)$filtros['variedad'];
                $typesLonas .= 's';
            }

            if (!empty($filtros['cosecha'])) {
                $whereLonas[] = "cosecha = ?";
                $paramsLonas[] = (string)$filtros['cosecha'];
                $typesLonas .= 's';
            }

            if (!empty($filtros['semana_siembra'])) {
                $whereLonas[] = "SUBSTRING(YEARWEEK(fecha_siembra, 3), 3, 4) = ?";
                $paramsLonas[] = (string)$filtros['semana_siembra'];
                $typesLonas .= 's';
            }

            $whereSql = implode(' AND ', $whereLonas);
            $sqlLonas = "
                SELECT 
                    DATE(fecha) AS fecha_corte,
                    SUM(tallos) AS total_tallos
                FROM ld_lonas
                WHERE $whereSql
                GROUP BY DATE(fecha)
            ";

            $stmtLonas = $conexion->prepare($sqlLonas);
            if ($stmtLonas) {
                $stmtLonas->bind_param($typesLonas, ...$paramsLonas);
                if ($stmtLonas->execute()) {
                    $resLonas = $stmtLonas->get_result();
                    while ($r = $resLonas->fetch_assoc()) {
                        $dailyData[$r['fecha_corte']] = (int)$r['total_tallos'];
                    }
                }
                $stmtLonas->close();
            }
        }

        // 3. Estructurar la matriz de 53 semanas x 7 días
        $nombresDias = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];

        $matriz = [];
        $totalesDias = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0];
        $totalGeneral = 0;
        $semanasConDatos = 0;
        $diasConDatos = 0;

        for ($w = 1; $w <= 53; $w++) {
            $rangoStr = '';
            if (isset($semanaRango[$w])) {
                $iniPart = date('d/m', strtotime($semanaRango[$w]['inicio']));
                $finPart = date('d/m', strtotime($semanaRango[$w]['fin']));
                $rangoStr = "$iniPart - $finPart";
            }

            $diasSemana = [];
            $totalSemana = 0;
            $tieneDatoSemana = false;

            for ($d = 1; $d <= 7; $d++) {
                $diasSemana[$d] = [
                    'numero' => $d,
                    'nombre' => $nombresDias[$d],
                    'tallos' => null,
                    'fecha'  => '',
                ];
            }

            $matriz[$w] = [
                'semana'       => $w,
                'rango'        => $rangoStr,
                'dias'         => $diasSemana,
                'total_semana' => null,
            ];
        }

        // Asignar los datos diarios mapeados
        foreach ($dateToWeekDay as $fStr => $infoDia) {
            $w = $infoDia['semana'];
            $d = $infoDia['dia_semana'];

            if ($w >= 1 && $w <= 53 && $d >= 1 && $d <= 7) {
                $matriz[$w]['dias'][$d]['fecha'] = $fStr;

                if (isset($dailyData[$fStr])) {
                    $tallos = $dailyData[$fStr];
                    // Si el valor es mayor a 0, se guarda; si es 0 o nulo queda vacío
                    if ($tallos > 0) {
                        $matriz[$w]['dias'][$d]['tallos'] = $tallos;
                        $matriz[$w]['total_semana'] = ($matriz[$w]['total_semana'] ?? 0) + $tallos;
                        $totalesDias[$d] += $tallos;
                        $totalGeneral += $tallos;
                        $diasConDatos++;
                    }
                }
            }
        }

        // Contar semanas que tuvieron al menos un día con datos
        for ($w = 1; $w <= 53; $w++) {
            if ($matriz[$w]['total_semana'] !== null && $matriz[$w]['total_semana'] > 0) {
                $semanasConDatos++;
            }
        }

        return [
            'año'               => $año,
            'semanas'           => array_values($matriz),
            'totales_dias'      => $totalesDias,
            'total_general'     => $totalGeneral,
            'semanas_con_datos' => $semanasConDatos,
            'dias_con_datos'    => $diasConDatos,
        ];
    }

    /**
     * Años disponibles en la base de datos para selector.
     */
    public static function obtenerAniosDisponibles(): array {
        $conexion = Database::getConnection();
        $anios = [];
        $res = $conexion->query("
            SELECT DISTINCT año 
            FROM dates 
            WHERE año >= 2023 AND año <= 2030 
            ORDER BY año DESC
        ");
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $anios[] = (int)$r['año'];
            }
        }
        if (empty($anios)) {
            $anios = [2026, 2025, 2024];
        }
        return $anios;
    }
}
