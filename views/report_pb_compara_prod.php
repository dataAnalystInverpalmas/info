<?php
include_once('funciones/conexion.php');
$c = new \App\Controllers\PowerBIController();
$c->show('Compara Producción', 'https://app.powerbi.com/view?r=eyJrIjoiZDE0MDE5OTItZWMyMC00YTYzLTk3ZTktMDY5MzNkMTNlMTU2IiwidCI6ImIzMTI4MDM5LTFkN2ItNGE0Ny1hYjA2LTE1MmU3MWMzYTg1NyIsImMiOjR9');