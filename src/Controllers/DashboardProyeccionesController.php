<?php
namespace App\Controllers;

use App\Models\DashboardProyecciones;

class DashboardProyeccionesController
{
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public function index()
    {
        require_once __DIR__ . '/../Views/DashboardProyecciones/index.php';
    }

    public static function data($filters = [])
    {
        return DashboardProyecciones::getDashboardData($filters);
    }

    public static function handleDataRequest(array $queryParams, array $server): void
    {
        self::sendJsonHeader();

        register_shutdown_function(function () {
            $error = error_get_last();
            if (!$error) {
                return;
            }

            if (!headers_sent()) {
                http_response_code(500);
                self::sendJsonHeader();
            }

            echo json_encode([
                'ok' => false,
                'message' => 'Error fatal en dashboard_proyecciones',
                'error' => $error['message'],
            ], self::JSON_FLAGS);
        });

        try {
            if (($server['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
                http_response_code(405);
                echo json_encode([
                    'ok' => false,
                    'message' => 'Metodo no permitido',
                ], self::JSON_FLAGS);
                return;
            }

            echo json_encode(self::data($queryParams), self::JSON_FLAGS);
        } catch (\Throwable $exception) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'message' => 'Error interno del servidor',
                'error' => $exception->getMessage(),
            ], self::JSON_FLAGS);
        }
    }

    private static function sendJsonHeader(): void
    {
        header('Content-Type: application/json; charset=utf-8');
    }
}
