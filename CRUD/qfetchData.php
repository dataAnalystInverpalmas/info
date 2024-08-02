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
    $find=$conexion->query("select nombre from varieties where producto='$state' and estado=1 order by nombre asc");
        while($row=mysqli_fetch_array($find))
        {
            echo "<option>".$row['nombre']."</option>";
        }
    exit;
}
?>