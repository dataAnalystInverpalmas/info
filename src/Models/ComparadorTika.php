<?php
declare(strict_types=1);

namespace App\Models;

use App\Helpers\Database;
use PDO;
use RuntimeException;

class ComparadorTika
{
    public const DEFAULT_NETWORK_PATH = '\\\\172.10.18.2\\Usuarios\\Hilder\\Data\\tika';

    public const REQUIRED_FILES = [
        'esquejes_futuros.csv',
        'proy1.csv',
        'proy2.csv',
        'proy3.csv',
        'guarde.csv',
        'salvamento.csv',
    ];

    /**
     * Nombres alternativos aceptados por cada archivo requerido (ej. granel.csv equivale a guarde.csv).
     */
    public const FILE_ALIASES = [
        'esquejes_futuros.csv' => ['esquejes_futuros.csv', 'equejes_futuros.csv'],
        'proy1.csv'            => ['proy1.csv'],
        'proy2.csv'            => ['proy2.csv'],
        'proy3.csv'            => ['proy3.csv'],
        'guarde.csv'           => ['guarde.csv', 'granel.csv'],
        'salvamento.csv'       => ['salvamento.csv'],
    ];

    /**
     * Resuelve el nombre canónico de uno de los 6 archivos CSV de Tika a partir del nombre subido.
     */
    public static function resolveCanonicalCsvName(string $rawName): ?string
    {
        $base = strtolower(trim(basename($rawName)));
        if ($base === '') {
            return null;
        }

        foreach (self::FILE_ALIASES as $canonical => $aliases) {
            if (in_array($base, $aliases, true)) {
                return $canonical;
            }
        }

        // Coincidencia flexible por prefijo/contenido (ej. "granel (1).csv", "proy1_553.csv")
        if (strpos($base, 'esquejes_futuros') !== false || strpos($base, 'equejes_futuros') !== false) {
            return 'esquejes_futuros.csv';
        }
        if (preg_match('/(^|[^a-z0-9])proy1([^0-9]|$)/', $base)) {
            return 'proy1.csv';
        }
        if (preg_match('/(^|[^a-z0-9])proy2([^0-9]|$)/', $base)) {
            return 'proy2.csv';
        }
        if (preg_match('/(^|[^a-z0-9])proy3([^0-9]|$)/', $base)) {
            return 'proy3.csv';
        }
        if (strpos($base, 'guarde') !== false || strpos($base, 'granel') !== false) {
            return 'guarde.csv';
        }
        if (strpos($base, 'salvamento') !== false) {
            return 'salvamento.csv';
        }

        return null;
    }

    /**
     * Obtiene la ruta base de red configurada para los modelos Tika.
     */
    public static function getNetworkBasePath(): string
    {
        $envPath = getenv('TIKA_MODELS_PATH');
        if (is_string($envPath) && trim($envPath) !== '') {
            return rtrim(trim($envPath), "\\/");
        }
        return self::DEFAULT_NETWORK_PATH;
    }

    /**
     * Verifica si la carpeta base de red es accesible desde este servidor.
     */
    public static function isNetworkPathAccessible(): bool
    {
        $base = self::getNetworkBasePath();
        return @is_dir($base);
    }

    private static bool $schemaEnsured = false;

    /**
     * Crea las tablas del comparador Tika si no existen aún.
     */
    public static function ensureSchema(): void
    {
        if (self::$schemaEnsured) {
            return;
        }
        $pdo = Database::getPdo();
        $sqlPath = dirname(__DIR__, 2) . '/scripts/crear_tablas_comparador_tika.sql';
        if (!file_exists($sqlPath)) {
            throw new RuntimeException("No se encontró el archivo de migración: {$sqlPath}");
        }

        $sql = file_get_contents($sqlPath);
        if ($sql === false) {
            throw new RuntimeException("No se pudo leer el archivo SQL: {$sqlPath}");
        }

        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $stmt) {
            if ($stmt !== '') {
                $pdo->exec($stmt);
            }
        }
        self::$schemaEnsured = true;
    }

    /**
     * Convierte una cantidad con separador de miles "." o "," a entero.
     *
     * @param mixed $val
     */
    public static function parseIntQty($val): int
    {
        if ($val === null) {
            return 0;
        }
        $clean = str_replace(['.', ','], '', trim((string)$val));
        if ($clean === '' || !is_numeric($clean)) {
            return 0;
        }
        return (int)$clean;
    }

    /**
     * Importa un modelo desde la carpeta de red \\172.10.18.2\Usuarios\Hilder\Data\tika\<ID>
     * (o desde docs/tika/modelos/<ID> si existe como fallback local).
     */
    public static function importFromNetwork(int $modeloId, ?string $cargadoPor = null): array
    {
        if ($modeloId <= 0) {
            throw new RuntimeException("El ID del modelo debe ser un entero positivo.");
        }

        $baseRed = self::getNetworkBasePath();
        $candidateDirs = [
            $baseRed . DIRECTORY_SEPARATOR . $modeloId,
            dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'tika' . DIRECTORY_SEPARATOR . 'modelos' . DIRECTORY_SEPARATOR . $modeloId,
        ];

        $chosenDir = null;
        foreach ($candidateDirs as $dir) {
            if (@is_dir($dir)) {
                $chosenDir = $dir;
                break;
            }
        }

        if ($chosenDir === null) {
            throw new RuntimeException(
                "No se encontró la carpeta del modelo {$modeloId} en la red ({$baseRed}\\{$modeloId}). " .
                "Si el servidor no tiene acceso a la ruta de red, use la opción de subir los 6 archivos CSV."
            );
        }

        $filesMap = [];
        foreach (self::REQUIRED_FILES as $fileName) {
            $aliases = self::FILE_ALIASES[$fileName] ?? [$fileName];
            $foundPath = null;
            foreach ($aliases as $candidateName) {
                $fullPath = $chosenDir . DIRECTORY_SEPARATOR . $candidateName;
                if (is_file($fullPath)) {
                    $foundPath = $fullPath;
                    break;
                }
            }
            if ($foundPath === null) {
                $label = count($aliases) > 1 ? implode(' o ', $aliases) : $fileName;
                throw new RuntimeException("Falta el archivo {$label} en {$chosenDir}");
            }
            $filesMap[$fileName] = $foundPath;
        }

        return self::importFromFiles($modeloId, $filesMap, $cargadoPor, $chosenDir);
    }

    /**
     * Lee, agrega según las reglas exactas de preparar_comparacion.py e inserta por lotes en MySQL.
     *
     * @param int $modeloId
     * @param array<string, string> $filesMap Mapa nombre_archivo => ruta_fisica
     * @param string|null $cargadoPor
     * @param string $rutaOrigen
     * @return array Resumen con totales del modelo importado
     */
    public static function importFromFiles(
        int $modeloId,
        array $filesMap,
        ?string $cargadoPor = null,
        string $rutaOrigen = 'upload'
    ): array {
        if ($modeloId <= 0) {
            throw new RuntimeException("El ID del modelo debe ser un entero positivo.");
        }

        foreach (self::REQUIRED_FILES as $req) {
            if (empty($filesMap[$req]) || !is_file($filesMap[$req])) {
                throw new RuntimeException("Falta el archivo obligatorio: {$req}");
            }
        }

        self::ensureSchema();

        $variedades = []; // variedad => [flor, color]
        $allVars = [];    // variedad => true
        $escenario = '';

        // 1. esquejes_futuros.csv
        $esqAgg = []; // "semana|variedad|finca|ciclo" => [semana, variedad, finca, ciclo, cantidad]
        $totalEsq = 0;
        self::readCsv($filesMap['esquejes_futuros.csv'], function (array $row, array $idx) use (&$esqAgg, &$variedades, &$allVars, &$escenario, &$totalEsq) {
            $col0 = trim((string)($row[0] ?? ''));
            if ($col0 === '') {
                return;
            }
            $semStr = trim((string)($row[$idx['Semana']] ?? ''));
            $var = trim((string)($row[$idx['Variedad']] ?? ''));
            if ($semStr === '' || $var === '') {
                return;
            }

            $semana = (int)$semStr;
            $finca = trim((string)($row[$idx['Finca']] ?? ''));
            $ciclo = (int)trim((string)($row[$idx['Ciclo']] ?? '0'));
            $qty = self::parseIntQty($row[$idx['Cantidad']] ?? '0');

            if ($escenario === '') {
                $escCandidate = trim((string)($row[$idx['Escenario']] ?? ''));
                if ($escCandidate !== '') {
                    $escenario = $escCandidate;
                }
            }

            $flor = trim((string)($row[$idx['Flor']] ?? ''));
            $color = trim((string)($row[$idx['Color']] ?? ''));
            if (!isset($variedades[$var])) {
                $variedades[$var] = [$flor !== '' ? $flor : 'SIN FLOR', $color];
            }
            $allVars[$var] = true;

            $key = "{$semana}|{$var}|{$finca}|{$ciclo}";
            if (!isset($esqAgg[$key])) {
                $esqAgg[$key] = [$semana, $var, $finca, $ciclo, $qty];
            } else {
                $esqAgg[$key][4] += $qty;
            }
            $totalEsq += $qty;
        }, ['Variedad', 'Color', 'Finca', 'Ciclo', 'Cantidad', 'Semana', 'Escenario', 'Flor']);

        if ($escenario === '') {
            $escenario = 'Modelo_' . $modeloId;
        }

        // 2. proy1.csv, proy2.csv, proy3.csv
        $prodAgg = []; // "semana|variedad|finca|nueva" => [semana, variedad, finca, nueva, cantidad]
        $totalProd = 0;
        foreach (['proy1.csv', 'proy2.csv', 'proy3.csv'] as $proyFile) {
            self::readCsv($filesMap[$proyFile], function (array $row, array $idx) use (&$prodAgg, &$variedades, &$allVars, &$totalProd) {
                $semStr = trim((string)($row[$idx['SemanaProducion']] ?? ''));
                $var = trim((string)($row[$idx['Variedad']] ?? ''));
                if ($semStr === '' || $var === '') {
                    return;
                }

                $semana = (int)$semStr;
                $finca = trim((string)($row[$idx['Finca']] ?? ''));
                $origen = trim((string)($row[$idx['Origen']] ?? ''));
                $nueva = ($origen !== 'Planeado') ? 1 : 0;
                $qty = self::parseIntQty($row[$idx['Cantidad']] ?? '0');

                $flor = trim((string)($row[$idx['Flor']] ?? ''));
                $color = trim((string)($row[$idx['Color']] ?? ''));
                if (!isset($variedades[$var])) {
                    $variedades[$var] = [$flor !== '' ? $flor : 'SIN FLOR', $color];
                }
                $allVars[$var] = true;

                $key = "{$semana}|{$var}|{$finca}|{$nueva}";
                if (!isset($prodAgg[$key])) {
                    $prodAgg[$key] = [$semana, $var, $finca, $nueva, $qty];
                } else {
                    $prodAgg[$key][4] += $qty;
                }
                $totalProd += $qty;
            }, ['SemanaProducion', 'Variedad', 'Color', 'Cantidad', 'Finca', 'Origen', 'Flor']);
        }

        // 3. guarde.csv (lectura por posición: 0 semana, 1 variedad, 2 finca, 3 nsg, 4 cantidad, 6 pérdidas)
        $guaAgg = []; // "semana|variedad|finca|nsg" => [semana, variedad, finca, nsg, cantidad, perdidas]
        $totalGuarde = 0;
        $totalPerdidas = 0;
        self::readCsv($filesMap['guarde.csv'], function (array $row) use (&$guaAgg, &$allVars, &$totalGuarde, &$totalPerdidas) {
            $semStr = trim((string)($row[0] ?? ''));
            $var = trim((string)($row[1] ?? ''));
            if ($semStr === '' || $var === '') {
                return;
            }

            $semana = (int)$semStr;
            $finca = trim((string)($row[2] ?? ''));
            $nsg = trim((string)($row[3] ?? ''));
            $qty = self::parseIntQty($row[4] ?? '0');
            $perd = self::parseIntQty($row[6] ?? '0');

            $allVars[$var] = true;

            $key = "{$semana}|{$var}|{$finca}|{$nsg}";
            if (!isset($guaAgg[$key])) {
                $guaAgg[$key] = [$semana, $var, $finca, $nsg, $qty, $perd];
            } else {
                $guaAgg[$key][4] += $qty;
                $guaAgg[$key][5] += $perd;
            }
            $totalGuarde += $qty;
            $totalPerdidas += $perd;
        });

        // 4. salvamento.csv
        $salRaw = [];
        $subsSet = [];
        self::readCsv($filesMap['salvamento.csv'], function (array $row, array $idx) use (&$salRaw, &$subsSet, &$allVars) {
            $col0 = trim((string)($row[0] ?? ''));
            if ($col0 === '') {
                return;
            }
            $semStr = trim((string)($row[$idx['Semana']] ?? ''));
            $var = trim((string)($row[$idx['Variedad']] ?? ''));
            if ($semStr === '' || $var === '') {
                return;
            }

            $semana = (int)$semStr;
            $sub = trim((string)($row[$idx['Submercado']] ?? ''));
            $qty = self::parseIntQty($row[$idx['Cantidad']] ?? '0');

            if ($sub !== '') {
                $subsSet[$sub] = true;
            }
            $allVars[$var] = true;
            $salRaw[] = [$semana, $var, $sub, $qty];
        }, ['Semana', 'Variedad', 'Submercado', 'Cantidad']);

        $subsList = array_keys($subsSet);
        sort($subsList, SORT_STRING);
        $defaultSub = $subsList[0] ?? 'SIN_SUBMERCADO';

        $salAgg = []; // "semana|variedad|submercado" => [semana, variedad, submercado, cantidad]
        $totalSalv = 0;
        foreach ($salRaw as [$semana, $var, $sub, $qty]) {
            $subFinal = $sub !== '' ? $sub : $defaultSub;
            $key = "{$semana}|{$var}|{$subFinal}";
            if (!isset($salAgg[$key])) {
                $salAgg[$key] = [$semana, $var, $subFinal, $qty];
            } else {
                $salAgg[$key][3] += $qty;
            }
            $totalSalv += $qty;
        }
        unset($salRaw);

        // Asegurar todas las variedades en el catálogo
        foreach (array_keys($allVars) as $v) {
            if (!isset($variedades[$v])) {
                $variedades[$v] = ['SIN FLOR', ''];
            }
        }

        // Inserción transaccional por lotes usando PDO
        $pdo = Database::getPdo();
        $pdo->beginTransaction();
        try {
            // Reemplazar modelo si ya existía (ON DELETE CASCADE borra sus tablas hijas)
            $stmtDel = $pdo->prepare('DELETE FROM `tika_modelo` WHERE `id` = ?');
            $stmtDel->execute([$modeloId]);

            $stmtMod = $pdo->prepare(
                'INSERT INTO `tika_modelo` (`id`, `escenario`, `ruta`, `cargado_en`, `cargado_por`) VALUES (?, ?, ?, NOW(), ?)'
            );
            $stmtMod->execute([$modeloId, $escenario, $rutaOrigen, $cargadoPor]);

            // Upsert tika_variedad por lotes
            $varRows = [];
            foreach ($variedades as $nombre => [$flor, $color]) {
                $varRows[] = [$nombre, $flor, $color];
            }
            self::batchInsert(
                $pdo,
                'tika_variedad',
                ['nombre', 'flor', 'color'],
                $varRows,
                500,
                'ON DUPLICATE KEY UPDATE `flor` = IF(VALUES(`flor`) <> "SIN FLOR", VALUES(`flor`), `flor`), `color` = IF(VALUES(`color`) <> "", VALUES(`color`), `color`)'
            );

            // Insert tika_esquejes
            $esqRows = [];
            foreach ($esqAgg as $r) {
                $esqRows[] = [$modeloId, $r[0], $r[1], $r[2], $r[3], $r[4]];
            }
            self::batchInsert($pdo, 'tika_esquejes', ['modelo_id', 'semana', 'variedad', 'finca', 'ciclo', 'cantidad'], $esqRows, 1000);
            unset($esqAgg, $esqRows);

            // Insert tika_produccion
            $prodRows = [];
            foreach ($prodAgg as $r) {
                $prodRows[] = [$modeloId, $r[0], $r[1], $r[2], $r[3], $r[4]];
            }
            self::batchInsert($pdo, 'tika_produccion', ['modelo_id', 'semana', 'variedad', 'finca', 'nueva', 'cantidad'], $prodRows, 1000);
            unset($prodAgg, $prodRows);

            // Insert tika_guarde
            $guaRows = [];
            foreach ($guaAgg as $r) {
                $guaRows[] = [$modeloId, $r[0], $r[1], $r[2], $r[3], $r[4], $r[5]];
            }
            self::batchInsert($pdo, 'tika_guarde', ['modelo_id', 'semana', 'variedad', 'finca', 'nsg', 'cantidad', 'perdidas'], $guaRows, 1000);
            unset($guaAgg, $guaRows);

            // Insert tika_salvamento
            $salRows = [];
            foreach ($salAgg as $r) {
                $salRows[] = [$modeloId, $r[0], $r[1], $r[2], $r[3]];
            }
            self::batchInsert($pdo, 'tika_salvamento', ['modelo_id', 'semana', 'variedad', 'submercado', 'cantidad'], $salRows, 1000);
            unset($salAgg, $salRows);

            $pdo->commit();
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }

        return [
            'id'         => $modeloId,
            'escenario'  => $escenario,
            'ruta'       => $rutaOrigen,
            'totales'    => [
                'esquejes'   => $totalEsq,
                'produccion' => $totalProd,
                'guarde'     => $totalGuarde,
                'perdidas'   => $totalPerdidas,
                'salvamento' => $totalSalv,
            ],
        ];
    }

    /**
     * Lista todos los modelos cargados en BD con sus totales de validación.
     */
    public static function listarModelos(bool $conTotales = false): array
    {
        self::ensureSchema();
        $pdo = Database::getPdo();

        if (!$conTotales) {
            $stmt = $pdo->query(
                'SELECT `id`, `escenario`, `ruta`, `cargado_en` AS `fecha`, `cargado_por`
                 FROM `tika_modelo`
                 ORDER BY `id` DESC'
            );
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$r) {
                $r['id'] = (int)$r['id'];
            }
            return $rows;
        }

        $sql = 'SELECT
                    m.`id`,
                    m.`escenario`,
                    m.`ruta`,
                    m.`cargado_en` AS `fecha`,
                    m.`cargado_por`,
                    COALESCE(e.tot_esq, 0) AS `esquejes`,
                    COALESCE(p.tot_prod, 0) AS `produccion`,
                    COALESCE(g.tot_gua, 0) AS `guarde`,
                    COALESCE(s.tot_sal, 0) AS `salvamento`
                FROM `tika_modelo` m
                LEFT JOIN (SELECT `modelo_id`, SUM(`cantidad`) AS tot_esq FROM `tika_esquejes` GROUP BY `modelo_id`) e ON e.`modelo_id` = m.`id`
                LEFT JOIN (SELECT `modelo_id`, SUM(`cantidad`) AS tot_prod FROM `tika_produccion` GROUP BY `modelo_id`) p ON p.`modelo_id` = m.`id`
                LEFT JOIN (SELECT `modelo_id`, SUM(`cantidad`) AS tot_gua FROM `tika_guarde` GROUP BY `modelo_id`) g ON g.`modelo_id` = m.`id`
                LEFT JOIN (SELECT `modelo_id`, SUM(`cantidad`) AS tot_sal FROM `tika_salvamento` GROUP BY `modelo_id`) s ON s.`modelo_id` = m.`id`
                ORDER BY m.`id` DESC';

        $stmt = $pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$r) {
            $r['id'] = (int)$r['id'];
            $r['esquejes'] = (int)$r['esquejes'];
            $r['produccion'] = (int)$r['produccion'];
            $r['guarde'] = (int)$r['guarde'];
            $r['salvamento'] = (int)$r['salvamento'];
        }
        return $rows;
    }

    /**
     * Elimina un modelo cargado en BD.
     */
    public static function eliminarModelo(int $modeloId): bool
    {
        self::ensureSchema();
        $pdo = Database::getPdo();
        $stmt = $pdo->prepare('DELETE FROM `tika_modelo` WHERE `id` = ?');
        $stmt->execute([$modeloId]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Construye la estructura exacta DATOS para el frontend del comparador Tika.
     *
     * @param int[] $modeloIds
     * @return array{generado:string, infoVar:array<string,array{0:string,1:string}>, modelos:array}
     */
    public static function obtenerDatosComparador(array $modeloIds): array
    {
        self::ensureSchema();
        $pdo = Database::getPdo();

        $cleanIds = [];
        foreach ($modeloIds as $id) {
            $val = filter_var($id, FILTER_VALIDATE_INT);
            if ($val !== false && $val > 0 && !in_array($val, $cleanIds, true)) {
                $cleanIds[] = $val;
            }
        }

        if (empty($cleanIds)) {
            return [
                'generado' => date('Y-m-d H:i'),
                'infoVar'  => (object)[],
                'modelos'  => [],
            ];
        }

        $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
        $stmtMod = $pdo->prepare("SELECT `id`, `escenario`, `cargado_en` FROM `tika_modelo` WHERE `id` IN ({$placeholders})");
        $stmtMod->execute($cleanIds);
        $modMap = [];
        while ($row = $stmtMod->fetch(PDO::FETCH_ASSOC)) {
            $modMap[(int)$row['id']] = $row;
        }

        $stmtEsq = $pdo->prepare(
            "SELECT `semana`, `variedad`, `finca`, `ciclo`, `cantidad`
             FROM `tika_esquejes`
             WHERE `modelo_id` = ?
             ORDER BY `semana` ASC, `variedad` ASC, `finca` ASC, `ciclo` ASC"
        );
        $stmtProd = $pdo->prepare(
            "SELECT `semana`, `variedad`, `finca`, `nueva`, `cantidad`
             FROM `tika_produccion`
             WHERE `modelo_id` = ?
             ORDER BY `semana` ASC, `variedad` ASC, `finca` ASC, `nueva` ASC"
        );
        $stmtGua = $pdo->prepare(
            "SELECT `semana`, `variedad`, `finca`, `nsg`, `cantidad`, `perdidas`
             FROM `tika_guarde`
             WHERE `modelo_id` = ?
             ORDER BY `semana` ASC, `variedad` ASC, `finca` ASC, `nsg` ASC"
        );
        $stmtSal = $pdo->prepare(
            "SELECT `semana`, `variedad`, `submercado`, `cantidad`
             FROM `tika_salvamento`
             WHERE `modelo_id` = ?
             ORDER BY `semana` ASC, `variedad` ASC, `submercado` ASC"
        );

        $allRequestedVars = [];
        $modelosOut = [];

        foreach ($cleanIds as $mId) {
            if (!isset($modMap[$mId])) {
                continue;
            }
            $modInfo = $modMap[$mId];

            $stmtEsq->execute([$mId]);
            $esqDb = $stmtEsq->fetchAll(PDO::FETCH_NUM);

            $stmtProd->execute([$mId]);
            $prodDb = $stmtProd->fetchAll(PDO::FETCH_NUM);

            $stmtGua->execute([$mId]);
            $guaDb = $stmtGua->fetchAll(PDO::FETCH_NUM);

            $stmtSal->execute([$mId]);
            $salDb = $stmtSal->fetchAll(PDO::FETCH_NUM);

            $varsSet = [];
            $fincasSet = [];
            $subsSet = [];
            $nsgSet = [];

            foreach ($esqDb as $r) {
                $varsSet[$r[1]] = true;
                $fincasSet[$r[2]] = true;
            }
            foreach ($prodDb as $r) {
                $varsSet[$r[1]] = true;
                $fincasSet[$r[2]] = true;
            }
            foreach ($guaDb as $r) {
                $varsSet[$r[1]] = true;
                $fincasSet[$r[2]] = true;
                $nsgSet[$r[3]] = true;
            }
            foreach ($salDb as $r) {
                $varsSet[$r[1]] = true;
                $subsSet[$r[2]] = true;
            }

            $vars = array_keys($varsSet);
            sort($vars, SORT_STRING);
            foreach ($vars as $v) {
                $allRequestedVars[$v] = true;
            }

            $fincas = array_keys($fincasSet);
            sort($fincas, SORT_STRING);

            $subs = array_keys($subsSet);
            sort($subs, SORT_STRING);

            $nsg = array_keys($nsgSet);
            sort($nsg, SORT_STRING);

            $iVar = array_flip($vars);
            $iFinca = array_flip($fincas);
            $iSub = array_flip($subs);
            $iNsg = array_flip($nsg);

            $esqRows = [];
            foreach ($esqDb as $r) {
                $esqRows[] = [(int)$r[0], $iVar[$r[1]], $iFinca[$r[2]], (int)$r[3], (int)$r[4]];
            }
            unset($esqDb);

            $prodRows = [];
            foreach ($prodDb as $r) {
                $prodRows[] = [(int)$r[0], $iVar[$r[1]], $iFinca[$r[2]], (int)$r[3], (int)$r[4]];
            }
            unset($prodDb);

            $guaRows = [];
            foreach ($guaDb as $r) {
                $guaRows[] = [(int)$r[0], $iVar[$r[1]], $iFinca[$r[2]], $iNsg[$r[3]], (int)$r[4], (int)$r[5]];
            }
            unset($guaDb);

            $salRows = [];
            foreach ($salDb as $r) {
                $salRows[] = [(int)$r[0], $iVar[$r[1]], $iSub[$r[2]], (int)$r[3]];
            }
            unset($salDb);

            $modelosOut[] = [
                'id'        => (string)$mId,
                'escenario' => (string)$modInfo['escenario'],
                'vars'      => $vars,
                'fincas'    => $fincas,
                'subs'      => $subs,
                'nsg'       => $nsg,
                'esq'       => $esqRows,
                'prod'      => $prodRows,
                'guarde'    => $guaRows,
                'salv'      => $salRows,
            ];
        }

        $infoVar = [];
        if (!empty($allRequestedVars)) {
            $varNames = array_keys($allRequestedVars);
            sort($varNames, SORT_STRING);
            foreach (array_chunk($varNames, 500) as $chunk) {
                $ph = implode(',', array_fill(0, count($chunk), '?'));
                $stmtVar = $pdo->prepare("SELECT `nombre`, `flor`, `color` FROM `tika_variedad` WHERE `nombre` IN ({$ph})");
                $stmtVar->execute($chunk);
                while ($vr = $stmtVar->fetch(PDO::FETCH_ASSOC)) {
                    $flor = trim((string)$vr['flor']);
                    if ($flor !== '' && $flor !== 'SIN FLOR') {
                        $infoVar[$vr['nombre']] = [$flor, (string)$vr['color']];
                    }
                }
            }
            ksort($infoVar, SORT_STRING);
        }

        return [
            'generado' => date('Y-m-d H:i'),
            'infoVar'  => !empty($infoVar) ? $infoVar : (object)[],
            'modelos'  => $modelosOut,
        ];
    }

    /**
     * Lee un archivo CSV de forma eficiente en streaming, manejando BOM UTF-8.
     *
     * @param string $filePath
     * @param callable $onRow function(array $row, array $headerIndex): void
     * @param string[] $requiredHeaders
     */
    private static function readCsv(string $filePath, callable $onRow, array $requiredHeaders = []): void
    {
        $handle = @fopen($filePath, 'r');
        if ($handle === false) {
            throw new RuntimeException("No se pudo abrir el archivo CSV: {$filePath}");
        }

        try {
            // Avanzar puntero si el archivo inicia con BOM UTF-8 (\xEF\xBB\xBF)
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }

            $header = fgetcsv($handle);
            if ($header === false || empty($header)) {
                throw new RuntimeException("El archivo CSV está vacío: " . basename($filePath));
            }

            // Limpiar posibles comillas residuales o espacios en encabezados
            $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$header[0]);
            $header = array_map(static fn($h) => trim((string)$h, " \t\n\r\0\x0B\""), $header);
            $idx = array_flip($header);

            foreach ($requiredHeaders as $reqCol) {
                if (!isset($idx[$reqCol])) {
                    throw new RuntimeException(
                        sprintf("No se encontró la columna '%s' en %s", $reqCol, basename($filePath))
                    );
                }
            }

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) <= 1 && trim((string)($row[0] ?? '')) === '') {
                    continue;
                }
                $onRow($row, $idx);
            }
        } finally {
            fclose($handle);
        }
    }

    /**
     * Inserta múltiples registros por lotes dentro de una transacción PDO activa.
     */
    private static function batchInsert(
        PDO $pdo,
        string $table,
        array $columns,
        array $rows,
        int $chunkSize = 1000,
        string $onDuplicateClause = ''
    ): void {
        if (empty($rows)) {
            return;
        }

        $colList = implode(', ', array_map(static fn($c) => "`{$c}`", $columns));
        $rowPlaceholder = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';

        foreach (array_chunk($rows, $chunkSize) as $chunk) {
            $placeholders = implode(', ', array_fill(0, count($chunk), $rowPlaceholder));
            $sql = "INSERT INTO `{$table}` ({$colList}) VALUES {$placeholders}";
            if ($onDuplicateClause !== '') {
                $sql .= ' ' . $onDuplicateClause;
            }
            $params = [];
            foreach ($chunk as $r) {
                foreach ($r as $val) {
                    $params[] = $val;
                }
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
        }
    }
}
