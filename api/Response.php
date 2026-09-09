<?php
declare(strict_types=1);

class Response {

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
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        echo json_encode([
            'status'       => 'ok',
            'data'         => $data,
            'total'        => $total,
            'page'         => $page,
            'pageSize'     => $pageSize,
            'filters'      => $filters,
            'generated_at' => gmdate('c'),
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }

    public static function error(string $message, int $code = 400): void {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        echo json_encode([
            'status'  => 'error',
            'message' => $message,
            'code'    => $code,
        ], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
}
