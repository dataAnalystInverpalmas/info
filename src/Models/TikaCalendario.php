<?php
declare(strict_types=1);

namespace App\Models;

use mysqli;
use DateTimeImmutable;

class TikaCalendario
{
    private mysqli $db;

    public function __construct(?mysqli $conexion = null)
    {
        if ($conexion) {
            $this->db = $conexion;
        } else {
            $this->db = $GLOBALS['conexion'];
        }
    }

    /**
     * Obtiene el listado de semanas filtrado y ordenado.
     */
    public function getList(array $filters = []): array
    {
        $where = [];
        $types = '';
        $params = [];

        if (!empty($filters['anio'])) {
            $where[] = "anio = ?";
            $types .= 'i';
            $params[] = (int)$filters['anio'];
        }

        if (isset($filters['modelo_tika_id']) && $filters['modelo_tika_id'] !== '') {
            if ($filters['modelo_tika_id'] === 'null' || $filters['modelo_tika_id'] === '0') {
                $where[] = "modelo_tika_id IS NULL";
            } elseif ($filters['modelo_tika_id'] === 'not_null') {
                $where[] = "modelo_tika_id IS NOT NULL";
            } else {
                $where[] = "modelo_tika_id = ?";
                $types .= 'i';
                $params[] = (int)$filters['modelo_tika_id'];
            }
        }

        if (!empty($filters['search'])) {
            $term = '%' . trim((string)$filters['search']) . '%';
            $where[] = "(semana LIKE ? OR anio LIKE ? OR observaciones LIKE ? OR CAST(modelo_tika_id AS CHAR) LIKE ?)";
            $types .= 'ssss';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql = "SELECT id, semana, anio, numero_semana, modelo_tika_id, fecha_inicio, fecha_fin, observaciones, created_at, updated_at 
                FROM tika_calendario";

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY semana ASC";

        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                return [];
            }
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $res = $stmt->get_result();
            return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        }

        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene una semana por ID.
     */
    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM tika_calendario WHERE id = ? LIMIT 1");
        if (!$stmt) return null;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Obtiene una semana por código YYWW.
     */
    public function getBySemana(int $semana): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM tika_calendario WHERE semana = ? LIMIT 1");
        if (!$stmt) return null;
        $stmt->bind_param("i", $semana);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Guarda o actualiza una semana individual.
     */
    public function save(array $data): array
    {
        $semana = (int)($data['semana'] ?? 0);
        $anio = (int)($data['anio'] ?? 0);
        $modeloId = isset($data['modelo_tika_id']) && $data['modelo_tika_id'] !== '' && $data['modelo_tika_id'] !== null
            ? (int)$data['modelo_tika_id']
            : null;
        $observaciones = trim((string)($data['observaciones'] ?? ''));

        if ($semana <= 0) {
            return ['success' => false, 'error' => 'La semana YYWW es obligatoria y debe ser mayor a 0.'];
        }

        // Si el año no viene, deducirlo de la semana YYWW
        if ($anio <= 0) {
            $yy = (int)substr((string)$semana, 0, 2);
            $anio = 2000 + $yy;
        }

        $numSemana = (int)substr((string)$semana, -2);
        if ($numSemana < 1 || $numSemana > 53) {
            return ['success' => false, 'error' => "El número de semana ({$numSemana}) debe estar entre 1 y 53."];
        }

        // Calcular fechas inicio y fin si no vienen dadas
        $fechas = self::calcularFechasSemana($anio, $numSemana);
        $fechaInicio = $data['fecha_inicio'] ?? $fechas['inicio'];
        $fechaFin = $data['fecha_fin'] ?? $fechas['fin'];

        $id = (int)($data['id'] ?? 0);

        if ($id > 0) {
            // Actualización
            $stmt = $this->db->prepare("UPDATE tika_calendario 
                SET semana = ?, anio = ?, numero_semana = ?, modelo_tika_id = ?, fecha_inicio = ?, fecha_fin = ?, observaciones = ? 
                WHERE id = ?");
            if (!$stmt) {
                return ['success' => false, 'error' => $this->db->error];
            }
            $stmt->bind_param("iiiisssi", $semana, $anio, $numSemana, $modeloId, $fechaInicio, $fechaFin, $observaciones, $id);
            if (!$stmt->execute()) {
                return ['success' => false, 'error' => $stmt->error];
            }
            return ['success' => true, 'id' => $id, 'action' => 'updated'];
        }

        // Creación
        $stmt = $this->db->prepare("INSERT INTO tika_calendario (semana, anio, numero_semana, modelo_tika_id, fecha_inicio, fecha_fin, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE modelo_tika_id = VALUES(modelo_tika_id), observaciones = VALUES(observaciones)");
        if (!$stmt) {
            return ['success' => false, 'error' => $this->db->error];
        }
        $stmt->bind_param("iiiisss", $semana, $anio, $numSemana, $modeloId, $fechaInicio, $fechaFin, $observaciones);
        if (!$stmt->execute()) {
            return ['success' => false, 'error' => $stmt->error];
        }

        return ['success' => true, 'id' => $this->db->insert_id, 'action' => 'created'];
    }

    /**
     * Actualización rápida en línea del modelo_tika_id.
     */
    public function updateModelo(int $id, ?int $modeloId): bool
    {
        $stmt = $this->db->prepare("UPDATE tika_calendario SET modelo_tika_id = ? WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("ii", $modeloId, $id);
        return $stmt->execute();
    }

    /**
     * Asignación masiva de modelo por lote de IDs.
     */
    public function bulkAssignModelo(array $ids, ?int $modeloId): int
    {
        $ids = array_filter(array_map('intval', $ids));
        if (empty($ids)) return 0;

        $inList = implode(',', $ids);
        if ($modeloId !== null) {
            $sql = "UPDATE tika_calendario SET modelo_tika_id = ? WHERE id IN ($inList)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $modeloId);
            $stmt->execute();
        } else {
            $sql = "UPDATE tika_calendario SET modelo_tika_id = NULL WHERE id IN ($inList)";
            $this->db->query($sql);
        }
        return $this->db->affected_rows;
    }

    /**
     * Asignación de modelo a todo un año.
     */
    public function assignModeloByAnio(int $anio, ?int $modeloId): int
    {
        if ($modeloId !== null) {
            $stmt = $this->db->prepare("UPDATE tika_calendario SET modelo_tika_id = ? WHERE anio = ?");
            $stmt->bind_param("ii", $modeloId, $anio);
            $stmt->execute();
        } else {
            $stmt = $this->db->prepare("UPDATE tika_calendario SET modelo_tika_id = NULL WHERE anio = ?");
            $stmt->bind_param("i", $anio);
            $stmt->execute();
        }
        return $this->db->affected_rows;
    }

    /**
     * Genera automáticamente todas las semanas de un año (52 o 53 semanas).
     * Ejemplo para 2030: 3001 a 3052 o 3001 a 3053.
     */
    public function generarAnio(int $anio, int $semanasTotales = 52, ?int $modeloId = null, bool $sobrescribir = false): array
    {
        if ($anio < 2000 || $anio > 2100) {
            return ['success' => false, 'error' => 'El año debe estar entre 2000 y 2100.'];
        }

        if ($semanasTotales !== 52 && $semanasTotales !== 53) {
            return ['success' => false, 'error' => 'La cantidad de semanas debe ser 52 o 53.'];
        }

        $yy = substr((string)$anio, -2);
        $insertados = 0;
        $actualizados = 0;

        $stmtInsert = $this->db->prepare("INSERT INTO tika_calendario 
            (semana, anio, numero_semana, modelo_tika_id, fecha_inicio, fecha_fin) 
            VALUES (?, ?, ?, ?, ?, ?)");

        $stmtUpdate = $this->db->prepare("UPDATE tika_calendario 
            SET modelo_tika_id = COALESCE(?, modelo_tika_id), fecha_inicio = ?, fecha_fin = ? 
            WHERE semana = ?");

        for ($sem = 1; $sem <= $semanasTotales; $sem++) {
            $semanaYYWW = (int)($yy . str_pad((string)$sem, 2, '0', STR_PAD_LEFT));
            $fechas = self::calcularFechasSemana($anio, $sem);
            $fInicio = $fechas['inicio'];
            $fFin = $fechas['fin'];

            $existente = $this->getBySemana($semanaYYWW);

            if ($existente) {
                if ($sobrescribir && $stmtUpdate) {
                    $stmtUpdate->bind_param("issi", $modeloId, $fInicio, $fFin, $semanaYYWW);
                    $stmtUpdate->execute();
                    $actualizados++;
                }
            } else {
                if ($stmtInsert) {
                    $stmtInsert->bind_param("iiiiss", $semanaYYWW, $anio, $sem, $modeloId, $fInicio, $fFin);
                    $stmtInsert->execute();
                    $insertados++;
                }
            }
        }

        return [
            'success'      => true,
            'anio'         => $anio,
            'semanas'      => $semanasTotales,
            'insertados'   => $insertados,
            'actualizados' => $actualizados,
            'total'        => $insertados + $actualizados,
        ];
    }

    /**
     * Importa / sincroniza semanas desde la tabla dates.
     */
    public function sincronizarDesdeDates(int $desde = 2401, int $hasta = 2952): array
    {
        $sql = "INSERT INTO `tika_calendario` (`semana`, `anio`, `numero_semana`, `fecha_inicio`, `fecha_fin`)
                SELECT 
                    d.aass AS semana,
                    d.año AS anio,
                    d.semana AS numero_semana,
                    MIN(DATE(d.fecha)) AS fecha_inicio,
                    MAX(DATE(d.fecha)) AS fecha_fin
                FROM `dates` d
                WHERE d.aass >= ? AND d.aass <= ?
                GROUP BY d.aass, d.año, d.semana
                ORDER BY d.aass ASC
                ON DUPLICATE KEY UPDATE 
                    fecha_inicio = VALUES(fecha_inicio),
                    fecha_fin = VALUES(fecha_fin)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return ['success' => false, 'error' => $this->db->error];
        }
        $stmt->bind_param("ii", $desde, $hasta);
        $stmt->execute();

        return [
            'success' => true,
            'filas_afectadas' => $this->db->affected_rows,
            'desde' => $desde,
            'hasta' => $hasta
        ];
    }

    /**
     * Elimina una semana por ID.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM tika_calendario WHERE id = ?");
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Elimina todas las semanas de un año.
     */
    public function deleteByAnio(int $anio): int
    {
        $stmt = $this->db->prepare("DELETE FROM tika_calendario WHERE anio = ?");
        if (!$stmt) return 0;
        $stmt->bind_param("i", $anio);
        $stmt->execute();
        return $this->db->affected_rows;
    }

    /**
     * Obtiene los años registrados actualmente.
     */
    public function getAniosRegistrados(): array
    {
        $res = $this->db->query("SELECT DISTINCT anio FROM tika_calendario ORDER BY anio ASC");
        $anios = [];
        if ($res) {
            while ($r = $res->fetch_row()) {
                $anios[] = (int)$r[0];
            }
        }
        return $anios;
    }

    /**
     * Obtiene los IDs de modelos Tika registrados.
     */
    public function getModelosRegistrados(): array
    {
        $res = $this->db->query("SELECT DISTINCT modelo_tika_id FROM tika_calendario WHERE modelo_tika_id IS NOT NULL ORDER BY modelo_tika_id ASC");
        $modelos = [];
        if ($res) {
            while ($r = $res->fetch_row()) {
                $modelos[] = (int)$r[0];
            }
        }
        return $modelos;
    }

    /**
     * Retorna indicadores clave (KPIs) para el encabezado.
     */
    public function getKpis(): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total_semanas,
                    COUNT(DISTINCT anio) AS total_anios,
                    SUM(CASE WHEN modelo_tika_id IS NOT NULL THEN 1 ELSE 0 END) AS con_modelo,
                    SUM(CASE WHEN modelo_tika_id IS NULL THEN 1 ELSE 0 END) AS sin_modelo,
                    MIN(semana) AS semana_min,
                    MAX(semana) AS semana_max
                FROM tika_calendario";

        $res = $this->db->query($sql);
        $row = $res ? $res->fetch_assoc() : [];

        return [
            'total_semanas' => (int)($row['total_semanas'] ?? 0),
            'total_anios'   => (int)($row['total_anios'] ?? 0),
            'con_modelo'    => (int)($row['con_modelo'] ?? 0),
            'sin_modelo'    => (int)($row['sin_modelo'] ?? 0),
            'semana_min'    => $row['semana_min'] ?? null,
            'semana_max'    => $row['semana_max'] ?? null,
        ];
    }

    /**
     * Calcula la fecha de inicio (lunes) y fin (domingo) para una semana ISO dada.
     */
    public static function calcularFechasSemana(int $anio, int $numeroSemana): array
    {
        try {
            $dtLunes = (new DateTimeImmutable())->setISODate($anio, $numeroSemana, 1);
            $dtDomingo = (new DateTimeImmutable())->setISODate($anio, $numeroSemana, 7);
            return [
                'inicio' => $dtLunes->format('Y-m-d'),
                'fin'    => $dtDomingo->format('Y-m-d')
            ];
        } catch (\Throwable $e) {
            return ['inicio' => null, 'fin' => null];
        }
    }
}
