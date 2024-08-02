<?php
//lamar conexion
include ('../funciones/conexion.php');
$sql=$conexion->query("select distinct nombre from fv_origens order by nombre asc");

 ?>
    <option value="">Origen</option>
 <?php
 while($row=mysqli_fetch_array($sql))
 {
    echo "<option>".$row['nombre']."</option>";
 }

?>