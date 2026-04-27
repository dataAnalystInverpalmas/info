<?php
include_once('funciones/conexion.php');

$c = new \App\Controllers\CatalogCrudController();
$c->show('supplies', 'supplies', 'CRUD Supplies');
?>