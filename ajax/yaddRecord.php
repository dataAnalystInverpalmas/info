<?php
//lamar conexion
include ('../funciones/conexion.php');

if(isset($_POST))
{
        $tipo = $_POST['ytipo'];
        $revision= $_POST['yrevision'];
        $medida = $_POST['ymedida'];

$query = "INSERT INTO  indicators
        (tipo,revision,medida)
        VALUES 
        ('$tipo','$revision','$medida')
        ";
$result = $conexion->query($query);

}

?>
