<?php
declare(strict_types=1);

namespace App\Helpers;

use mysqli;
use mysqli_result;
use RuntimeException;

/**
 * Helper de Base de Datos para Inverpalmas.
 * Proporciona acceso a la conexión mysqli global y utilidades modernas
 * preparadas para desarrollo ágil y asistido por IA (Prepared Statements,
 * helpers fetchAll/fetchOne/insert/execute, y transacciones).
 */
class Database
{
    private static ?\PDO $pdoInstance = null;

    /**
     * Obtiene la instancia activa de la conexión mysqli principal
     */
    public static function getConnection(): mysqli
    {
        global $conexion;
        if (!isset($conexion) || !($conexion instanceof mysqli)) {
            include_once __DIR__ . '/../../funciones/conexion.php';
        }
        if (!isset($conexion) || $conexion->connect_errno) {
            throw new RuntimeException("Error de conexión a la base de datos: " . ($conexion->connect_error ?? 'Desconocido'));
        }
        return $conexion;
    }

    /**
     * Obtiene una instancia singleton de PDO usando la misma configuración central (.env)
     */
    public static function getPdo(): \PDO
    {
        if (self::$pdoInstance instanceof \PDO) {
            return self::$pdoInstance;
        }

        if (!defined('CONEXION_LOADED')) {
            include_once __DIR__ . '/../../funciones/conexion.php';
        }

        $host   = getenv('DB_HOST') ?: '127.0.0.1';
        $dbname = getenv('DB_NAME') ?: 'info';
        $user   = getenv('DB_USER') ?: 'root';
        $pass   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
        $port   = getenv('DB_PORT') ?: '3306';

        $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
        self::$pdoInstance = new \PDO($dsn, $user, $pass, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);

        return self::$pdoInstance;
    }

    /**
     * Ejecuta una consulta SELECT con parámetros preparados y retorna todas las filas asociativas.
     *
     * @param string $sql Consulta SQL con marcadores '?'
     * @param array $params Lista de parámetros a vincular
     * @return array<int, array<string, mixed>>
     */
    public static function fetchAll(string $sql, array $params = []): array
    {
        $db = self::getConnection();
        if (empty($params)) {
            $result = $db->query($sql);
            if ($result === false) {
                throw new RuntimeException("Error en consulta: " . $db->error);
            }
            $rows = $result->fetch_all(MYSQLI_ASSOC);
            $result->free();
            return $rows;
        }

        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException("Error al preparar consulta: " . $db->error);
        }

        self::bindParams($stmt, $params);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $rows;
    }

    /**
     * Ejecuta una consulta SELECT con parámetros preparados y retorna una única fila o null.
     */
    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $rows = self::fetchAll($sql, $params);
        return $rows[0] ?? null;
    }

    /**
     * Ejecuta una consulta INSERT, UPDATE o DELETE y retorna el número de filas afectadas.
     */
    public static function execute(string $sql, array $params = []): int
    {
        $db = self::getConnection();
        if (empty($params)) {
            if ($db->query($sql) === false) {
                throw new RuntimeException("Error en ejecución: " . $db->error);
            }
            return $db->affected_rows;
        }

        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException("Error al preparar sentencia: " . $db->error);
        }

        self::bindParams($stmt, $params);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected;
    }

    /**
     * Inserta un registro en una tabla a partir de un arreglo asociativo y retorna el ID insertado.
     */
    public static function insert(string $table, array $data): int
    {
        if (empty($data)) {
            throw new RuntimeException("No se enviaron datos para insertar en {$table}");
        }

        $columns = array_keys($data);
        $escapedColumns = array_map(static fn($col) => "`" . preg_replace('/[^a-zA-Z0-9_]/', '', (string)$col) . "`", $columns);
        $placeholders = array_fill(0, count($columns), '?');

        $sql = sprintf(
            "INSERT INTO `%s` (%s) VALUES (%s)",
            preg_replace('/[^a-zA-Z0-9_]/', '', $table),
            implode(', ', $escapedColumns),
            implode(', ', $placeholders)
        );

        $db = self::getConnection();
        $stmt = $db->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException("Error al preparar insert en {$table}: " . $db->error);
        }

        self::bindParams($stmt, array_values($data));
        $stmt->execute();
        $insertId = (int)$stmt->insert_id;
        $stmt->close();
        return $insertId;
    }

    /**
     * Escapa una cadena para consultas dinámicas
     */
    public static function escape(string $value): string
    {
        return self::getConnection()->real_escape_string($value);
    }

    public static function beginTransaction(): void
    {
        self::getConnection()->begin_transaction();
    }

    public static function commit(): void
    {
        self::getConnection()->commit();
    }

    public static function rollback(): void
    {
        self::getConnection()->rollback();
    }

    /**
     * Vincula automáticamente los parámetros al statement infiriendo tipos.
     */
    private static function bindParams(\mysqli_stmt $stmt, array $params): void
    {
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
        }
        $stmt->bind_param($types, ...$params);
    }
}
