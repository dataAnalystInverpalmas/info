<?php
include_once('funciones/conexion.php');
$c = new \App\Controllers\PowerBIController();
$c->show('Compara Producción', 'https://app.powerbi.com/view?r=eyJrIjoiZTMyNjNhNzYtZTBkOC00NjVmLWE1MjQtOTE0NzJlN2NkNDQ1IiwidCI6IjFmZTU2MjBkLWEwYTktNDU0Yy1iZTdhLTViOWY1MDA4OTQ0NCJ9');