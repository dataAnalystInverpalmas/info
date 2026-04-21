<?php
namespace App\Helpers;

/**
 * Clase auxiliar para recuperar la conexión global de la base de datos
 */
class Database {
    public static function getConnection() {
        global $conexion;
        if (!isset($conexion)) {
            // Si por alguna razón no está seteada, la llamamos
            include_once(__DIR__ . '/../../funciones/conexion.php');
        }
        return $conexion;
    }
}
