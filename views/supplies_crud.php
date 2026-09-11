<?php
include_once('funciones/conexion.php');

$c = new \App\Controllers\CatalogCrudController();
$c->show('supplies', 'supplies', 'CRUD Supplies', '{"arrangement_id":"../ajax/fetch_arrangements.php","finca":"../ajax/fetchFarms.php"}', '{"arrangement_id":"arrangement_name"}', '{"arrangement_name":true,"finca":"../ajax/fetchFarms.php","insumo":true}');
?>