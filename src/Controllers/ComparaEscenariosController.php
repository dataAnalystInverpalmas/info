<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\ComparaEscenarios;

class ComparaEscenariosController
{
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PARTIAL_OUTPUT_ON_ERROR;

    public function index(): void
    {
        [$semanaDesdeDefault, $semanaHastaDefault] = ComparaEscenarios::getRangoPorDefecto();

        $semanas = ComparaEscenarios::getSemanasDisponibles();
        $semanas = array_values(array_unique(array_merge($semanas, [$semanaDesdeDefault, $semanaHastaDefault])));
        sort($semanas, SORT_STRING);

        extract([
            'semanas' => $semanas,
            'semanaDesdeDefault' => $semanaDesdeDefault,
            'semanaHastaDefault' => $semanaHastaDefault,
        ]);

        require_once __DIR__ . '/../Views/ComparaEscenarios/index.php';
    }

    public static function handleDataRequest(array $queryParams, array $server): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            if (($server['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
                http_response_code(405);
                echo json_encode(['ok' => false, 'message' => 'Metodo no permitido'], self::JSON_FLAGS);
                return;
            }
            echo json_encode(ComparaEscenarios::getRows($queryParams), self::JSON_FLAGS);
        } catch (\Throwable $exception) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'message' => 'Error interno del servidor',
                'error' => $exception->getMessage(),
            ], self::JSON_FLAGS);
        }
    }
}
