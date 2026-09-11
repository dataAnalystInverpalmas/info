<?php
include_once('funciones/conexion.php');

$c = new \App\Controllers\CatalogCrudController();
$c->show('greenhouses', 'greenhouses', 'CRUD Greenhouses', '{"finca_id":"../ajax/fetchFarmsId.php"}', '{"finca_id":"finca_name"}', '{"finca_id":"../ajax/fetchFarmsId.php","bloque":true,"tabla":true,"nave":true}', 'longitud');
?>