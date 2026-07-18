<?php
include_once('funciones/conexion.php');

$c = new \App\Controllers\CatalogCrudController();
$c->show('varieties', 'varieties', 'CRUD Varieties', '{"producto":"../ajax/fv_fetchProducts.php","casa_comercial":"../ajax/fv_fetchBreeders.php"}');
?>