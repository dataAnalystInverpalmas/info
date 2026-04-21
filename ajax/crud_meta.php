<?php
require_once __DIR__ . '/_crud_dynamic_common.php';
include('../funciones/conexion.php');
header('Content-Type: application/json; charset=utf-8');

$table = trim($_GET['table'] ?? '');
$allowed = ['breeders', 'users', 'roles', 'supplies', 'varieties', 'seasons'];
if (!in_array($table, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Tabla no permitida']);
    exit;
}

list($meta, $err) = crud_get_table_meta($conexion, $table);
if ($err) {
    echo json_encode(['success' => false, 'message' => $err]);
    exit;
}

$editable = crud_editable_columns($meta);
$columns = [];
foreach ($meta['columns'] as $name => $info) {
    $columns[] = [
        'name' => $name,
        'type' => $info['data_type'],
        'nullable' => $info['nullable'],
        'is_pk' => $info['is_pk'],
        'editable' => in_array($name, $editable, true),
    ];
}

echo json_encode([
    'success' => true,
    'table' => $table,
    'pk' => $meta['pk'],
    'columns' => $columns,
]);
