<?php
//lamar conexion
if (is_file("funciones/conexion.php")){
    include ("funciones/conexion.php");
  }else{
    include ("../funciones/conexion.php");
}

require "../vendor/autoload.php";
use Carbon\Carbon;

$actual=Carbon::now('-5:00');
$fechacarbon=new Carbon($actual);
$fecha=$fechacarbon->subDay(7)->format('Y-m-d 00:00:00');

$query = "SELECT a.id,a.registro,b.nombre,a.fecha_florero,d.nombre,a.grupo_descripcion
            FROM flower_vases as a 
            LEFT JOIN varieties as d ON a.variedad_id=d.id
            LEFT JOIN fv_kinds as b on b.id=a.tipo_id
            WHERE registro>='$fecha'
            ORDER BY a.id desc
            ";

// Design initial table header 
$data = '<table id="tabla_floreros" class="table">
<thead><tr>
    <th>id</th>
    <th>fecha registro</th>
    <th>fecha florero</th>
    <th>variedad</th>
    <th>grupo</th>
    <th>eliminar</th>
</tr></thead>';

if (!$result = mysqli_query($conexion, $query)) {
exit(mysqli_error($conexion));
}

// if query results contains rows then featch those rows 
if(mysqli_num_rows($result) > 0)
{
    while($row = mysqli_fetch_assoc($result)){
        $data .= '<tbody><tr>
        <td>'.$row['id'].'</td>
        <td>'.$row['registro'].'</td>
        <td>'.$row['fecha_florero'].'</td>
        <td>'.$row['nombre'].'</td>
        <td>'.$row['grupo_descripcion'].'</td>
        <td>
            <button onclick="DeleteFV('.$row['id'].')" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
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