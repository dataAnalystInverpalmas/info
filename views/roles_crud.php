<?php
include_once('funciones/conexion.php');

$c = new \App\Controllers\CatalogCrudController();
$c->show('roles', 'roles', 'CRUD Roles');
?>