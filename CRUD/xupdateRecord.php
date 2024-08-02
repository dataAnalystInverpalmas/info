<?php
//lamar conexion
include ('../funciones/conexion.php');
use Carbon\Carbon;
// check request
if(isset($_POST))
{
    // get values
    $id = $_POST['id'];
	$finca = $_POST['xupdate_finca'];
    $valor = $_POST['xupdate_valor'];

    $queryIdF = "SELECT id FROM farms WHERE nombre='$finca' ";
    $resIDF = $conexion->query($queryIdF);
    $idFinca = $resIDF->fetch_row();
    $idF = $idFinca['0']; 

    // Updaste User details
    $query = "UPDATE locations SET id='$id', 
            finca_id = $idF, ubicacion='$valor'  
            WHERE id = '$id' ";
    if (!$result = mysqli_query($conexion, $query)) {
        exit(mysqli_error($conexion));
    }
}