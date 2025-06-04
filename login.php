<?php
//lamar conexion
if (is_file("funciones/conexion.php")){
  include ("funciones/conexion.php");
}else{
  include ("../funciones/conexion.php");
}
  //comprobar si se logea en formulario

if (isset($_POST['login'])){
//CONSULTA
$usuario=$_POST['email'];
$password=$_POST['password'];

$sql="SELECT id,name,password,email,role FROM users WHERE email='".$usuario."' and password='".$password."' ";

$query=$conexion->query($sql);
$rows=$query->num_rows;

$row=mysqli_fetch_assoc($query);

if($rows>0){
  //iniciali  variables de session_start
  $_SESSION['usuario']=$row['name'];
  $_SESSION['role']=$row['role'];
  $_SESSION['id']=$row['id'];
  header("location: home.php");
}else{
?>
  <script>
    alert("Error en Login");
  </script>
<?php
header("location: index.php");
}

}

 ?>
