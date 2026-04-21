<?php
/**
 * Archivo de configuración del módulo Proyectos-Tareas-Bitácora
 * 
 * Variables de entorno disponibles (opcionales):
 * DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT
 * 
 * Si no están definidas, se usan los valores por defecto de:
 * - funciones/conexion.php (mysqli)
 * - bd/conexion.php (PDO)
 */

// Zona horaria
date_default_timezone_set(getenv('TIMEZONE') ?: 'America/Mexico_City');

// Configuración de debug
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');

// Ruta de logs
define('LOG_PATH', dirname(__DIR__) . '/logs');

// Crear carpeta de logs si no existe
if (!is_dir(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}

/**
 * Función para registrar en logs
 */
function log_evento($evento, $tipo = 'info') {
    $fecha = date('Y-m-d H:i:s');
    $linea = "[$fecha] [$tipo] $evento\n";
    file_put_contents(LOG_PATH . '/sistema.log', $linea, FILE_APPEND);
    
    if (APP_DEBUG) {
        error_log($linea);
    }
}

/**
 * Función para responder JSON con error
 */
function responder_error($mensaje, $codigo = 400) {
    http_response_code($codigo);
    echo json_encode(['error' => $mensaje]);
    exit;
}

/**
 * Función para responder JSON exitoso
 */
function responder_exito($datos) {
    http_response_code(200);
    echo json_encode($datos);
    exit;
}

/**
 * Función para validar entrada
 */
function validar_entrada($datos, $campos_requeridos = []) {
    $errores = [];
    
    foreach ($campos_requeridos as $campo) {
        if (empty($datos[$campo]) && $datos[$campo] !== 0 && $datos[$campo] !== false) {
            $errores[] = "El campo '$campo' es requerido";
        }
    }
    
    return $errores;
}

/**
 * Función para sanitizar entrada
 */
function sanitizar($entrada) {
    if (is_array($entrada)) {
        return array_map('sanitizar', $entrada);
    }
    
    return htmlspecialchars(trim($entrada), ENT_QUOTES, 'UTF-8');
}

// Log de inicio de sesión
log_evento('Aplicación iniciada', 'info');
