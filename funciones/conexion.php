<?php
if (defined('CONEXION_LOADED')) return;
define('CONEXION_LOADED', true);

//variables — usar variables de entorno si están definidas, si no usar valores por defecto
$host = getenv('DB_HOST') ?: 'db';
$username = getenv('DB_USER') ?: 'inverpalmas';
$password = getenv('DB_PASS') ?: 'Inver2020!';
$database = getenv('DB_NAME') ?: 'informes';
$port = getenv('DB_PORT') ?: 3306;

// Create connection (mysqli)
$conexion = new mysqli($host, $username, $password, $database, (int)$port);
$conexion->set_charset("utf8");

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
// APP_SRC puede definirse en .env; si no, se mantiene el valor histórico
$GLOBALS['src'] = getenv('APP_SRC') ?: 'http://172.10.18.128:9258';

// Composer autoloader (Carbon, PhpSpreadsheet, mPDF, etc.)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Cargador de las nuevas clases refactorizadas (Models, Controllers, etc.)
require_once __DIR__ . '/../src/autoload.php';

