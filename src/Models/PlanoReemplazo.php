<?php
namespace App\Models;

use App\Helpers\Database;

class PlanoReemplazo {

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
                p.finca,
                p.bloque,
                p.tabla,
                p.nave,
                p.cama,
                p.fecha_siembra,
                DATE_FORMAT(p.fecha_siembra, '%y%v') AS semana_siembra,
                p.origen,
                p.tipo_siembra,
                p.variedad AS variedad_original,
                p.temporada AS cosecha_original,
                p.variedad_reem,
                p.cosecha_reem,
                p.plantas
             FROM plane p
             $where
             ORDER BY p.finca, p.bloque, p.tabla, p.nave, p.cama",
            "SELECT
                p.finca,
                p.bloque,
                p.tabla,
                p.nave,
                p.cama,
                p.fecha_siembra,
                DATE_FORMAT(p.fecha_siembra, '%y%v') AS semana_siembra,
                p.origen,
                p.tipo_siembra,
                p.variedad AS variedad_original,
                p.temporada AS cosecha_original,
                p.variedad_reem,
                p.cosecha_reem,
                p.plantas
             FROM plane p
             $where
             ORDER BY p.finca, p.bloque, p.tabla, p.nave, p.cama",
            "SELECT
                p.finca,
                p.bloque,
                p.tabla,
                p.nave,
                p.cama,
                p.fecha_siembra,
                DATE_FORMAT(p.fecha_siembra, '%y%v') AS semana_siembra,
                p.origen,
                p.tipo_siembra,
                p.variedad AS variedad_original,
                p.temporada AS cosecha_original,
                p.variedad_reem,
                p.cosecha_reem,
                p.plantas
             FROM plane p
             $where
             ORDER BY p.finca, p.bloque, p.tabla, p.nave, p.cama"
        ];

        return self::fetchAssocPreparedByFirstSuccessfulQuery($queries, $params, $types);
    }

    public static function getVariedadesActivas() {
        return self::fetchAssocByFirstSuccessfulQuery([
            "SELECT id, nombre FROM varieties WHERE estado = 1 ORDER BY nombre",
        ]);
    }

    public static function getTemporadasActivas() {
        return self::fetchAssocByFirstSuccessfulQuery([
            "SELECT id, nombre FROM seasons ORDER BY nombre"
        ]);
    }

    public static function getDistinctValues($column) {
        $conexion = Database::getConnection();
        
        $allowedColumns = ['finca', 'bloque', 'tabla', 'nave', 'tipo_siembra', 'variedad', 'temporada'];
        if (!in_array($column, $allowedColumns)) {
            return [];
        }

        $queries = [
            "SELECT DISTINCT $column FROM plane WHERE plantas > 0 AND $column IS NOT NULL AND $column != '' ORDER BY $column",
            "SELECT DISTINCT $column FROM informes.plane WHERE plantas > 0 AND $column IS NOT NULL AND $column != '' ORDER BY $column"
        ];

        foreach ($queries as $sql) {
            $result = $conexion->query($sql);
            if ($result === false) {
                continue;
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row[$column];
            }

            if (!empty($data)) {
                return $data;
            }
        }

        return [];
    }

    public static function getDistinctSemanasSiembra() {
        $conexion = Database::getConnection();

        $queries = [
            "SELECT DISTINCT DATE_FORMAT(fecha_siembra, '%y%v') AS semana_siembra
             FROM plane
             WHERE plantas > 0 AND fecha_siembra IS NOT NULL
             ORDER BY semana_siembra DESC",
            "SELECT DISTINCT DATE_FORMAT(fecha_siembra, '%y%v') AS semana_siembra
             FROM informes.plane
             WHERE plantas > 0 AND fecha_siembra IS NOT NULL
             ORDER BY semana_siembra DESC"
        ];

        foreach ($queries as $sql) {
            $result = $conexion->query($sql);
            if ($result === false) {
                continue;
            }

            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row['semana_siembra'];
            }

            if (!empty($data)) {
                return $data;
            }
        }

        return [];
    }

    public static function guardarMasivo(array $rows, $variedadNuevaId, $temporadaNuevaId, $createdBy) {
        $conexion = Database::getConnection();

        if (empty($rows)) {
            return ['success' => false, 'message' => 'Debe seleccionar al menos una ubicación'];
        }

        $variedadNuevaId = (int)$variedadNuevaId;
        $temporadaNuevaId = (int)$temporadaNuevaId;
        $createdBy = trim((string)$createdBy);

        if ($variedadNuevaId <= 0 || $temporadaNuevaId <= 0) {
            return ['success' => false, 'message' => 'Debe seleccionar variedad y temporada nuevas'];
        }

        $sql = "INSERT INTO informes.plano_reemplazos
                (
                    finca,
                    bloque,
                    tabla,
                    nave,
                    cama,
                    variedad_original,
                    temporada_original,
                    variedad_nueva_id,
                    temporada_nueva_id,
                    activo,
                    created_by
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)
                ON DUPLICATE KEY UPDATE
                    variedad_original = VALUES(variedad_original),
                    temporada_original = VALUES(temporada_original),
                    variedad_nueva_id = VALUES(variedad_nueva_id),
                    temporada_nueva_id = VALUES(temporada_nueva_id),
                    activo = 1,
                    created_by = VALUES(created_by),
                    updated_at = CURRENT_TIMESTAMP";

        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'message' => 'Error de preparación: ' . $conexion->error];
        }

        $procesados = 0;

        $conexion->begin_transaction();
        try {
            foreach ($rows as $row) {
                $finca = trim((string)($row['finca'] ?? ''));
                $bloque = trim((string)($row['bloque'] ?? ''));
                $tabla = trim((string)($row['tabla'] ?? ''));
                $nave = trim((string)($row['nave'] ?? ''));
                $cama = trim((string)($row['cama'] ?? ''));
                $variedadOriginal = trim((string)($row['variedad_original'] ?? ''));
                $temporadaOriginal = trim((string)($row['temporada_original'] ?? ''));

                if ($finca === '' || $bloque === '' || $tabla === '' || $nave === '' || $cama === '') {
                    continue;
                }

                $stmt->bind_param(
                    'sssssssiis',
                    $finca,
                    $bloque,
                    $tabla,
                    $nave,
                    $cama,
                    $variedadOriginal,
                    $temporadaOriginal,
                    $variedadNuevaId,
                    $temporadaNuevaId,
                    $createdBy
                );

                if (!$stmt->execute()) {
                    throw new \RuntimeException($stmt->error);
                }

                $procesados++;
            }

            $conexion->commit();
            return ['success' => true, 'message' => 'Reemplazo guardado', 'procesados' => $procesados];
        } catch (\Throwable $e) {
            $conexion->rollback();
            return ['success' => false, 'message' => 'No se pudo guardar: ' . $e->getMessage()];
        }
    }
}
