<?php
//lamar conexion
include ('../funciones/conexion.php');

 if(isset($_POST['get_option']))
 {
  $item = $_POST['get_option'];
  $find=$conexion->query("select puntaje from fv_evaluation_items where nombre='$item'");
  while($row=mysqli_fetch_array($find))
  {
   echo "<option>".$row['puntaje']."</option>";
  }
  exit;
 }else
 {
    $sql=$conexion->query("select distinct nombre from fv_evaluation_items");
    ?>
   <option value="">Tipo</option>
    <?php
    while($row=mysqli_fetch_array($sql))
    {
     echo "<option>".$row['nombre']."</option>";
    }
 }

?>