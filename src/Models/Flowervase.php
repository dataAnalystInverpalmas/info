<?php
namespace App\Models;

use App\Helpers\Database;

class Flowervase {

    public static function getRecentRecords(int $days = 7): array {
        $conexion = Database::getConnection();
        $stmt = $conexion->prepare(
            "SELECT fv.id, fv.registro, fv.fecha_florero, v.nombre AS variedad,
                    fv.grupo_descripcion, k.nombre AS tipo
             FROM flower_vases AS fv
             LEFT JOIN varieties AS v ON v.id = fv.variedad_id
             LEFT JOIN fv_kinds AS k ON k.id = fv.tipo_id
             WHERE fv.registro >= DATE_SUB(NOW(), INTERVAL ? DAY)
             ORDER BY fv.id DESC"
        );
        $stmt->bind_param("i", $days);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = [];
        while ($row = $result->fetch_object()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }
}
