<?php
include_once '../bd/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

$variedad = (isset($_POST['variedad'])) ? $_POST['variedad'] : '';
$temporada_obj = (isset($_POST['temporada_obj'])) ? $_POST['temporada_obj'] : '';
$fecha_ensarte_r = (isset($_POST['fecha_ensarte_r'])) ? $_POST['fecha_ensarte_r'] : '';
$esquejes_ensarte = (isset($_POST['esquejes_ensarte'])) ? $_POST['esquejes_ensarte'] : '';
$fecha_cosecha_r = (isset($_POST['fecha_cosecha_r'])) ? $_POST['fecha_cosecha_r'] : '';
$esquejes_cosecha = (isset($_POST['esquejes_cosecha'])) ? $_POST['esquejes_cosecha'] : '';
$banco = "";

$tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : '';
if ($tipo==0){
    $tabla = " program " ;
}else{
    $tabla = " program_add_pto " ;
}

$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$id = (isset($_POST['id'])) ? $_POST['id'] : '';
$fecha_ini = (isset($_POST['fecha_ini'])) ? $_POST['fecha_ini'] : '';
$fecha_fin = (isset($_POST['fecha_fin'])) ? $_POST['fecha_fin'] : '';
$where = " WHERE estado = 1 AND fecha_ensarte BETWEEN '$fecha_ini' AND '$fecha_fin' ";

switch($opcion){
    case 1:
        $consulta = "INSERT INTO $tabla (variedad, temporada_obj, fecha_ensarte, fecha_ensarte_r, esquejes_ensarte) VALUES('$variedad', '$temporada_obj', '$fecha_ensarte_r', '$fecha_ensarte_r', $esquejes_ensarte) ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 
        
        $consulta = "SELECT *  FROM $tabla ORDER BY id DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);    
        break;    
    case 2:
        if ((isset($fecha_ensarte_r) and isset($esquejes_ensarte)) and empty($fecha_cosecha_r)){        
            $consulta = "UPDATE $tabla SET fecha_ensarte_r='$fecha_ensarte_r', esquejes_ensarte='$esquejes_ensarte' WHERE id='$id' ";		
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();        
        }else if (isset($fecha_ensarte_r) and isset($esquejes_ensarte) and isset($fecha_cosecha_r) and isset($esquejes_cosecha) ){
            $consulta = "UPDATE $tabla SET fecha_ensarte_r='$fecha_ensarte_r', esquejes_ensarte='$esquejes_ensarte', fecha_cosecha_r='$fecha_cosecha_r', esquejes_cosecha='$esquejes_cosecha' WHERE id='$id' ";		
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
        }

        $consulta = "SELECT * FROM $tabla WHERE id='$id' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3:        
        $consulta = "UPDATE $tabla SET estado=0 WHERE id='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;
    case 4:    
        $consulta = "SELECT id,variedad,temporada_obj,fecha_ensarte_r,esquejes_ensarte,fecha_cosecha_r,esquejes_cosecha FROM $tabla $where ";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
}

print json_encode($data, JSON_UNESCAPED_UNICODE);//envio el array final el formato json a AJAX
$conexion=null;