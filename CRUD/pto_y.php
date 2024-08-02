<?php
//lamar conexion
include ('../funciones/conexion.php');

 $sql=$conexion->query("SELECT programa FROM program GROUP BY programa");
 ?>
<option value="">Programa</option>
 <?php
 while($row=mysqli_fetch_array($sql))
 {
  echo "<option>".$row['programa']."</option>";
 }

?>