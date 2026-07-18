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
        $it = "'".$_POST['item']."'";
        (int)$val = "'".$_POST['valor']."'";

        $sqlI = "SELECT id FROM fv_evaluation_items WHERE nombre={$it} and puntaje={$val}";
        $resID = $conexion->query($sqlI);
        $idItem = $resID->fetch_row();
        $item = $idItem['0'];

        $sqlF = "SELECT max(id) as id FROM flower_vases where user_id=$user_id";
        $resIDF= $conexion->query($sqlF);
        $idFlorero = $resIDF->fetch_row();
        $id = $idFlorero['0'];

        

        $sql = "INSERT INTO fv_evaluations (fv_id,evaluation_item_id) VALUES ($id,$item)";
        $conexion->query($sql);
}
