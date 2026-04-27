<?php
namespace App\Models;

use App\Helpers\Database;

class Application {

    public static function fechaAplicacionExpr(): string {
        $fechaSiembraJueves = "DATE_ADD(p.fecha_siembra, INTERVAL (3 - WEEKDAY(p.fecha_siembra)) DAY)";
        $fechaBase = "IF(p.tipo_siembra IN ('REEMPLAZO', 'ADICIONAL'), DATE_ADD($fechaSiembraJueves, INTERVAL + COALESCE(pr.pico, v.ciclo) WEEK), s.fecha_pico)";
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
                ld_v.codigo,
                COUNT(p.bloque) as camas, round(sum(p.plantas)/960,1) as ncamas,
                $expr as fecha_aplica
                FROM plane AS p
                INNER JOIN arrangements as a ON a.variedad=p.variedad and a.finca=p.finca
                INNER JOIN varieties as v ON v.nombre=p.variedad
                LEFT JOIN ld_variedades as ld_v ON ld_v.nombre=p.variedad
                INNER JOIN seasons as s ON s.nombre=p.temporada
                LEFT JOIN (SELECT variedad,temporada_obj,pico FROM program group by 1,2,3) as pr
                    ON pr.variedad=p.variedad and pr.temporada_obj=s.nombre
                left join arrangement as aa on a.tipo=aa.tipo and a.aplicar=aa.aplicar
                $where
                GROUP BY 1,2,3,4,5,6";
        $result = $conexion->query($sql);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) $data[] = $row;
        }
        return $data;
    }

    public static function getByBlock(string $where): array {
        $conexion = Database::getConnection();
        $expr = self::fechaAplicacionExpr();
        $sql = "SELECT p.finca,
                p.bloque,
                a.tipo,
                a.aplicar,
                COUNT(p.bloque) as camas, round(sum(p.plantas)/960,1) as ncamas,
                $expr as fecha_aplica
                FROM plane AS p
                INNER JOIN arrangements as a ON a.variedad=p.variedad and a.finca=p.finca
                INNER JOIN varieties as v ON v.nombre=p.variedad
                INNER JOIN seasons as s ON s.nombre=p.temporada
                LEFT JOIN (SELECT variedad,temporada_obj,pico FROM program group by 1,2,3) as pr
                    ON pr.variedad=p.variedad and pr.temporada_obj=s.nombre
                left join arrangement as aa on a.tipo=aa.tipo and a.aplicar=aa.aplicar
                $where
                GROUP BY p.finca,p.bloque,a.tipo,a.aplicar";
        $result = $conexion->query($sql);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) $data[] = $row;
        }
        return $data;
    }

    public static function getSummary(string $where): array {
        $conexion = Database::getConnection();
        $sql = "SELECT IFNULL(a.aplicar,'Total') as aplicar,
                COUNT(p.bloque) as camas, round(sum(p.plantas)/960,1) as ncamas
                FROM plane AS p
                INNER JOIN arrangements as a ON a.variedad=p.variedad and a.finca=p.finca
                $where
                GROUP BY a.aplicar WITH ROLLUP";
        $result = $conexion->query($sql);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) $data[] = $row;
        }
        return $data;
    }

    public static function getSupplies(string $where): array {
        $conexion = Database::getConnection();
        $sql = "SELECT a.aplicar as aplicacion, s.insumo, s.medida,
                round(sum(p.plantas)/960,1) * s.dosis as cantidad
                FROM plane AS p
                INNER JOIN arrangements as a ON a.variedad=p.variedad and a.finca=p.finca
                INNER JOIN arrangement as aa ON a.aplicar=aa.aplicar and a.tipo=aa.tipo
                INNER JOIN supplies as s ON s.arrangement_id=aa.id
                $where
                GROUP BY a.aplicar,s.insumo";
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
