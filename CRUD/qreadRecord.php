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
$fecha=$fechacarbon->subDay(7)->format('Y-m-d');

$query = "SELECT a.id,b.fuente,a.fecha,a.finca,d.nombre,e.grado,a.valor
            FROM table_qualities as a 
            LEFT JOIN kind_qualities as b ON a.fuente=b.id 
            LEFT JOIN farms as c ON a.variedad=c.id
            LEFT JOIN varieties as d ON a.variedad=d.id
            LEFT JOIN grades as e ON a.grado=e.id
            WHERE fecha>='$fecha'
            ORDER BY a.id desc
            ";

// Design initial table header 
$data = '<table id="tabla_clasificacion" class="table">
<thead><tr>
    <th>Fuente</th>
    <th>Fecha Registro</th>
    <th>Finca</th>
    <th>Variedad</th>
    <th>Grado</th>
    <th>Valor</th>
    <th>Eliminar</th>
</tr></thead>';

if (!$result = mysqli_query($conexion, $query)) {
exit(mysqli_error($conexion));
}

// if query results contains rows then featch those rows 
if(mysqli_num_rows($result) > 0)
{
    while($row = mysqli_fetch_assoc($result)){
        $data .= '<tbody><tr>
        <td>'.$row['fuente'].'</td>
        <td>'.$row['fecha'].'</td>
        <td>'.$row['finca'].'</td>
        <td>'.$row['nombre'].'</td>
        <td>'.$row['grado'].'</td>
        <td>'.$row['valor'].'</td>
        <td>
        <button onclick="DeleteQualities('.$row['id'].')" class="btn btn-danger"><i class="far fa-trash-alt"></i></button>
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
