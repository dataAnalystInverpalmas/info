<?php
//lamar conexion
include ('../funciones/conexion.php');
use Carbon\Carbon;
// check request
if(isset($_POST))
{
    // get values
    $id = $_POST['id'];
	$fecha = $_POST['update_fecha'];
	$finca = $_POST['update_finca'];
    $producto = $_POST['update_producto'];
    $tipo = $_POST['update_tipo'];
    $indicador = $_POST['update_indicador'];
    $revision = $_POST['update_revision'];
    $valor = $_POST['update_valor'];
    // Updaste User details
    $query = "UPDATE indicator_transactions SET valor=$valor  
            WHERE id = $id ";
    if (!$result = mysqli_query($conexion, $query)) {
        exit(mysqli_error($conexion));
    }
}