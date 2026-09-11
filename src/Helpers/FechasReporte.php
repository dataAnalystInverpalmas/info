<?php
namespace App\Helpers;

class FechasReporte {

    /**
     * Rango por defecto de los dashboards de Reportes: desde el lunes ISO de la
     * semana 1 del año actual (puede caer en diciembre del año anterior) hasta el
     * domingo de la última semana completa (el domingo antes del lunes de esta semana).
     */
    public static function rangoDesdeInicioAnoHastaDomingoAnterior(): array {
        $hoy = new \DateTimeImmutable('today');
        $anioIso = (int)$hoy->format('o');

        $desde = (new \DateTimeImmutable())->setISODate($anioIso, 1, 1);

        $diaSemanaIso = (int)$hoy->format('N');
        $lunesEstaSemana = $hoy->modify('-' . ($diaSemanaIso - 1) . ' days');
        $hasta = $lunesEstaSemana->modify('-1 day');

        return [
            'desde' => $desde->format('Y-m-d'),
            'hasta' => $hasta->format('Y-m-d'),
        ];
    }
}
