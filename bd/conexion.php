<?php 
class Conexion{	  
    public static function Conectar() {
        // Leer configuración desde variables de entorno si están definidas, si no usar los valores históricos
        $host = getenv('DB_HOST') ?: '172.10.18.128';
        $dbname = getenv('DB_NAME') ?: 'informes';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: 'AdmSys2014';
        $port = getenv('DB_PORT') ?: '3306';

        $opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
        try{
            $dsn = "mysql:host={$host};port={$port};dbname={$dbname}";
            $conexion = new PDO($dsn, $user, $pass, $opciones);
            return $conexion;
        }catch (Exception $e){
            die("El error de Conexión es: ". $e->getMessage());
        }
    }
}
