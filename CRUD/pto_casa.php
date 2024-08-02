<?php
//lamar conexion
include ('../funciones/conexion.php');

 $sql=$conexion->query("SELECT DISTINCT(b.nombre) as casa FROM program AS p LEFT JOIN breeders AS b ON b.id=p.casa_id WHERE b.nombre IS NOT NULL");
 ?>
<option value="">Casa</option>
 <?php
 while($row=mysqli_fetch_array($sql))
 {
  echo "<option>".$row['casa']."</option>";
 }

?>