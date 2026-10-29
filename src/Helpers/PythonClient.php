<?php
declare(strict_types=1);

namespace App\Helpers;

use RuntimeException;

/**
 * Cliente HTTP ligero para comunicarse con el microservicio Python (FastAPI).
 * Soporta operaciones de matrices (NumPy), análisis de consultas con Pandas y
 * control de tareas programadas automáticas (APScheduler).
 */
class PythonClient
{
    private const DEFAULT_BASE_URL = 'http://python_api:8000';
    private const DEFAULT_TOKEN = '9035232260ca3fc11faa82b6b32b20605fe55c242e6903ca2f7e0887c3e9be17';

    private static function getBaseUrl(): string
    {
        $url = getenv('PYTHON_API_URL') ?: ($_ENV['PYTHON_API_URL'] ?? self::DEFAULT_BASE_URL);
        return rtrim((string)$url, '/');
    }

    private static function getToken(): string
    {
        $token = getenv('API_SECRET_TOKEN') ?: ($_ENV['API_SECRET_TOKEN'] ?? self::DEFAULT_TOKEN);
        return (string)$token;
    }

    /**
     * Realiza una petición GET al microservicio Python.
     *
     * @param string $endpoint Ruta del endpoint (ej. '/jobs' o '/health')
     * @param array<string, mixed> $queryParams Parámetros GET opcionales
     * @return array<string, mixed>
     */
    public static function get(string $endpoint, array $queryParams = [], int $timeoutSeconds = 15): array
    {
        $url = self::getBaseUrl() . '/' . ltrim($endpoint, '/');
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return self::request('GET', $url, null, $timeoutSeconds);
    }

    /**
     * Realiza una petición POST JSON al microservicio Python.
     *
     * @param string $endpoint Ruta del endpoint (ej. '/matrices/operar')
     * @param array<string, mixed> $payload Cuerpo JSON a enviar
     * @return array<string, mixed>
     */
    public static function post(string $endpoint, array $payload = [], int $timeoutSeconds = 30): array
    {
        $url = self::getBaseUrl() . '/' . ltrim($endpoint, '/');
        return self::request('POST', $url, $payload, $timeoutSeconds);
    }

    /**
     * Atajo para operar matrices en Python con NumPy.
     *
     * Operaciones soportadas:
     * 'multiply', 'add', 'subtract', 'transpose', 'inverse',
     * 'determinant', 'eigen', 'solve_linear', 'correlation', 'covariance', 'svd'
     *
     * @param string $operation Nombre de la operación
     * @param array<int, array<int, float|int>> $matrixA Matriz principal 2D
     * @param array<int, mixed>|null $matrixB Segunda matriz o vector independiente
     * @param int $decimals Decimales de redondeo
     * @return array<string, mixed>
     */
    public static function operateMatrix(
        string $operation,
        array $matrixA,
        ?array $matrixB = null,
        int $decimals = 6
    ): array {
        return self::post('/matrices/operar', [
            'operation' => $operation,
            'matrix_a'  => $matrixA,
            'matrix_b'  => $matrixB,
            'decimals'  => $decimals,
        ]);
    }

    /**
     * Ejecuta una consulta SELECT en Python con Pandas y retorna matriz de correlación,
     * matriz de covarianza y estadísticas descriptivas.
     *
     * @param string $sql Consulta SELECT (puede usar marcadores :param)
     * @param array<string, mixed> $params Parámetros con nombre
     * @param array<int, string>|null $columns Lista opcional de columnas numéricas
     * @return array<string, mixed>
     */
    public static function analyzeSqlQuery(
        string $sql,
        array $params = [],
        ?array $columns = null,
        int $decimals = 4
    ): array {
        return self::post('/matrices/analizar-consulta', [
            'sql'      => $sql,
            'params'   => empty($params) ? null : $params,
            'columns'  => $columns,
            'decimals' => $decimals,
        ]);
    }

    /**
     * @param array<string, mixed>|null $payload
     * @return array<string, mixed>
     */
    private static function request(string $method, string $url, ?array $payload, int $timeoutSeconds): array
    {
        $ch = curl_init($url);
        if ($ch === false) {
            throw new RuntimeException('No se pudo inicializar cURL para conectar con Python API.');
        }

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . self::getToken(),
        ];

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_TIMEOUT        => $timeoutSeconds,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => $headers,
        ];

        if ($payload !== null) {
            $jsonBody = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $headers[] = 'Content-Type: application/json';
            $options[CURLOPT_HTTPHEADER] = $headers;
            $options[CURLOPT_POSTFIELDS] = $jsonBody;
        }

        curl_setopt_array($ch, $options);
        $rawResponse = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($rawResponse === false) {
            throw new RuntimeException("Error de conexión con el microservicio Python ({$url}): {$curlError}");
        }

        $decoded = json_decode((string)$rawResponse, true);
        if (!is_array($decoded)) {
            throw new RuntimeException("Respuesta inválida de Python API (HTTP {$httpCode}): {$rawResponse}");
        }

        if ($httpCode >= 400) {
            $detail = $decoded['detail'] ?? ($decoded['message'] ?? 'Error desconocido en Python API');
            if (is_array($detail)) {
                $detail = json_encode($detail, JSON_UNESCAPED_UNICODE);
            }
            throw new RuntimeException("Error en Python API (HTTP {$httpCode}): {$detail}");
        }

        return $decoded;
    }
}

