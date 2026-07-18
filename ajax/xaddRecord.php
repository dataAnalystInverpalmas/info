<?php
//lamar conexion
include ('../funciones/conexion.php');

if(isset($_POST))
{
        $finca = $_POST['xfinca'];
        $valor = $_POST['xvalor'];

        $queryIdF = "SELECT id FROM farms WHERE nombre='$finca' ";
        $resIDF = $conexion->query($queryIdF);
        $idFinca = $resIDF->fetch_row();
        $idF = $idFinca['0']; 

$query = "INSERT INTO  locations
        (ubicacion,finca_id)
        VALUES 
        ('$valor',$idF)";

$result = $conexion->query($query);

}

?>
