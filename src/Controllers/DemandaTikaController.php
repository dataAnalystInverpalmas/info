<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Models\DemandaTika;

class DemandaTikaController
{
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    private const AGRUPAR_RESUMEN_OPCIONES = [
        'color'         => 'Color',
        'mercado'       => 'Mercado',
        'submercado'    => 'Submercado',
        'grupo_demanda' => 'Grupo de demanda',
    ];

    public function index(): void
    {
        $filtros = DemandaTika::getFilterOptions();
        [$semanaDesdeDefault, $semanaHastaDefault] = DemandaTika::getRangoSemanasPorDefecto();

        // La semana actual y la del rango +52 pueden no tener datos aún (demanda
        // futura), así que se agregan como opción aunque no vengan de BD.
        $semanasParaRango = DemandaTika::combinarSemanas($filtros['semanas'], [$semanaDesdeDefault, $semanaHastaDefault]);

        extract([
            'flores'               => $filtros['flores'],
            'mercados'             => $filtros['mercados'],
            'submercados'          => $filtros['submercados'],
            'gruposDemanda'        => $filtros['gruposDemanda'],
            'items'                => $filtros['items'],
            'semanas'              => $semanasParaRango,
            'semanaDesdeDefault'   => $semanaDesdeDefault,
            'semanaHastaDefault'   => $semanaHastaDefault,
            'agruparOpciones'      => self::AGRUPAR_RESUMEN_OPCIONES,
            'agruparPorDefecto'    => DemandaTika::RESUMEN_POR_DEFECTO,
        ]);

        require_once __DIR__ . '/../Views/DemandaTika/index.php';
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

            $agrupar = (string)($queryParams['agrupar'] ?? DemandaTika::RESUMEN_POR_DEFECTO);
            if (!isset(self::AGRUPAR_RESUMEN_OPCIONES[$agrupar])) {
                $agrupar = DemandaTika::RESUMEN_POR_DEFECTO;
            }

            echo json_encode(DemandaTika::getPivotData($queryParams, $agrupar), self::JSON_FLAGS);
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
            echo json_encode(DemandaTika::getFilterOptions($queryParams), self::JSON_FLAGS);
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
