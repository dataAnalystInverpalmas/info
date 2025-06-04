<?php
//lamar conexion
include ('../funciones/conexion.php');
//require "../vendor/autoload.php";

// Design initial table header 
$data = '<table id="tabla_consumos" class="table">
<thead><tr>
    <th>Fecha_Hora_del_Registro</th>
    <th>Finca</th>
    <th>Producción</th>
    <th>Ubicación</th>
    <th>Grupo</th>
    <th>Item</th>
    <th>Valor</th>
    <th>Editar</th>
    <th>Eliminar</th>
</tr></thead>';

$query = "SELECT a.id,a.fecha,b.ubicacion as indicador,c.revision as revision,c.tipo as tipo,a.valor FROM indicator_transactions as a 
            LEFT JOIN locations as b ON a.ubicacion_id=b.id LEFT JOIN indicators as c ON a.indicador_id=c.id WHERE a.estado=1";

if (!$result = mysqli_query($conexion, $query)) {
exit(mysqli_error($conexion));
}

// if query results contains rows then featch those rows 
if(mysqli_num_rows($result) > 0)
{
while($row = mysqli_fetch_assoc($result))
{
    $data .= '<tbody><tr>
    <td>'.$row['fecha'].'</td>
    <td>'.$row['finca'].'</td>
    <td>'.$row['producto'].'</td>
    <td>'.$row['indicador'].'</td>
    <td>'.$row['revision'].'</td>
    <td>'.$row['tipo'].'</td>
    <td>'.$row['valor'].'</td>
    <td>
    <button onclick="GetUserDetails('.$row['id'].')" class="btn btn-warning"><i class="far fa-edit"></i></button>
    </td>
    <td>
    <button onclick="DeleteUser('.$row['id'].')" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
    </td>
    </tr>';
    }
    }
else
{
// records now found 
    $data .= '<tr><td colspan="9">No hay registros!</td></tr>';
}

    $data .= '</tbody><tfoot> </tfoot></table>';

echo $data;

?>

