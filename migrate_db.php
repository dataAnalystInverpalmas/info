<?php
/**
 * Script de migración — ejecutar UNA sola vez desde el navegador
 * Solo accesible para administradores (role 1)
 */
include('funciones/conexion.php');

if (empty($_SESSION['usuario']) || intval($_SESSION['role']) !== 1) {
    http_response_code(403);
    die('Acceso denegado. Debe iniciar sesión como administrador.');
}

header('Content-Type: text/plain; charset=utf-8');

$migraciones = [
    // proyectos
    "ALTER TABLE proyectos ADD COLUMN usuario_id INT NULL DEFAULT NULL AFTER id"
        => "SHOW COLUMNS FROM proyectos LIKE 'usuario_id'",
    // tareas
    "ALTER TABLE tareas ADD COLUMN usuario_id INT NULL DEFAULT NULL AFTER id"
        => "SHOW COLUMNS FROM tareas LIKE 'usuario_id'",
    "ALTER TABLE tareas ADD COLUMN quien_solicita VARCHAR(150) NULL DEFAULT NULL AFTER responsable"
        => "SHOW COLUMNS FROM tareas LIKE 'quien_solicita'",
    // bitacora
    "ALTER TABLE bitacora ADD COLUMN usuario_id INT NULL DEFAULT NULL AFTER id"
        => "SHOW COLUMNS FROM bitacora LIKE 'usuario_id'",
];

foreach ($migraciones as $alter => $check) {
    $r = $conexion->query($check);
    if ($r && $r->num_rows > 0) {
        echo "SKIP: columna ya existe  [{$check}]\n";
    } else {
        $ok = $conexion->query($alter);
        echo $ok
            ? "OK:   {$alter}\n"
            : "ERROR: {$alter} — {$conexion->error}\n";
    }
}

echo "\nMigración completada.\n";
echo "IMPORTANTE: Elimine este archivo (migrate_db.php) del servidor.\n";
