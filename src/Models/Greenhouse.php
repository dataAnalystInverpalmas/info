<?php
namespace App\Models;

use App\Helpers\Database;

class Greenhouse {
    /**
     * Obtiene el resumen de invernaderos (camas y plantas) agrupado.
     * @return array
     */
    public static function getSummary() {
        $conexion = Database::getConnection();
        
        $query = "SELECT finca_id as finca, bloque, tabla, nave, cama, 
                         sum(longitud*ancho)/18 as camas_real, 
                         sum(longitud*ancho)*53.3333 as nplantas 
                  FROM greenhouses 
                  WHERE longitud > 0 
                  GROUP BY finca_ID, bloque, tabla, nave, cama";
                  
        $result = $conexion->query($query);
        $data = [];
        
        if ($result) {
            while ($row = $result->fetch_object()) {
                $data[] = $row;
            }
        }
        
        return $data;
    }
}
