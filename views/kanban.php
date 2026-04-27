<?php
include_once ('funciones/conexion.php');

$controller = new \App\Controllers\KanbanController();
$controller->index();
