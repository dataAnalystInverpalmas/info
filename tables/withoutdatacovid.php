<?php
//lamar conexion
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;

$sql="SELECT a.fecha as fecha, a.codigo as codigo FROM assistances as a
LEFT JOIN datacovid as b
ON a.codigo=b.codigo AND date(a.fecha)=date(b.fecha)
WHERE b.codigo IS NULL
";

$result = $conexion->query($sql);

while ($row=$result->fetch_object())
{
    $date=new Carbon($row->fecha);
    $date->format('Ymd');
    $id=$row->codigo.$date;
    $fecha=$row->fecha;
    $codigo=$row->codigo;

    $slqi="INSERT IGNORE INTO unregistered_covid (id,fecha,codigo)
    VALUES ('$id','$fecha','$codigo') ";
    $res=$conexion->query($slqi);
    if (!$res){
        die ("Query failed ". $conexion->error.$sqli);
    }
}
header("Location: home.php?menu=tables&report=1004");
