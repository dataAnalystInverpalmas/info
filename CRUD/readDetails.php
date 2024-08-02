<?php
//lamar conexion
include ('../funciones/conexion.php');

// check request
if(isset($_POST['id']) && isset($_POST['id']) != "")
{
    // get User ID
    $id = $_POST['id'];

    // Get User Details
    $query = "SELECT a.fecha, b.finca as finca, b.producto as producto,b.ubicacion as indicador,c.revision as revision,c.tipo as tipo,a.valor FROM indicator_transactions as a 
    LEFT JOIN locations as b ON a.ubicacion_id=b.id LEFT JOIN indicators as c ON a.indicador_id=c.id WHERE a.id = '$id'";
    if (!$result = mysqli_query($conexion, $query)) {
        exit(mysqli_error($conexion));
    }
    $response = array();
    if(mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $response = $row;
        }
    }
    else
    {
        $response['status'] = 200;
        $response['message'] = "Data not found!";
    }
    // display JSON data
    echo json_encode($response);
}
else
{
    $response['status'] = 200;
    $response['message'] = "Invalid Request!";
}