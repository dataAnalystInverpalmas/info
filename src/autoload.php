<?php
/**
 * Autoloader PSR-4 básico para la estructura refactorizada MVC.
 * Se evita depender de un `composer dump-autoload` en el servidor global.
 */
spl_autoload_register(function ($class) {
    // Definimos el prefijo principal
    $prefix = 'App\\';
    // Directorio base para el prefijo (carpeta src)
    $base_dir = __DIR__ . '/';
    
    // Si la clase no usa el prefijo, pasamos al siguiente autoloader
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    // Obtenemos el nombre de la clase sin el prefijo
    $relative_class = substr($class, $len);
    
    // Reemplazamos los separadores de namespace por separadores de directorios
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    // Si el archivo existe, lo requerimos
    if (file_exists($file)) {
        require $file;
    }
});
