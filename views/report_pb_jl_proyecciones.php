<?php
include_once('funciones/conexion.php');
$c = new \App\Controllers\PowerBIController();
$c->show('Proy JL', 'https://app.powerbi.com/view?r=eyJrIjoiNmEwYmJlNDYtZWI1YS00N2M1LTk4NDUtN2Y2YWFjYzNkYmMyIiwidCI6IjFmZTU2MjBkLWEwYTktNDU0Yy1iZTdhLTViOWY1MDA4OTQ0NCJ9');