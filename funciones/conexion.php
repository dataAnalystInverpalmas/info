<?php
//variables
$host='172.10.18.220';
$username='inverpalmas';
$password='Inver2020!';
$database='informes';
// Create connection
$conexion = new mysqli("127.0.0.1", "inverpalmas", "Inver2020!", "informes");
$conexion->set_charset("utf8");
//sesion

if(!isset($_SESSION)) 
    { 
        session_start(); 
    } 
//variable del directorio

$ip=$_SERVER['REMOTE_ADDR'];
if (substr($ip,0,9)=='172.10.18'){
    $_GLOBALS['src'] = 'http://172.10.18.220:4444';
}else{
    $_GLOBALS['src'] = 'http://190.60.223.98:9856';
}

?>
