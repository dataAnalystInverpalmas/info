<?php
// CRUD de catálogos del módulo de floreros (un solo archivo, se resuelve por ?report=).
include_once('funciones/conexion.php');

$catalogos = [
    'crud_fv_kinds'       => ['fv_kinds',            'CRUD Tipos de Florero'],
    'crud_fv_origens'     => ['fv_origens',          'CRUD Orígenes de Florero'],
    'crud_fv_causes'      => ['fv_causes_items',     'CRUD Causas de Florero'],
    'crud_fv_evaluations' => ['fv_evaluation_items', 'CRUD Ítems de Evaluación'],
];

$key = (string)($_GET['report'] ?? '');
if (!isset($catalogos[$key])) {
    include 'views/error_404.php';
    return;
}

list($tabla, $titulo) = $catalogos[$key];
$c = new \App\Controllers\CatalogCrudController();
$c->show($tabla, $tabla, $titulo);
