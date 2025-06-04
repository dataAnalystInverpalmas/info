<?php
ob_start();

if(!isset($_SESSION)) 
{      
    error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
    session_start(); 
} 

//variables
$host='db';
$username='inverpalmas';
$password='Inver2020!';
$database='informes';
$port = 3306;
// Create connection
$conexion = new mysqli("db", "inverpalmas", "Inver2020!", "informes", 3306);
$conexion->set_charset("utf8");

$ip=$_SERVER['REMOTE_ADDR'];
if (substr($ip,0,9)=='localhost'){
    $_GLOBALS['src'] = 'http://172.10.18.128:9258';
}else{
    $_GLOBALS['src'] = 'http://172.10.18.128:9258';
}

?>
