<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\SeguimientoProduccion;

class SeguimientoProduccionController
{
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public function index(): void
    {
        $filtros = SeguimientoProduccion::getFilterOptions();

        $tipos = $filtros['tipos'];
        $tiposPorDefecto = array_values(array_intersect(SeguimientoProduccion::TIPOS_POR_DEFECTO, $tipos));
        if (empty($tiposPorDefecto)) {
            $tiposPorDefecto = $tipos;
        }

        extract([
            'fincas'              => $filtros['fincas'],
            'bloques'             => $filtros['bloques'],
            'flores'              => $filtros['flores'],
            'variedades'          => $filtros['variedades'],
            'cosechas'            => $filtros['cosechas'],
            'tipos'               => $tipos,
            'tiposPorDefecto'     => $tiposPorDefecto,
            'semanaDesdeDefault'  => self::isoSemana(strtotime('-10 weeks')),
            'semanaHastaDefault'  => self::isoSemana(time()),
        ]);

        require_once __DIR__ . '/../Views/SeguimientoProduccion/index.php';
    }

    /** Devuelve una fecha como semana ISO-8601 en formato aaww (año ISO de 2 dígitos + semana). */
    private static function isoSemana(int $timestamp): string
    {
        return substr(date('o', $timestamp), -2) . date('W', $timestamp);
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

            echo json_encode(SeguimientoProduccion::getTrendData($queryParams), self::JSON_FLAGS);
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
            echo json_encode(SeguimientoProduccion::getFilterOptions($queryParams), self::JSON_FLAGS);
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
