<?php
include_once('funciones/conexion.php');
$c = new \App\Controllers\PowerBIController();
$c->show('Plano de siembras', 'https://app.powerbi.com/view?r=eyJrIjoiOGI4YWM0OWEtNzliYS00YmEyLWI5YzAtNjhmMmJlZTU4MTdmIiwidCI6ImIzMTI4MDM5LTFkN2ItNGE0Ny1hYjA2LTE1MmU3MWMzYTg1NyIsImMiOjR9');
