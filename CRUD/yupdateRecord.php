<?php
//lamar conexion
include ('../funciones/conexion.php');
use Carbon\Carbon;
// check request
if(isset($_POST))
{
    // get values
    $id = $_POST['id'];
	$tipo = $_POST['yupdate_tipo'];
    $revision = $_POST['yupdate_revision'];
    $medida = $_POST['yupdate_medida'];
    // Updaste User details
    $query = "UPDATE indicators SET id='$id', 
            tipo = '$tipo', revision = '$revision', medida='$medida'  
            WHERE id = $id ";
    if (!$result = mysqli_query($conexion, $query)) {
        exit(mysqli_error($conexion));
    }
}