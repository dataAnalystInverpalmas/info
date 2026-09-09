<?php
include_once('funciones/conexion.php');
$c = new \App\Controllers\PowerBIController();
$c->show('Causas Nacional', 'https://app.powerbi.com/view?r=eyJrIjoiOWIyYmM0ZmMtMDEwMi00Y2ZiLTgxYWMtMGJmN2JlOGI0NzQwIiwidCI6ImIzMTI4MDM5LTFkN2ItNGE0Ny1hYjA2LTE1MmU3MWMzYTg1NyIsImMiOjR9');
