<?php
//lamar conexion
include ('../funciones/conexion.php');

if(isset($_POST['get_option']))
{
 $item = $_POST['get_option'];
 $find=$conexion->query("select aplicar from arrangement where tipo='$item' ORDER BY aplicar ASC");
 
 while($row=mysqli_fetch_array($find))
 {
  echo "<option>".$row['aplicar']."</option>";
 }
 exit;
}
?>