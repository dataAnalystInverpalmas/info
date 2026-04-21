<?php
namespace App\Models;

use App\Helpers\Database;

class Program {

    public static function getProgramas() {
        $conexion = Database::getConnection();
        $result = $conexion->query(
            "SELECT DISTINCT programa FROM informes.program ORDER BY programa DESC"
        );
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) { $data[] = $row->programa; }
            $result->free();
        }
        return $data;
    }

    public static function getVariedades() {
        $conexion = Database::getConnection();
        $result = $conexion->query(
            "SELECT DISTINCT variedad FROM informes.program WHERE variedad IS NOT NULL AND variedad <> '' ORDER BY variedad"
        );
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) { $data[] = $row->variedad; }
            $result->free();
        }
        return $data;
    }

    public static function getTemporadas() {
        $conexion = Database::getConnection();
        $result = $conexion->query(
            "SELECT DISTINCT temporada_obj FROM informes.program WHERE temporada_obj IS NOT NULL AND temporada_obj <> '' ORDER BY temporada_obj"
        );
        $data = [];
        if ($result) {
            while ($row = $result->fetch_object()) { $data[] = $row->temporada_obj; }
            $result->free();
        }
        return $data;
    }
}
