<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\CurvasClavelProduccion;

class CurvasClavelProduccionController
{
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public function index(): void
    {
        $filtros = CurvasClavelProduccion::getFilterOptions();

        extract([
            'fincas'     => $filtros['fincas'],
            'bloques'    => $filtros['bloques'],
            'flores'     => $filtros['flores'],
            'variedades' => $filtros['variedades'],
            'cosechas'   => $filtros['cosechas'],
        ]);

        require_once __DIR__ . '/../Views/CurvasClavelProduccion/index.php';
    }

    public static function handleDataRequest(array $queryParams, array $server): void
    {
        self::sendJsonHeader();

        try {
            if (($server['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
                http_response_code(405);
                echo json_encode(['ok' => false, 'message' => 'Metodo no permitido'], self::JSON_FLAGS);
                return;
            }

            echo json_encode(CurvasClavelProduccion::getTrendData($queryParams), self::JSON_FLAGS);
        } catch (\Throwable $exception) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'message' => 'Error interno del servidor',
                'error' => $exception->getMessage(),
            ], self::JSON_FLAGS);
        }
    }

    public static function handleFiltersRequest(array $queryParams): void
    {
        self::sendJsonHeader();

        try {
            echo json_encode(CurvasClavelProduccion::getFilterOptions($queryParams), self::JSON_FLAGS);
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
