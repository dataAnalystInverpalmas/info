<?php
//lamar conexion
include ('../funciones/conexion.php');

// Design initial table header 
$data = '<table class="table">
<tr>
    <th>Tipo</th>
    <th>Revisión</th>
    <th>Medida</th>
    <th>Editar</th>
    <th>Eliminar</th>
</tr>';

$query = "SELECT * FROM indicators";

if (!$result = mysqli_query($conexion, $query)) {
exit(mysqli_error($conexion));
}

// if query results contains rows then featch those rows 
if(mysqli_num_rows($result) > 0)
{
while($row = mysqli_fetch_assoc($result))
{
    $data .= '<tr>
    <td>'.$row['tipo'].'</td>
    <td>'.$row['revision'].'</td>
    <td>'.$row['medida'].'</td>
    <td>
    <button onclick="yGetUserDetails('.$row['id'].')" class="btn btn-warning"><i class="fas fa-edit"></i></button>
    </td>
    <td>
    <button onclick="yDeleteUser('.$row['id'].')" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
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