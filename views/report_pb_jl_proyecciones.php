<?php
include_once('funciones/conexion.php');
$c = new \App\Controllers\PowerBIController();
$c->show('Proy JL', 'https://app.powerbi.com/view?r=eyJrIjoiMzFkYjgyMzUtZjY2YS00NmY2LWE5MmYtMWZmOGJhY2VkYjNkIiwidCI6ImIzMTI4MDM5LTFkN2ItNGE0Ny1hYjA2LTE1MmU3MWMzYTg1NyIsImMiOjR9');