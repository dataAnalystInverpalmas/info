<?php
namespace App\Models;

use App\Helpers\Database;

class Application {

    /**
     * DECISIÓN PENDIENTE: calcular las aplicaciones con fecha de siembra REAL o TEÓRICA.
     * Actualmente se usa la REAL (p.fecha_siembra) para todos los tipos de siembra.
     * Para volver a la fecha TEÓRICA (s.fecha_pico de la temporada) basta con
     * cambiar esta constante a false.
     */
    private const USAR_FECHA_SIEMBRA_REAL = true;

    public static function fechaAplicacionExpr(): string {
        $fechaSiembraJueves = "DATE_ADD(p.fecha_siembra, INTERVAL (3 - WEEKDAY(p.fecha_siembra)) DAY)";
        // Fecha pico derivada de la siembra real (jueves de la semana de siembra + pico/ciclo en semanas)
        $fechaPicoReal = "DATE_ADD($fechaSiembraJueves, INTERVAL + COALESCE(pr.pico, v.ciclo) WEEK)";
        if (self::USAR_FECHA_SIEMBRA_REAL) {
            // REAL: todas las siembras (normales, REEMPLAZO, ADICIONAL) parten de la fecha de siembra real
            $fechaBase = $fechaPicoReal;
        } else {
            // TEÓRICA (comportamiento anterior): siembras normales usan s.fecha_pico de la temporada;
            // solo REEMPLAZO/ADICIONAL usan la siembra real
            $fechaBase = "IF(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'), $fechaPicoReal, s.fecha_pico)";
        }
        return "DATE_ADD(
        IF(
            aa.calc_conciclo=0,
            DATE_ADD($fechaBase, INTERVAL - COALESCE(pr.pico, v.ciclo) WEEK),
            IF(aa.calc_conciclo=2,
                DATE_ADD($fechaBase, INTERVAL + COALESCE(pr.pico, v.ciclo) WEEK),
                IF(aa.calc_conciclo=3,
                    DATE_ADD($fechaBase, INTERVAL - COALESCE(pr.pico, v.ciclo) WEEK),
                    IF(aa.calc_conciclo=4,
                        DATE_ADD($fechaBase, INTERVAL - COALESCE(pr.pico, v.ciclo) WEEK),
                        IF(aa.calc_conciclo=5,
                            DATE_ADD($fechaBase, INTERVAL - COALESCE(pr.pico, v.ciclo) WEEK),
                            IF(aa.calc_conciclo=6,
                                DATE_ADD($fechaBase, INTERVAL - 0 WEEK),
                                IF(aa.calc_conciclo=7,
                                    DATE_ADD($fechaBase, INTERVAL - 0 WEEK),
                                    IF(aa.calc_conciclo=8,
                                        DATE_ADD($fechaBase, INTERVAL - 0 WEEK),
                                        DATE_ADD($fechaBase, INTERVAL - COALESCE(pr.pico, v.ciclo) + COALESCE(pr.pico, v.ciclo) WEEK)
                                    )
                                )
                            )
                        )
                    )
                )
            )
        ),
        INTERVAL a.valor * IF(aa.calc_conciclo=6,-1,1) DAY
        )";
    }

    public static function getMain(string $where): array {
        $conexion = Database::getConnection();
        $expr = self::fechaAplicacionExpr();
        $sql = "SELECT p.finca,
                p.bloque,
                p.variedad,
                s.cod_temporada as temporada,
                a.tipo,
                a.aplicar,
                MAX(ld_v.codigo) as codigo,
                COUNT(p.bloque) as camas, round(sum(p.plantas)/960,1) as ncamas,
                $expr as fecha_aplica
                FROM plane AS p
                INNER JOIN arrangements as a ON a.variedad=p.variedad and a.finca=p.finca
                INNER JOIN varieties as v ON v.nombre=p.variedad
                LEFT JOIN ld_variedades as ld_v ON ld_v.nombre=p.variedad
                INNER JOIN seasons as s ON s.nombre=p.temporada
                LEFT JOIN (SELECT variedad, temporada_obj, pico FROM program WHERE estado=1 GROUP BY variedad, temporada_obj, pico) as pr
                    ON pr.variedad=p.variedad and pr.temporada_obj=s.nombre
                left join arrangement as aa on a.tipo=aa.tipo and a.aplicar=aa.aplicar
                $where
                GROUP BY p.finca, p.bloque, p.variedad, s.cod_temporada, a.tipo, a.aplicar, $expr
                ORDER BY p.bloque, a.aplicar, p.variedad, s.cod_temporada";
        $result = $conexion->query($sql);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) $data[] = $row;
        }
        return $data;
    }

    public static function getSupplies(string $where): array {
        $conexion = Database::getConnection();
        $expr = self::fechaAplicacionExpr();
        $sql = "SELECT 
                    t.finca,
                    t.aplicacion,
                    t.insumo,
                    t.medida,
                    t.dosis,
                    ROUND(SUM(t.ncamas), 0) as ncamas,
                    ROUND(SUM(t.cantidad_bloque_app), 2) as cantidad
                FROM (
                    SELECT 
                        p.finca,
                        p.bloque,
                        a.aplicar as aplicacion,
                        su.insumo,
                        su.medida,
                        su.dosis,
                        ROUND(SUM(p.plantas)/960, 0) as ncamas,
                        ROUND(ROUND(SUM(p.plantas)/960, 0) * su.dosis, 2) as cantidad_bloque_app
                    FROM plane AS p
                    INNER JOIN arrangements as a ON a.variedad=p.variedad and a.finca=p.finca
                    INNER JOIN varieties AS v ON v.nombre=p.variedad
                    INNER JOIN seasons AS s ON s.nombre=p.temporada
                    LEFT JOIN (SELECT variedad,temporada_obj,pico FROM program WHERE estado=1 GROUP BY variedad, temporada_obj, pico) AS pr
                        ON pr.variedad=p.variedad and pr.temporada_obj=s.nombre
                    INNER JOIN arrangement as aa ON a.aplicar=aa.aplicar and a.tipo=aa.tipo
                    INNER JOIN supplies as su ON su.arrangement_id=aa.id AND (su.finca=p.finca OR (COALESCE(su.finca,'')='' AND NOT EXISTS (SELECT 1 FROM supplies s2 WHERE s2.arrangement_id=aa.id AND s2.finca=p.finca)))
                    $where
                    GROUP BY p.finca, p.bloque, a.aplicar, su.insumo, su.medida, su.dosis
                ) as t
                GROUP BY t.finca, t.aplicacion, t.insumo, t.medida, t.dosis
                ORDER BY t.finca, t.aplicacion, t.insumo";
        $result = $conexion->query($sql);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) $data[] = $row;
        }
        return $data;
    }

    public static function getTipos(): array {
        $conexion = Database::getConnection();
        $result = $conexion->query("SELECT DISTINCT tipo FROM arrangements");
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) $data[] = $row->tipo;
        }
        return $data;
    }

    public static function getMaterialsByBlock(string $where): array {
        $conexion = Database::getConnection();
        $expr = self::fechaAplicacionExpr();
        $sql = "SELECT 
                    t.finca,
                    t.bloque,
                    t.insumo,
                    t.medida,
                    MAX(t.codigo) as codigo,
                    GROUP_CONCAT(DISTINCT t.dosis SEPARATOR ' / ') as dosis,
                    t.justificacion,
                    ROUND(SUM(t.ncamas), 0) as ncamas,
                    ROUND(SUM(t.cantidad_bloque_app), 2) as cantidad,
                    MIN(t.fecha_aplica) as fecha_aplica
                FROM (
                    SELECT 
                        p.finca,
                        p.bloque,
                        a.aplicar as aplicacion,
                        su.insumo,
                        su.medida,
                        su.dosis,
                        su.codigo,
                        a.tipo as justificacion,
                        ROUND(SUM(p.plantas)/960, 0) as ncamas,
                        ROUND(ROUND(SUM(p.plantas)/960, 0) * su.dosis, 2) as cantidad_bloque_app,
                        MIN($expr) as fecha_aplica
                    FROM plane AS p
                    INNER JOIN arrangements as a ON a.variedad=p.variedad and a.finca=p.finca
                    INNER JOIN varieties AS v ON v.nombre=p.variedad
                    INNER JOIN seasons AS s ON s.nombre=p.temporada
                    LEFT JOIN (SELECT variedad,temporada_obj,pico FROM program WHERE estado=1 GROUP BY variedad, temporada_obj, pico) AS pr
                        ON pr.variedad=p.variedad and pr.temporada_obj=s.nombre
                    INNER JOIN arrangement as aa ON a.aplicar=aa.aplicar and a.tipo=aa.tipo
                    INNER JOIN supplies as su ON su.arrangement_id=aa.id AND (su.finca=p.finca OR (COALESCE(su.finca,'')='' AND NOT EXISTS (SELECT 1 FROM supplies s2 WHERE s2.arrangement_id=aa.id AND s2.finca=p.finca)))
                    $where
                    GROUP BY p.finca, p.bloque, a.aplicar, su.insumo, su.medida, su.dosis, su.codigo, a.tipo
                ) as t
                GROUP BY t.finca, t.bloque, t.insumo, t.medida, t.justificacion
                ORDER BY t.insumo, CAST(t.bloque AS UNSIGNED), t.bloque";
        $result = $conexion->query($sql);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) $data[] = $row;
        }
        return $data;
    }

    public static function getFincas(): array {
        $conexion = Database::getConnection();
        $result = $conexion->query("SELECT DISTINCT finca FROM plane");
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) $data[] = $row->finca;
        }
        return $data;
    }
}
