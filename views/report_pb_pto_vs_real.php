<?php
include_once('funciones/conexion.php');
$c = new \App\Controllers\PowerBIController();
$c->show('Plano de siembras', 'https://app.powerbi.com/view?r=eyJrIjoiYjAwOTg0M2YtYmRmZC00ZGZlLTg3MmQtYzNlOTA1NGI5NTEwIiwidCI6IjFmZTU2MjBkLWEwYTktNDU0Yy1iZTdhLTViOWY1MDA4OTQ0NCJ9');
