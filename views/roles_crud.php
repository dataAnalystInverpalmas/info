<?php
include_once('funciones/conexion.php');
\App\Helpers\Auth::requireAdmin();

$selects = '{"users_role":"../ajax/fetch_roles_catalog.php"}';
$filters = '{"users_role":"../ajax/fetch_roles_catalog.php","dir":true}';

$c = new \App\Controllers\CatalogCrudController();
$c->show('roles', 'roles', 'Permisos y Rutas por Rol', $selects, '', $filters);
?>