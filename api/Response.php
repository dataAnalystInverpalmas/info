<?php
declare(strict_types=1);

/**
 * Helper estandarizado de Respuestas HTTP y JSON para la API de Inverpalmas (2026 Ready).
 * Mantiene compatibilidad total con Databricks, PowerBI y endpoints existentes,
 * a la vez que proporciona métodos semánticos para nuevos desarrollos.
 */
class Response {

    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;

    /**
     * Aplica los encabezados estándar de seguridad y tipo de contenido JSON
     */
    private static function applyHeaders(int $code = 200): void {
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: DENY');
        }
    }

    /**
     * Respuesta paginada con metadatos para ingestión masiva (Databricks / Clientes API)
     */
    public static function json(
        array $data,
        array $filters   = [],
        int   $total     = 0,
        int   $page      = 1,
        int   $pageSize  = 500
    ): void {
        // Conservar valores falsy válidos (p. ej. filtros 'activo=0'); solo descartar nulls
        $filters = array_filter((array)$filters, static function ($v) {
            return $v !== null;
        });
        self::applyHeaders(200);
        echo json_encode([
            'status'       => 'ok',
            'data'         => $data,
            'total'        => $total,
            'page'         => $page,
            'pageSize'     => $pageSize,
            'filters'      => $filters,
            'generated_at' => gmdate('c'),
        ], self::JSON_FLAGS);
        exit;
    }

    /**
     * Respuesta genérica de éxito con mensaje opcional
     */
    public static function success(mixed $data = null, string $message = 'Operación exitosa', int $code = 200, array $meta = []): void {
        self::applyHeaders($code);
        $payload = [
            'status'       => 'ok',
            'message'      => $message,
            'data'         => $data,
            'generated_at' => gmdate('c'),
        ];
        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }
        echo json_encode($payload, self::JSON_FLAGS);
        exit;
    }

    /**
     * Respuesta HTTP 201 Created para creación de recursos
     */
    public static function created(mixed $data = null, string $message = 'Recurso creado con éxito'): void {
        self::success($data, $message, 201);
    }

    /**
     * Respuesta paginada moderna simplificada
     */
    public static function paginate(array $data, int $total, int $page = 1, int $pageSize = 50, array $filters = []): void {
        self::json($data, $filters, $total, $page, $pageSize);
    }

    /**
     * Respuesta de error estándar
     */
    public static function error(string $message, int $code = 400, mixed $details = null): void {
        self::applyHeaders($code);
        $payload = [
            'status'       => 'error',
            'message'      => $message,
            'code'         => $code,
            'generated_at' => gmdate('c'),
        ];
        if ($details !== null) {
            $payload['details'] = $details;
        }
        echo json_encode($payload, self::JSON_FLAGS);
        exit;
    }

    /**
     * HTTP 400 Bad Request
     */
    public static function badRequest(string $message = 'Solicitud incorrecta o incompleta', mixed $details = null): void {
        self::error($message, 400, $details);
    }

    /**
     * HTTP 401 Unauthorized
     */
    public static function unauthorized(string $message = 'No autorizado. Token inválido o ausente'): void {
        self::error($message, 401);
    }

    /**
     * HTTP 403 Forbidden
     */
    public static function forbidden(string $message = 'Acceso denegado. Permisos insuficientes'): void {
        self::error($message, 403);
    }

    /**
     * HTTP 404 Not Found
     */
    public static function notFound(string $message = 'Recurso o endpoint no encontrado'): void {
        self::error($message, 404);
    }

    /**
     * Obtiene el payload JSON de la petición (php://input)
     *
     * @return array<string, mixed>
     */
    public static function getBody(): array {
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}
