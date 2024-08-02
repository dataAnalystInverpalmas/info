<?php

if (is_file("funciones/conexion.php")){
	  include ("funciones/conexion.php");
        }
	else {
	  include ("../funciones/conexion.php");
}

$where = "WHERE 1 AND producto in('CLAVEL','MINICLAVEL','CLAVEL STANDARD','CLAVEL MINIATURA')";

if ($_POST){
	$finicial=strval($_POST['fini']);
	$ffinal=strval($_POST['ffin']);
        $tipo = $_POST['tipo'];

        if ( $tipo != "" ){
                $where .= "and tipo = '$tipo' ";
        }elseif( $finicial != "" & $ffinal != "") {
                $where .= "and fecha_siembra BETWEEN '$finicial' and '$ffinal' ";
        }
        else{
                $where .= "";
        }
}    

$query="
        SELECT  finca,
                bloque,
                producto,
                variedad,
                temporada,
                color, 
                fecha_siembra,
                sum(plantas) as plantas,
                tipo,
                ciclo,
                codvari,
                cod_temporada,
                siembra 
            FROM budget 
            $where 
            GROUP BY finca,bloque,producto,variedad,temporada,fecha_siembra,tipo,siembra,color 
            order by fecha_siembra asc
";

$result=$conexion->query($query);

$response = array();

while($row = $result->fetch_assoc()){
    $response[]=$row;
}

echo json_encode($response);

$conexion->close();

 ?> 