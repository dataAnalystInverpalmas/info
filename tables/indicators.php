<?php
//lamar conexion
include ('funciones/conexion.php');
//funciones personalizadas
$file = new SplFileObject('archivos/Conceptos.txt');

while (!$file->eof())
{
  $line = $file->fgets();
  list($tipo,$medida,$revisa)=explode(',',$line);
  
  $sql = "INSERT INTO indicators (tipo,revision,medida) 
                      VALUES ('$tipo','$revisa','$medida')";
  $conexion->query($sql);
}

?>