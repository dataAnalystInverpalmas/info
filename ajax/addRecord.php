<?php
//lamar conexion
include ('../funciones/conexion.php');

if(isset($_POST['fecha']))
{
        $fecha = $_POST['fecha'];
        $finca = $_POST['finca'];
        $producto = $_POST['producto'];
        $tipo = $_POST['tipo'];
        $indicador = $_POST['indicador'];
        $revision = $_POST['revision'];
        $valor = $_POST['valor'];

$queryId = "SELECT id FROM locations WHERE finca='$finca' AND producto='$producto' and ubicacion='$tipo' ";
$resID = $conexion->query($queryId);
$idLocation = $resID->fetch_row();
$idL = $idLocation['0'];

$queryIdR = "SELECT id FROM indicators WHERE tipo='$indicador' AND revision='$revision' ";
$resIdR = $conexion->query($queryIdR);
$idRevision = $resIdR->fetch_row();
$idR = $idRevision['0'];

$user_id=$_SESSION['id'];

$query = "INSERT INTO  indicator_transactions (fecha,ubicacion_id,indicador_id,user_id,valor,estado)
        VALUES ('$fecha','$idL','$idR','$user_id',$valor,1) ";
$result = $conexion->query($query);

}

?>
