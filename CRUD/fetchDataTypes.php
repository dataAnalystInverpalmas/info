<?php
//lamar conexion
include ('../funciones/conexion.php');

 $sql=$conexion->query("select distinct name from data_types");
 ?>
<option value="">Tipo</option>
 <?php
 while($row=mysqli_fetch_array($sql))
 {
  echo "<option>".$row['name']."</option>";
 }

?>