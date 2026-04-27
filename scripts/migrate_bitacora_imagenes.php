<?php
/**
 * Migración: bitácora con trazabilidad + tabla tarea_imagenes
 * Ejecutar una sola vez desde CLI o navegador (eliminar después).
 */
include(__DIR__ . '/../funciones/conexion.php');

// Agregar columna solo si no existe (compatible con MySQL < 8.0.3)
function addColumnIfNotExists($conexion, $tabla, $columna, $definicion) {
    $db = $conexion->real_escape_string($conexion->query("SELECT DATABASE()")->fetch_row()[0]);
    $col = $conexion->real_escape_string($columna);
    $tbl = $conexion->real_escape_string($tabla);
    $existe = $conexion->query(
        "SELECT 1 FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = '$db' AND TABLE_NAME = '$tbl' AND COLUMN_NAME = '$col' LIMIT 1"
    )->num_rows > 0;
    if (!$existe) {
        if (!$conexion->query("ALTER TABLE `$tbl` ADD COLUMN `$col` $definicion")) {
            return $conexion->error;
        }
    }
    return null;
}

$errores = [];

// Columnas en bitácora para snapshot y diff
foreach ([
    ['descripcion_antes', 'TEXT NULL AFTER descripcion'],
    ['cambios_json',      'TEXT NULL AFTER descripcion_antes'],
] as [$col, $def]) {
    $err = addColumnIfNotExists($conexion, 'bitacora', $col, $def);
    if ($err) $errores[] = "bitacora.$col: $err";
}

$sqls = [
    // Tabla de imágenes vinculadas a tareas
    "CREATE TABLE IF NOT EXISTS tarea_imagenes (
       id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
       tarea_id        INT UNSIGNED NOT NULL,
       ruta_relativa   VARCHAR(500) NOT NULL,
       nombre_original VARCHAR(255) NOT NULL,
       mime            VARCHAR(100) NOT NULL,
       size_bytes      INT UNSIGNED NOT NULL DEFAULT 0,
       hash_sha256     CHAR(64) NULL,
       subido_por      VARCHAR(100) NOT NULL DEFAULT 'Sistema',
       created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
       estado          ENUM('activo','eliminado') NOT NULL DEFAULT 'activo',
       INDEX idx_tarea (tarea_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
];

foreach ($sqls as $sql) {
    if (!$conexion->query($sql)) {
        $errores[] = $conexion->error . ' -- SQL: ' . substr($sql, 0, 80);
    }
}

if (empty($errores)) {
    echo "✅ Migración completada sin errores.\n";
} else {
    echo "❌ Errores:\n" . implode("\n", $errores) . "\n";
}
