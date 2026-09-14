<?php
if (defined('CONEXION_LOADED')) return;
define('CONEXION_LOADED', true);

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

//variables — usar variables de entorno si están definidas, si no usar valores por defecto
$host = getenv('DB_HOST') ?: '172.10.18.128';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: 'AdmSys2014';
$database = getenv('DB_NAME') ?: 'informes';
$port = getenv('DB_PORT') ?: 3306;

// Create connection (mysqli)
$conexion = new mysqli($host, $username, $password, $database, (int)$port);
$conexion->set_charset("utf8");

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
// APP_SRC permite forzar la URL base desde .env; si no está definida se calcula
// dinámicamente según el host con el que se accedió (evita que enlaces internos
// como "Salir" apunten a la IP privada cuando se entra por la IP pública).
if ($appSrc = getenv('APP_SRC')) {
    $GLOBALS['src'] = $appSrc;
} elseif (!empty($_SERVER['HTTP_HOST'])) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $GLOBALS['src'] = $scheme . '://' . $_SERVER['HTTP_HOST'];
} else {
    $GLOBALS['src'] = 'http://172.10.18.128:9258';
}

// Composer autoloader (Carbon, PhpSpreadsheet, mPDF, etc.)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Cargador de las nuevas clases refactorizadas (Models, Controllers, etc.)
require_once __DIR__ . '/../src/autoload.php';

