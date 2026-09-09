<?php
declare(strict_types=1);

class Auth {

    public static function verify(): void {
        $expected = getenv('API_SECRET_TOKEN') ?: '';
        if ($expected === '') {
            Response::error('API token not configured on server', 500);
        }

        $provided = self::extractToken();

        if ($provided === '' || !hash_equals($expected, $provided)) {
            Response::error('Unauthorized', 401);
        }
    }

    private static function extractToken(): string {
        // Standard: Authorization: Bearer <token>
        $header = $_SERVER['HTTP_AUTHORIZATION']
               ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION']
               ?? '';
        if (preg_match('/^Bearer\s+(\S+)$/i', $header, $m)) {
            return $m[1];
        }
        // Fallback via query param (legacy, se mantiene por compatibilidad con
        // notebooks antiguos de Databricks). El nuevo cliente usa el header Bearer,
        // que no expone el token en URLs/logs. Se puede eliminar cuando no queden
        // consumidores con api_key.
        return isset($_GET['api_key']) ? trim((string)$_GET['api_key']) : '';
    }
}
