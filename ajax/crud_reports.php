<?php
include_once '../bd/conexion.php';
if (is_file("funciones/conexion.php")){
    include ("funciones/conexion.php");
  }else{
    include ("../funciones/conexion.php");
}

$objeto = new Conexion();
$conn = $objeto->Conectar();
//recoger variables de formulario general
$fecha = (isset($_POST['fecha'])) ? $_POST['fecha'] : '';
$finca = (isset($_POST['finca'])) ? $_POST['finca'] : '';
$area = (isset($_POST['area'])) ? $_POST['area'] : '';
$flor = (isset($_POST['flor'])) ? $_POST['flor'] : '';
$tipod = (isset($_POST['tipod'])) ? $_POST['tipod'] : '';
$tipo = (isset($_POST['tipo'])) ? $_POST['tipo'] : '';
$fecha_ini = (isset($_POST['fecha_ini'])) ? $_POST['fecha_ini'] : '';
$fecha_fin = (isset($_POST['fecha_fin'])) ? $_POST['fecha_fin'] : '';
$opcion = (isset($_POST['opcion'])) ? $_POST['opcion'] : '';
$id = (isset($_POST['id'])) ? $_POST['id'] : '';

//variables para recoger detalles
$order_id = (isset($_POST['order_id'])) ? $_POST['order_id'] : '';
$variedad = (isset($_POST['variedad'])) ? $_POST['variedad'] : '';

//detalles a recoger
$color = (isset($_POST['color'])) ? $_POST['color'] : '';
$ciclo = (isset($_POST['ciclo'])) ? $_POST['ciclo'] : '';
$productividad = (isset($_POST['productividad'])) ? $_POST['productividad'] : '';
$tCabeza = (isset($_POST['tCabeza'])) ? $_POST['tCabeza'] : '';
$pFuerte = (isset($_POST['pFuerte'])) ? $_POST['pFuerte'] : '';
$fApertura = (isset($_POST['fApertura'])) ? $_POST['fApertura'] : '';
$gTallo = (isset($_POST['gTallo'])) ? $_POST['gTallo'] : '';
$longitud = (isset($_POST['longitud'])) ? $_POST['longitud'] : '';
$rFusarium = (isset($_POST['rFusarium'])) ? $_POST['rFusarium'] : '';
$sEnfermedades = (isset($_POST['sEnfermedades'])) ? $_POST['sEnfermedades'] : '';
$follaje = (isset($_POST['follaje'])) ? $_POST['follaje'] : '';
$pSpray = (isset($_POST['pSpray'])) ? $_POST['pSpray'] : '';
$sMini = (isset($_POST['sMini'])) ? $_POST['sMini'] : '';
$comentario = (isset($_POST['comentario'])) ? $_POST['comentario'] : '';

//user id
$user_id=$_SESSION['id'];
//según el tipo de transacción se asigna una tabla para trabajar
if ($tipo==0){
    //$evaluador_id = 3;

    $tabla = " data_reports " ;
    $where = " WHERE state = 1 AND date BETWEEN '$fecha_ini' AND '$fecha_fin' ";
}else{
    $tabla = " orders_details " ;
    $where = " WHERE order_id = '$id' ";
}

$sql = "SELECT id FROM farms WHERE nombre='$finca' LIMIT 1";
$result = $conexion->query($sql); 
$row = $result->fetch_assoc(); 
$finca_id = $row['id'];

$sql = "SELECT id FROM areas WHERE nombre='$area' LIMIT 1";
$result = $conexion->query($sql); 
$row = $result->fetch_assoc(); 
$area_id = $row['id'];

$sql = "SELECT id FROM products WHERE nombre='$flor' LIMIT 1";
$result = $conexion->query($sql); 
$row = $result->fetch_assoc(); 
$flor_id = $row['id'];

$sql = "SELECT id FROM data_types WHERE name='$tipod' LIMIT 1";
$result = $conexion->query($sql); 
$row = $result->fetch_assoc(); 
$tipo_id = $row['id'];

function exist_data($order,$variety,$item){
    $sql = "SELECT id FROM orders_details WHERE order_id=$order AND variety_id=$variety AND eval_goals_id=$item";
    $result = $conexion->query($sql); 
    $row = $result->fetch_assoc(); 
    $evaluador_id = $row['id'];

    if (empty($evaluador_id)){
        return int(0);
    }else{
        return int($evaluador_id);
    }
}

//se selecciona de acuedo a la opción que venga del order.js
switch($opcion){
    case 1:
        
        $consulta = "INSERT INTO $tabla (farm_id,flower_id,area_id,type_id,date, user_id) VALUES($finca_id,$flor_id,$area_id,$tipo_id,'$fecha', $user_id) ";			
        $resultado = $conn->prepare($consulta);
        $resultado->execute(); 
       
        $consulta = "SELECT *  FROM $tabla ORDER BY id DESC LIMIT 1";
        $resultado = $conn->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);    
        break;    
    case 2:     
        $consulta = "UPDATE $tabla SET date='$fecha', evaluator_id=$evaluador_id WHERE id='$id' ";		
        $resultado = $conn->prepare($consulta);
        $resultado->execute();        

        $consulta = "SELECT * FROM $tabla WHERE id='$id' ";       
        $resultado = $conn->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 3:        
        $consulta = "UPDATE $tabla SET state=0 WHERE id='$id' ";		
        $resultado = $conn->prepare($consulta);
        $resultado->execute();
        
        $consulta = "DELETE FROM order_comments WHERE orders_id='$id' ";		
        $resultado = $conn->prepare($consulta);
        $resultado->execute();
        break;
    case 4:
        $consulta = "SELECT dr.id,
            dr.date as fecha,
            farms.nombre as finca,
            flowers.nombre as flor,
            areas.nombre as area,
            dt.name as tipo_dato
        FROM $tabla as dr
            left join farms on dr.farm_id=farms.id
            left join areas on dr.area_id=areas.id
            left join products as flowers on dr.flower_id=flowers.id
            left join data_types as dt on dr.type_id=dt.id
            $where
        ";
        $resultado = $conn->prepare($consulta);
        $resultado->execute();        
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 5:    
        $consulta = "SELECT a.id,a.order_id,b.nombre,c.nombre as item,a.value as valor FROM $tabla AS a
                    LEFT JOIN varieties as b ON b.id=a.variety_id
                    LEFT JOIN eval_goals as c ON c.id=a.eval_goals_id
        $where ORDER BY id DESC";
        $resultado = $conn->prepare($consulta);
        $resultado->execute();        
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);
        break;
    case 6:        
        $consulta = "DELETE FROM $tabla WHERE id='$id' ";		
        $resultado = $conn->prepare($consulta);
        $resultado->execute();                           
        break;
    case 7:

        if (isset($_POST['color'])){
            $goal_id = 1;
            $val = $color;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['ciclo'])){
            $goal_id = 2;
            $val = $ciclo;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['productividad'])){
            $goal_id = 3;
            $val = $productividad;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['tCabeza'])){
            $goal_id = 4;
            $val = $tCabeza;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['pFuerte'])){
            $goal_id = 5;
            $val = $pFuerte;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['fApertura'])){
            $goal_id = 6;
            $val = $fApertura;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['gTallo'])){
            $goal_id = 7;
            $val = $gTallo;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['longitud'])){
            $goal_id = 8;
            $val = $longitud;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['rFusarium'])){
            $goal_id = 9;
            $val = $rFusarium;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['sEnfermedades'])){
            $goal_id = 10;
            $val = $sEnfermedades;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['follaje'])){
            $goal_id = 11;
            $val = $follaje;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['pSpray'])){
            $goal_id = 12;
            $val = $pSpray;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (isset($_POST['sMini'])){
            $goal_id = 13;
            $val = $sMini;
            $sql = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) 
                     VALUES($order_id,$variedad_id,$goal_id,$val)";
            $result=$conexion->query($sql);
        }

        if (!empty($_POST['comentario'])){
            $com=$comentario;
            $sql = "INSERT INTO order_comments (orders_id,variety_id,comment) 
                     VALUES($order_id,$variedad_id,'$com')";
            $result=$conexion->query($sql);
        }
        /* $consulta = "INSERT INTO $tabla (order_id,variety_id,eval_goals_id,value) VALUES($order_id,$variedad_id,1,1) ";			
        $resultado = $conn->prepare($consulta);
        $resultado->execute(); */ 
        
        $consulta = "SELECT *  FROM $tabla ORDER BY id DESC LIMIT 1";
        $resultado = $conn->prepare($consulta);
        $resultado->execute();
        $data=$resultado->fetchAll(PDO::FETCH_ASSOC);    
        break;  
}
//echo $data;
print json_encode($data, JSON_UNESCAPED_UNICODE);//envio el array final el formato json a AJAX
$conn=null;