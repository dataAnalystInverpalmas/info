<?php
if (defined('CONEXION_LOADED')) return;
define('CONEXION_LOADED', true);

if (!function_exists('cargarVariablesEntorno')) {
    function cargarVariablesEntorno($envPath) {
        if (!file_exists($envPath)) {
            return;
        }
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || (isset($line[0]) && $line[0] === '#')) {
                continue;
            }
            if (strpos($line, '=') !== false) {
                list($key, $val) = explode('=', $line, 2);
                $key = trim($key);
                $val = trim($val);
                $len = strlen($val);
                if ($len >= 2) {
                    $first = $val[0];
                    $last = $val[$len - 1];
                    if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                        $val = substr($val, 1, -1);
                    }
                }
                // Si se ejecuta dentro del contenedor Docker y DB_HOST es localhost o 127.0.0.1, redirigir a 'db'
                if ($key === 'DB_HOST' && file_exists('/.dockerenv') && in_array($val, ['127.0.0.1', 'localhost'], true)) {
                    $val = 'db';
                }
                putenv("{$key}={$val}");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }
}

// Cargar .env si está disponible en la raíz del proyecto
cargarVariablesEntorno(dirname(__DIR__) . '/.env');

// Configuración de entorno y depuración (Producción vs Desarrollo)
$appEnv = getenv('APP_ENV') ?: 'production';
if ($appEnv === 'production') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('log_errors', '1');
}

// Configuración de seguridad en sesiones PHP
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

// Cabeceras HTTP estándar de seguridad
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Variables de base de datos leídas de entorno (con fallback a la base de datos de producción)
$host     = getenv('DB_HOST') ?: '172.10.18.128';
$username = getenv('DB_USER') ?: 'root';
$password = (getenv('DB_PASS') !== false && getenv('DB_PASS') !== '') ? getenv('DB_PASS') : 'AdmSys2014';
$database = getenv('DB_NAME') ?: 'informes';
$port     = (int)(getenv('DB_PORT') ?: 3306);

// Create connection (mysqli)
$conexion = @new mysqli($host, $username, $password, $database, $port);
if ($conexion->connect_errno) {
    error_log("Fallo de conexión a la base de datos (MySQLi): " . $conexion->connect_error);
    if ($appEnv === 'development') {
        die("Error de conexión a la base de datos: " . $conexion->connect_error);
    } else {
        die("No se pudo conectar con la base de datos del sistema. Por favor contacte al administrador.");
    }
}
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
    $GLOBALS['src'] = 'http://localhost:9258';
}

// Composer autoloader (Carbon, PhpSpreadsheet, mPDF, etc.)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Cargador de las nuevas clases refactorizadas (Models, Controllers, etc.)
require_once __DIR__ . '/../src/autoload.php';
