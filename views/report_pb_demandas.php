<?php
include_once('funciones/conexion.php');
$c = new \App\Controllers\PowerBIController();
$c->show('Demandas', 'https://app.powerbi.com/view?r=eyJrIjoiNjE2YTZkOGMtNjJhNC00ZTVmLWI4MDQtMmIxOGY0MTA1N2RhIiwidCI6ImIzMTI4MDM5LTFkN2ItNGE0Ny1hYjA2LTE1MmU3MWMzYTg1NyIsImMiOjR9');