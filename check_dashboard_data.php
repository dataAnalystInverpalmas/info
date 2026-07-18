<?php
require_once __DIR__ . '/funciones/conexion.php';

$data = \App\Models\DashboardProyecciones::getDashboardData([
    'fecha_desde' => '2026-03-30',
    'fecha_hasta' => '2026-05-24',
]);

echo "ok: " . (($data['ok'] ?? false) ? 'true' : 'false') . "\n";
echo "Flor cards (RE/PT/AJ):\n";
print_r($data['florCards'] ?? []);

echo "\nDistribucion color labels:\n";
print_r($data['distribucionColor']['labels'] ?? []);
