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

    /**
     * Mismo rango por defecto que rangoDesdeInicioAnoHastaDomingoAnterior, pero
     * expresado en semanas ISO con formato yyww (ej. 2601).
     */
    public static function rangoSemanasDesdeInicioAnoHastaSemanaAnterior(): array {
        $rango = self::rangoDesdeInicioAnoHastaDomingoAnterior();
        $aYyww = static function (string $fecha): string {
            $d = new \DateTimeImmutable($fecha);
            return substr($d->format('o'), -2) . $d->format('W');
        };

        return [
            'desde' => $aYyww($rango['desde']),
            'hasta' => $aYyww($rango['hasta']),
        ];
    }

    /**
     * Convierte una semana ISO yyww (ej. 2601) a fecha Y-m-d: el lunes si
     * $finDeSemana es false, el domingo si es true. Devuelve '' si no es válida.
     */
    public static function semanaYywwAFecha(string $yyww, bool $finDeSemana = false): string {
        $yyww = trim($yyww);
        if (!preg_match('/^(\d{2})(\d{2})$/', $yyww, $m)) {
            return '';
        }
        $anio = 2000 + (int)$m[1];
        $semana = (int)$m[2];
        $semanasDelAnio = (int)(new \DateTimeImmutable())->setISODate($anio, 53)->format('W') === 53 ? 53 : 52;
        if ($semana < 1 || $semana > $semanasDelAnio) {
            return '';
        }

        return (new \DateTimeImmutable())->setISODate($anio, $semana, $finDeSemana ? 7 : 1)->format('Y-m-d');
    }
}
