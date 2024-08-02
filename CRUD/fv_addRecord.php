<?php
use Carbon\Carbon;
//lamar conexion
if (is_file("funciones/conexion.php")){
        include ("funciones/conexion.php");
      }else{
        include ("../funciones/conexion.php");
}

if(isset($_POST))
{
  $fecha_corte = $_POST['fecha_corte'];
  $fecha = $_POST['fecha'];
  (int)$grupo = $_POST['grupo'];
  $user_id=$_SESSION['id'];
  
  $sqlG = "SELECT grupo,count(*) as cuenta FROM `flower_vases` WHERE grupo=(select max(grupo) from flower_vases) group by 1";
  $resG = $conexion->query($sqlG);
  $group = $resG->fetch_row();

  //comprobar los grupos
  if($grupo==1){    
      $agrupa = $group['0'];
  }elseif($grupo==0){
      $agrupa = $group['0'];
      $agrupa++;
  }else{
      $agrupa = 1;
  }

  //variables para guardar resultados que se van al insert    
  $grupo_descripcion = strtolower($_POST['gdescripcion']);
  $tipo = $_POST['tipo'];
  $producto = $_POST['producto'];
  $variedad = $_POST['variedad'];
  $origen = $_POST['origen'];
  $tallos = $_POST['tallos'];
  $pmax = $_POST['pmax'];
  $pcorte = $_POST['pcorte'];
  $pempaque = $_POST['pempaque'];
  $pflorero = $_POST['pflorero'];
  $guarde = $_POST['guarde'];
  $simulacion = $_POST['simulacion'];
  $comentario = $_POST['comentario'];

  if ($pcorte<1){
    $pcorte=1;
  }
  if ($pempaque<1){
    $pempaque=1;
  }
  if($guarde<1){
   $guarde=0;
  }

  $queryIdV = "SELECT id FROM varieties WHERE nombre='$variedad' and producto='$producto' ";
        $resIDV = $conexion->query($queryIdV);
        $idvari = $resIDV->fetch_row();
        $idV = $idvari['0'];
  
  $queryIdK = "SELECT id FROM fv_kinds WHERE nombre='$tipo' ";
        $resIDK= $conexion->query($queryIdK);
        $idKind = $resIDK->fetch_row();
        $idK = $idKind['0'];

  $queryIdO = "SELECT id FROM fv_origens WHERE nombre='$origen' ";
        $resIDO= $conexion->query($queryIdO);
        $idOrigen = $resIDO->fetch_row();
        $idO = $idOrigen['0'];

  $sql = "
        INSERT INTO flower_vases (fecha_corte,fecha_florero,grupo,grupo_descripcion,tipo_id,variedad_id,origen_id,tallos,pmax,pcorte,pempaque,pflorero,guarde_granel,simulacion,observacion,user_id)
        VALUES ('$fecha_corte','$fecha',$agrupa,'$grupo_descripcion',$idK,$idV,$idO,$tallos,$pmax,$pcorte,$pempaque,$pflorero,$guarde,$simulacion,'$comentario',$user_id)
  ";      

  $result = $conexion->query($sql);
  
  if ($grupo==1){

    $sqlID = "SELECT id,grupo from flower_vases where user_id=$user_id order by id desc limit 1,1";
    $resID = $conexion->query($sqlID);
    $id_ant = $resID->fetch_row();
    $idUPT = $id_ant['0'];
    $grupoID = $id_ant['1'];

    /* $sqlDesc = "SELECT grupo_descripcion FROM flower_vases where user_id=$user_id ORDER BY id DESC LIMIT 1";
    $resDesc = $conexion->query($sqlDesc);
    $desc = $resDesc->fetch_row();
    $descripcion = $desc['0']; */

    $sqlDesc = "SELECT GROUP_CONCAT(v.nombre) as descripcion FROM `flower_vases` as fv
      LEFT JOIN varieties AS v ON v.id=fv.variedad_id
      WHERE user_id=$user_id  and fv.grupo='$grupoID' ";
    $resDesc = $conexion->query($sqlDesc);
    $desc = $resDesc->fetch_row();
    $descripcion = $desc['0'];
    
    $update = "update flower_vases set grupo_descripcion='$descripcion'  where grupo=$grupoID ";
    $resUpdate = $conexion->query($update);
  }

}else{
  echo "Algo está mal";
}