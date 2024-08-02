<?php
//lamar conexion
include ('../funciones/conexion.php');

 $sql=$conexion->query("SELECT DISTINCT tipo FROM `budget` WHERE 1");
 ?>
<option value="">Programa</option>
 <?php
 while($row=mysqli_fetch_array($sql))
 {
  echo "<option>".$row['tipo']."</option>";
 }

?>