<?php
//lamar conexion
include ('../funciones/conexion.php');
$sql=$conexion->query("select distinct nombre from evaluators");

 ?>
    <option value="">Evaluador</option>
 <?php
 while($row=mysqli_fetch_array($sql))
 {
    echo "<option>".$row['nombre']."</option>";
 }

?>