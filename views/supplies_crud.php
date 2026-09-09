<?php
include_once('funciones/conexion.php');

$c = new \App\Controllers\CatalogCrudController();
$c->show('supplies', 'supplies', 'CRUD Supplies', '{"arrangement_id":"../ajax/fetch_arrangements.php"}', '{"arrangement_id":"arrangement_name"}');
?>