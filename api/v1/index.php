<?php
declare(strict_types=1);

define('API_DIR', dirname(__DIR__));
define('APP_DIR', dirname(__DIR__, 2));

require_once API_DIR . '/Response.php';
require_once API_DIR . '/Auth.php';
require_once APP_DIR . '/funciones/conexion.php';

// CORS — ajustar origen si se requiere restringir
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    Response::error('Method Not Allowed', 405);
}

if (!isset($conexion) || $conexion->connect_errno) {
    Response::error('Database connection failed', 503);
}

Auth::verify();

// Normalizar endpoint: solo letras minúsculas y guiones bajos
$endpoint = isset($_GET['endpoint'])
    ? preg_replace('/[^a-z_]/', '', strtolower((string)$_GET['endpoint']))
    : '';

$allowed = ['plano', 'variedades', 'colores', 'curvas_rosas', 'proyecciones', 'planos_proyeccion', 'lonas', 'arreglos', 'dates'];

if (!in_array($endpoint, $allowed, true)) {
    Response::error(
        'Endpoint not found. Available: ' . implode(', ', $allowed),
        404
    );
}

require_once API_DIR . '/endpoints/' . $endpoint . '.php';
