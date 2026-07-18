<?php
use Carbon\Carbon;
//lamar conexion
if (is_file("funciones/conexion.php")){
        include ("funciones/conexion.php");
      }else{
        include ("../funciones/conexion.php");
}

//$data = json_decode($_POST['data'],true);

if(isset($_POST))
{
        $user_id=$_SESSION['id'];
        $causa = "'".$_POST['causa']."'";
        (int)$dias = "'".$_POST['dias']."'";
        (int)$cantidad = "'".$_POST['cantidad']."'";

        $sqlC = "SELECT id FROM fv_causes_items WHERE nombre={$causa}";
        $resIC= $conexion->query($sqlC);
        $idCause = $resIC->fetch_row();
        $cause = $idCause['0'];

        $sqlF = "SELECT max(id) as id FROM flower_vases where user_id=$user_id";
        $resIDF= $conexion->query($sqlF);
        $idFlorero = $resIDF->fetch_row();
        $id = $idFlorero['0'];

        $sql = "INSERT INTO fv_causes (fv_id,causes_item_id,dias,cantidad) VALUES ($id,$cause,$dias,$cantidad)";
        $conexion->query($sql);
}
