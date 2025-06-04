<?php
//lamar conexion
include ('funciones/conexion.php');
//funciones personalizadas
$file = new SplFileObject('archivos/Ubicaciones.txt');

while (!$file->eof())
{
  $line = $file->fgets();
  list($finca,$nfinca,$ubica)=explode(';',$line);
  echo $finca."<br>";
}
?>