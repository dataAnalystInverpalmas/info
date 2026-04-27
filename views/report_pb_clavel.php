<?php
include_once('funciones/conexion.php');
$c = new \App\Controllers\PowerBIController();
$c->show('Pto Vs Real', 'https://app.powerbi.com/view?r=eyJrIjoiNmIxNDliMzgtNjNkZi00NGZhLWE5YWUtZTk2ZjIxNmM4ZTBjIiwidCI6ImIzMTI4MDM5LTFkN2ItNGE0Ny1hYjA2LTE1MmU3MWMzYTg1NyIsImMiOjR9');