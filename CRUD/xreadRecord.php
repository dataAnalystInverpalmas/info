<?php
//lamar conexion
include ('../funciones/conexion.php');

// Design initial table header 
$data = '<table class="table">
<tr>
    <th>Finca</th>
    <th>Ubicación</th>
    <th>Editar</th>
    <th>Eliminar</th>
</tr>';

$query = "SELECT locations.id,farms.nombre as finca, locations.ubicacion FROM locations
LEFT JOIN farms
on farms.id=locations.finca_id";

if (!$result = mysqli_query($conexion, $query)) {
exit(mysqli_error($conexion));
}

// if query results contains rows then featch those rows 
if(mysqli_num_rows($result) > 0)
{
while($row = mysqli_fetch_assoc($result))
{
    $data .= '<tr>
    <td>'.$row['finca'].'</td>
    <td>'.$row['ubicacion'].'</td>
    <td>
    <button onclick="xGetUserDetails('.$row['id'].')" class="btn btn-warning"><i class="fas fa-edit"></i></button>
    </td>
    <td>
    <button onclick="xDeleteUser('.$row['id'].')" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
    </td>
    </tr>';
    }
    }
else
{
// records now found 
    $data .= '<tr><td colspan="9">No hay registros!</td></tr>';
}

    $data .= '</table>';

echo $data;

?>