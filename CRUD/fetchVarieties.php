<?php
//lamar conexion
include ('../funciones/conexion.php');

if(isset($_POST['get_option']))
{
 $producto = $_POST['get_option'];
 $find=$conexion->query("select nombre from varieties where producto='$producto' and estado=1 ORDER BY NOMBRE ASC");
 while($row=mysqli_fetch_array($find))
 {
  echo "<option>".$row['nombre']."</option>";
 }
 exit;
}
?>