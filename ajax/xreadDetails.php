<?php
//lamar conexion
include ('../funciones/conexion.php');

// check request
if(isset($_POST['id']) && isset($_POST['id']) != "")
{
    // get User ID
    $id = $_POST['id'];

    // Get User Details
    $query = "SELECT farms.nombre as finca, locations.ubicacion FROM locations
                LEFT JOIN farms
                on farms.id=locations.finca_id 
                WHERE locations.id = '$id' ";

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