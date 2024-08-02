<?php 
class Conexion{	  
    public static function Conectar() {        
        define('servidor', '172.10.18.220');
        define('nombre_bd', 'informes');
        define('usuario', 'inverpalmas');
        define('password', 'Inver2020!');	
        define('port', '3306');				        
        $opciones = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');			
        try{
            $conexion = new PDO("mysql:host=".servidor."; dbname=".nombre_bd, usuario, password, $opciones);			
            return $conexion;
        }catch (Exception $e){
            die("El error de Conexión es: ". $e->getMessage());
        }
    }
}
