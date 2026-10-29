<?php
declare(strict_types=1);

define('API_DIR', dirname(__DIR__));
define('APP_DIR', dirname(__DIR__, 2));

require_once API_DIR . '/Response.php';
require_once API_DIR . '/Auth.php';
require_once APP_DIR . '/funciones/conexion.php';

// CORS — Headers estándar para consumo desde aplicaciones modernas (Frontend, PowerBI, Databricks, AI Agents)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if (!isset($conexion) || $conexion->connect_errno) {
    Response::error('Database connection failed', 503);
}

// Verificación de autenticación vía Bearer Token
Auth::verify();

// Normalizar nombre de endpoint: solo letras minúsculas, números y guiones bajos
$endpoint = isset($_GET['endpoint'])
    ? preg_replace('/[^a-z0-9_]/', '', strtolower((string)$_GET['endpoint']))
    : '';

if ($endpoint === '') {
    $availableFiles = glob(API_DIR . '/endpoints/*.php') ?: [];
    $available = array_map(static fn($f) => basename($f, '.php'), $availableFiles);
    sort($available);
    Response::error('No endpoint specified. Available: ' . implode(', ', $available), 400);
}

$endpointFile = API_DIR . '/endpoints/' . $endpoint . '.php';

if (!file_exists($endpointFile)) {
    $availableFiles = glob(API_DIR . '/endpoints/*.php') ?: [];
    $available = array_map(static fn($f) => basename($f, '.php'), $availableFiles);
    sort($available);
    Response::error(
        "Endpoint '{$endpoint}' not found. Available: " . implode(', ', $available),
        404
    );
}

require_once $endpointFile;
