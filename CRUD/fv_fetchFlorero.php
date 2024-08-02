<?php
//lamar conexion
include ('../funciones/conexion.php');

 $sql=$conexion->query("select b.nombre,a.fecha_florero from flower_vases as a INNER join varieties as b on a.variedad_id=b.id order by a.id desc limit 1");
 ?>
 <?php
 while($row=mysqli_fetch_array($sql))
 {     
  echo "Está registrando datos del florero de: <br> Variedad: ".$row['nombre']."<br> Fecha de Florero: ".$row['fecha_florero'];
 }

?>