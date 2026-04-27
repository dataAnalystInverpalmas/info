<?php
/**
 * Migración v2: Expandir módulo de gestión
 *
 * Ejecutar desde la app o phpMyAdmin cuando esté conectado al servidor DB.
 * Ejemplo: http://172.10.18.128:9258/scripts/migrate_gestion_v2.php
 *
 * Cambios:
 * - proyectos.categoria  (para agrupar: Tika, Estadística, etc.)
 * - tareas.tipo           (prevista / imprevista)
 * - tareas.responsable    (quién ejecuta la tarea)
 * - tareas.fecha_inicio   (cuándo empezó)
 * - bitacora.proyecto_id  (referencia directa al proyecto)
 */
include(__DIR__ . '/../funciones/conexion.php');

$queries = [
    "ALTER TABLE proyectos ADD COLUMN categoria VARCHAR(100) DEFAULT NULL AFTER nombre",
    "ALTER TABLE tareas ADD COLUMN tipo ENUM('prevista','imprevista') DEFAULT 'prevista' AFTER nombre",
    "ALTER TABLE tareas ADD COLUMN responsable VARCHAR(150) DEFAULT NULL AFTER fecha_vencimiento",
    "ALTER TABLE tareas ADD COLUMN fecha_inicio DATE DEFAULT NULL AFTER fecha_vencimiento",
    "ALTER TABLE bitacora ADD COLUMN proyecto_id INT DEFAULT NULL AFTER tarea_id",
];

echo "<pre>\n";
foreach ($queries as $sql) {
    if ($conexion->query($sql)) {
        echo "OK: $sql\n";
    } else {
        if (strpos($conexion->error, 'Duplicate column') !== false) {
            echo "SKIP (ya existe): $sql\n";
        } else {
            echo "ERROR: " . $conexion->error . " -> $sql\n";
        }
    }
}
echo "\nMigración v2 completada.\n</pre>";
