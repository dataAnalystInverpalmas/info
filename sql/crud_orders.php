<?php
include_once '../bd/conexion.php';
if (is_file("funciones/conexion.php")){
    include ("funciones/conexion.php");
  }else{
    include ("../funciones/conexion.php");
}

$objeto = new Conexion();
$conexion = $objeto->Conectar();
//recoger variables de formulario general
$fecha = (isset($_POST['fecha'])) ? $_POST['fecha'] : '';
$casa = (isset($_POST['casa'])) ? $_POST['casa'] : '';
$pref_documento = (isset($_POST['pref_documento'])) ? $_POST['pref_documento'] : '';
$n_documento = (isset($_POST['n_documento'])) ? $_POST['n_documento'] : '';
$tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : '';
$fecha_ini = (isset($_POST['fecha_ini'])) ? $_POST['fecha_ini'] : '';
$fecha_fin = (isset($_POST['fecha_fin'])) ? $_POST['fecha_fin'] : '';
$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$id = (isset($_POST['id'])) ? $_POST['id'] : '';
//variables para recoger detalles
$order_id = (isset($_POST['order_id'])) ? $_POST['order_id'] : '';
$origen_id = (isset($_POST['origen_id'])) ? $_POST['origen_id'] : '';
$propagacion_id = (isset($_POST['propagacion_id'])) ? $_POST['propagacion_id'] : '';
$variedad_id = (isset($_POST['variedad_id'])) ? $_POST['variedad_id'] : '';
$cantidad = (isset($_POST['cantidad'])) ? $_POST['cantidad'] : '';
$excedente = (isset($_POST['excedente'])) ? $_POST['excedente'] : '';
//user id
$user_id=$_SESSION['id'];
//según el tipo de transacción se asigna una tabla para trabajar
if ($tipo==0){
    $tabla = " orders " ;
    $where = " WHERE state = 1 AND date BETWEEN '$fecha_ini' AND '$fecha_fin' ";
}else{
    $tabla = " orders_details " ;
    $where = " WHERE order_id = '$id' ";
}
//se selecciona de acuedo a la opción que venga del order.js
switch($opcion){
    case 1:
        $consulta = "INSERT INTO $tabla (date, breeder_id, pref_document, num_document, user_id) VALUES('$fecha', '$casa', '$pref_documento', $n_documento, $user_id) ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 
        echo $consulta;
        $consulta = "SELECT *  FROM $tabla ORDER BY id DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);    
        break;    
    case 2:
        if ((isset($pref_documento) and isset($n_documento)) and empty($fecha_cosecha_r)){        
            $consulta = "UPDATE $tabla SET pref_document='$pref_documento', num_document='$n_documento' WHERE id='$id' ";		
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();        
        }else if (isset($pref_documento) and isset($n_documento) and isset($fecha_cosecha_r) and isset($esquejes_cosecha) ){
            $consulta = "UPDATE $tabla SET pref_document='$pref_documento', num_document='$n_documento' WHERE id='$id' ";		
            $resultado = $conexion->prepare($consulta);
            $resultado->execute();
        }

        $consulta = "SELECT * FROM $tabla WHERE id='$id' ";       
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3:        
        $consulta = "UPDATE $tabla SET state=0 WHERE id='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;
    case 4:    
        $consulta = "SELECT id,date as fecha,breeder_id as casa,pref_document as pref_documento,num_document as num_documento FROM $tabla $where ";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 5:    
        $consulta = "SELECT id,order_id FROM $tabla $where ";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();        
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 6:        
        $consulta = "DELETE FROM $tabla WHERE id='$id' ";		
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();                           
        break;
    case 7:
        $consulta = "INSERT INTO $tabla (order_id,origen_id,kind_id,variety_id,quantity,plus_quantity) VALUES($order_id,$origen_id,$propagacion_id,$variedad_id,$cantidad,$excedente) ";			
        $resultado = $conexion->prepare($consulta);
        $resultado->execute(); 
        echo $consulta;
        $consulta = "SELECT *  FROM $tabla ORDER BY id DESC LIMIT 1";
        $resultado = $conexion->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);    
        break;  
}

print json_encode($data, JSON_UNESCAPED_UNICODE);//envio el array final el formato json a AJAX
$conexion=null;