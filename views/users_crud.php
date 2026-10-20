<?php
include_once('funciones/conexion.php');
\App\Helpers\Auth::requireAdmin();

$selects = '{"role":"../ajax/fetch_roles_catalog.php","activo":"../ajax/fetch_users_activo.php"}';
$filters = '{"role":"../ajax/fetch_roles_catalog.php","activo":"../ajax/fetch_users_activo.php","name":true,"email":true}';

$c = new \App\Controllers\CatalogCrudController();
$c->show('users', 'users', 'Gestión de Usuarios', $selects, '', $filters);
?>