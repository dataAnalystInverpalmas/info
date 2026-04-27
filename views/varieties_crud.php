<?php
include_once('funciones/conexion.php');

$c = new \App\Controllers\CatalogCrudController();
$c->show('varieties', 'varieties', 'CRUD Varieties', '{"producto":"../CRUD/fv_fetchProducts.php","casa_comercial":"../CRUD/fv_fetchBreeders.php"}');
?>