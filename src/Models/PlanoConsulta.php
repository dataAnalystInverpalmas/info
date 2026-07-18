<?php
namespace App\Models;

use App\Helpers\Database;

class PlanoConsulta {

    private static function fetchAssocPreparedByFirstSuccessfulQuery(array $queries, array $params = [], string $types = '') {
        $conexion = Database::getConnection();

        foreach ($queries as $sql) {
            $stmt = $conexion->prepare($sql);
            if ($stmt === false) {
                continue;
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                $stmt->close();
                continue;
            }

            $result = $stmt->get_result();
            if ($result === false) {
                $stmt->close();
                continue;
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            $stmt->close();
            return $data;
        }

        return [];
    }

    private static function fetchAssocByFirstSuccessfulQuery(array $queries) {
        $conexion = Database::getConnection();

        foreach ($queries as $sql) {
            $result = $conexion->query($sql);
            if ($result === false) {
                continue;
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }

            return $data;
        }

        return [];
    }

    public static function getBaseListado($filters = []) {
        $where = "WHERE plantas > 0";
        $params = [];
        $types = "";

        if (!empty($filters['finca'])) {
            $where .= " AND finca = ?";
            $params[] = $filters['finca'];
            $types .= "s";
        }
        if (!empty($filters['bloque'])) {
            $where .= " AND bloque = ?";
            $params[] = $filters['bloque'];
            $types .= "s";
        }
        if (!empty($filters['tabla'])) {
            $where .= " AND tabla = ?";
            $params[] = $filters['tabla'];
            $types .= "s";
        }
        if (!empty($filters['nave'])) {
            $where .= " AND nave = ?";
            $params[] = $filters['nave'];
            $types .= "s";
        }
        if (!empty($filters['tipo_siembra'])) {
            $where .= " AND tipo_siembra = ?";
            $params[] = $filters['tipo_siembra'];
            $types .= "s";
        }
        if (!empty($filters['variedad'])) {
            $where .= " AND (variedad = ? OR variedad_reem = ?)";
            $params[] = $filters['variedad'];
            $params[] = $filters['variedad'];
            $types .= "ss";
        }
        if (!empty($filters['cosecha'])) {
            $where .= " AND (temporada = ? OR cosecha_reem = ?)";
            $params[] = $filters['cosecha'];
            $params[] = $filters['cosecha'];
            $types .= "ss";
        }
        if (!empty($filters['semana_siembra'])) {
            $where .= " AND DATE_FORMAT(fecha_siembra, '%y%v') = ?";
            $params[] = $filters['semana_siembra'];
            $types .= "s";
        }

        $queries = [
            "SELECT
                finca,
                bloque,
                tabla,
                nave,
                cama,
                fecha_siembra,
                DATE_FORMAT(fecha_siembra, '%y%v') AS semana_siembra,
                origen,
                tipo_siembra,
                variedad AS variedad_original,
                temporada AS cosecha_original,
                variedad_reem,
                cosecha_reem,
                plantas
             FROM plane
             $where
             ORDER BY finca, bloque, tabla, nave, cama",
            "SELECT
                finca,
                bloque,
                tabla,
                nave,
                cama,
                fecha_siembra,
                DATE_FORMAT(fecha_siembra, '%y%v') AS semana_siembra,
                origen,
                tipo_siembra,
                variedad AS variedad_original,
                temporada AS cosecha_original,
                variedad_reem,
                cosecha_reem,
                plantas
             FROM informes.plane
             $where
             ORDER BY finca, bloque, tabla, nave, cama"
        ];

        return self::fetchAssocPreparedByFirstSuccessfulQuery($queries, $params, $types);
    }

    public static function getDistinctValues($column, $filters = []) {
        $conexion = Database::getConnection();

        $allowedColumns = ['finca', 'bloque', 'tabla', 'nave', 'tipo_siembra', 'variedad', 'temporada'];
        if (!in_array($column, $allowedColumns)) {
            return [];
        }

        // Construir WHERE dinámicamente basado en filtros
        $where = "plantas > 0";
        $params = [];
        $types = "";

        if (!empty($filters['finca'])) {
            $where .= " AND finca = ?";
            $params[] = $filters['finca'];
            $types .= "s";
        }
        if (!empty($filters['bloque'])) {
            $where .= " AND bloque = ?";
            $params[] = $filters['bloque'];
            $types .= "s";
        }
        if (!empty($filters['tabla'])) {
            $where .= " AND tabla = ?";
            $params[] = $filters['tabla'];
            $types .= "s";
        }
        if (!empty($filters['nave'])) {
            $where .= " AND nave = ?";
            $params[] = $filters['nave'];
            $types .= "s";
        }
        if (!empty($filters['tipo_siembra'])) {
            $where .= " AND tipo_siembra = ?";
            $params[] = $filters['tipo_siembra'];
            $types .= "s";
        }

        $queries = [
            "SELECT DISTINCT $column FROM plane WHERE $where AND $column IS NOT NULL AND $column != '' ORDER BY $column",
            "SELECT DISTINCT $column FROM informes.plane WHERE $where AND $column IS NOT NULL AND $column != '' ORDER BY $column"
        ];

        foreach ($queries as $sql) {
            $stmt = $conexion->prepare($sql);
            if ($stmt === false) {
                continue;
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                $stmt->close();
                continue;
            }

            $result = $stmt->get_result();
            if ($result === false) {
                $stmt->close();
                continue;
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row[$column];
            }

            $stmt->close();

            if (!empty($data)) {
                return $data;
            }
        }

        return [];
    }

    public static function getDistinctSemanasSiembra($filters = []) {
        $conexion = Database::getConnection();

        // Construir WHERE dinámicamente basado en filtros
        $where = "plantas > 0";
        $params = [];
        $types = "";

        if (!empty($filters['finca'])) {
            $where .= " AND finca = ?";
            $params[] = $filters['finca'];
            $types .= "s";
        }
        if (!empty($filters['bloque'])) {
            $where .= " AND bloque = ?";
            $params[] = $filters['bloque'];
            $types .= "s";
        }
        if (!empty($filters['tabla'])) {
            $where .= " AND tabla = ?";
            $params[] = $filters['tabla'];
            $types .= "s";
        }
        if (!empty($filters['nave'])) {
            $where .= " AND nave = ?";
            $params[] = $filters['nave'];
            $types .= "s";
        }
        if (!empty($filters['tipo_siembra'])) {
            $where .= " AND tipo_siembra = ?";
            $params[] = $filters['tipo_siembra'];
            $types .= "s";
        }

        $queries = [
            "SELECT DISTINCT DATE_FORMAT(fecha_siembra, '%y%v') AS semana_siembra
             FROM plane
             WHERE $where AND fecha_siembra IS NOT NULL
             ORDER BY semana_siembra DESC",
            "SELECT DISTINCT DATE_FORMAT(fecha_siembra, '%y%v') AS semana_siembra
             FROM informes.plane
             WHERE $where AND fecha_siembra IS NOT NULL
             ORDER BY semana_siembra DESC"
        ];

        foreach ($queries as $sql) {
            $stmt = $conexion->prepare($sql);
            if ($stmt === false) {
                continue;
            }

            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                $stmt->close();
                continue;
            }

            $result = $stmt->get_result();
            if ($result === false) {
                $stmt->close();
                continue;
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row['semana_siembra'];
            }

            $stmt->close();

            if (!empty($data)) {
                return $data;
            }
        }

        return [];
    }
}
