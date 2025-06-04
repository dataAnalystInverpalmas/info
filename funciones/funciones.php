<?php
//lamar conexion
include_once '../bd/conexion.php';
if (is_file("funciones/conexion.php")){
    include ("funciones/conexion.php");
  }else{
    include ("../funciones/conexion.php");
}
use Carbon\Carbon;

function fini(){//funcion para allar el inicio de la semana
	$fini = Carbon::now(); // or $date = new Carbon();
	$year=$fini->year;//año actual
	$fini->setISODate($year,43); // fecha de semana 42
    $fini->startOfWeek(Carbon::MONDAY); // inicio de la semana
    //$fini->addWeek(72);
    $fini->format('Y-m-d');
    return $fini;
}
function ffin(){//semana 42 siguiente año
	$ffin=Carbon::now();//fecha actual
	$yearf=$ffin->year;
	$ffin->setISODate($yearf+1,43); // fecha de semana 42
    $ffin->endOfWeek(Carbon::SUNDAY); // inicio de la semana
    //ahora retroceder 72 semanas
    $ffin->format('Y-m-d');
    return $ffin;
}

function roundC($valor){
    $cama=960;//valor de cama real
    $numero=intval($valor/$cama);
    $sobra=$valor%$cama;
    if($sobra>($cama/2)){
        $rta=$numero+1;
    }else{
        $rta=$numero;
    }
    return $rta;
}

function exist_data($order,$variety,$item){
    $sql = "SELECT id FROM informes.orders_details WHERE order_id=$order AND variety_id=$variety AND eval_goals_id=$item";
    $result = $conexion->query($sql); 
    $row = $result->fetch_assoc(); 
    $evaluador_id = $row['id'];

    if (empty($evaluador_id)){
        return int(0);
    }else{
        return int($evaluador_id);
    }
}

?>