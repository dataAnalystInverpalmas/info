<?php
//lamar conexion
if (is_file("funciones/conexion.php")){
    include ("funciones/conexion.php");
  }else{
    include ("../funciones/conexion.php");
}

if(isset($_POST['get_option']))
{
    $state = $_POST['get_option'];
    $find=$conexion->query("SELECT a.grado FROM grades as a INNER JOIN products AS b ON a.producto_id=b.id where b.nombre='$state' ");
    echo "<option>Grado</option>";
    while($row=mysqli_fetch_array($find))
        {
            echo "<option>".$row['grado']."</option>";
        }
    exit;
}
?>