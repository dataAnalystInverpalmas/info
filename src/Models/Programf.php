<?php
namespace App\Models;

use App\Helpers\Database;

class Programf {

    public static function getProgramas(): array {
        $conexion = Database::getConnection();
        $result = $conexion->query("SELECT DISTINCT programa FROM programf ORDER BY programa DESC");
        $data = [];
        if ($result) {
            while ($r = $result->fetch_object()) { $data[] = $r->programa; }
            $result->free();
        }
        return $data;
    }

    public static function getVariedades(): array {
        $conexion = Database::getConnection();
        $result = $conexion->query("SELECT DISTINCT variedad FROM programf WHERE variedad IS NOT NULL AND variedad <> '' ORDER BY variedad");
        $data = [];
        if ($result) {
            while ($r = $result->fetch_object()) { $data[] = $r->variedad; }
            $result->free();
        }
        return $data;
    }

    public static function getTemporadas(): array {
        $conexion = Database::getConnection();
        $result = $conexion->query("SELECT DISTINCT temporada_obj FROM programf WHERE temporada_obj IS NOT NULL AND temporada_obj <> '' ORDER BY temporada_obj");
        $data = [];
        if ($result) {
            while ($r = $result->fetch_object()) { $data[] = $r->temporada_obj; }
            $result->free();
        }
        return $data;
    }

    public static function getProductos(): array {
        $conexion = Database::getConnection();
        $result = $conexion->query("SELECT DISTINCT producto FROM programf WHERE producto IS NOT NULL AND producto <> '' ORDER BY producto");
        $data = [];
        if ($result) {
            while ($r = $result->fetch_object()) { $data[] = $r->producto; }
            $result->free();
        }
        return $data;
    }

    public static function getFincas(): array {
        $conexion = Database::getConnection();
        $result = $conexion->query("SELECT DISTINCT finca FROM programf WHERE finca IS NOT NULL AND finca <> '' ORDER BY finca");
        $data = [];
        if ($result) {
            while ($r = $result->fetch_object()) { $data[] = $r->finca; }
            $result->free();
        }
        return $data;
    }

    public static function getBloques(): array {
        $conexion = Database::getConnection();
        $result = $conexion->query("SELECT DISTINCT bloque FROM programf WHERE bloque IS NOT NULL ORDER BY bloque");
        $data = [];
        if ($result) {
            while ($r = $result->fetch_object()) { $data[] = $r->bloque; }
            $result->free();
        }
        return $data;
    }
}
