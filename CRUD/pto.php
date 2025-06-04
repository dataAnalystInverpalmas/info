<?php

if (is_file("funciones/conexion.php")){
	  include ("funciones/conexion.php");
        }
	else {
	  include ("../funciones/conexion.php");
}

$where = " WHERE 1 ";

if ($_POST){

	$finicial=strval($_POST['fini']);
	$ffinal=strval($_POST['ffin']);
        $year=$_POST['year_p'];	
        $casa = strval($_POST['casa']);

        if ( $finicial != "" & $ffinal != "" and $year != "" and $casa == ""){
		$where .= "and programa=$year ";
	}else if( $finicial != "" & $ffinal != "" and $year == "" and $casa == "") {
                $where .= "and p.fecha_siembra BETWEEN '$finicial' and '$ffinal' ";
        }else if( $finicial != "" & $ffinal != "" and $year != "" and $casa != "") {
                $where .= "and p.programa=$year and b.nombre='$casa' ";
        }else{
                $where .= "";
        }

}       

$query="
        SELECT p.variedad,
                p.temporada_obj,
                p.producto,
                p.ciclo,
                DATE_FORMAT(AVG(p.fecha_siembra),'%Y-%m-%d') AS fecha_siembra,
                DATE_FORMAT(AVG(p.fecha_ensarte),'%Y-%m-%d') AS fecha_ensarte,
                DATE_FORMAT(AVG(p.fecha_cosecha),'%Y-%m-%d') AS fecha_cosecha,
                DATE_FORMAT(AVG(p.fecha_pico),'%Y-%m-%d') AS fecha_pico,
                p.programa,
                if(p.tipo='REEMPLAZO','REEMPLAZO','PROGRAMA') AS tipo,
                b.nombre as casa,
                p.raiz,
                sum(p.plantas) as plantas
        FROM program as p
        LEFT JOIN breeders as b ON b.id=p.casa_id
        $where
        GROUP BY p.variedad,p.temporada_obj,p.producto,p.fecha_siembra,p.tipo,p.casa_id,p.raiz  
        UNION
        SELECT p.variedad,
                p.temporada_obj,
                p.producto,
                p.ciclo,
                DATE_FORMAT(AVG(p.fecha_siembra),'%Y-%m-%d') AS fecha_siembra,
                DATE_FORMAT(AVG(p.fecha_ensarte),'%Y-%m-%d') AS fecha_ensarte,
                DATE_FORMAT(AVG(p.fecha_cosecha),'%Y-%m-%d') AS fecha_cosecha,
                DATE_FORMAT(AVG(p.fecha_pico),'%Y-%m-%d') AS fecha_pico,
                p.programa,
                if(p.tipo='REEMPLAZO','REEMPLAZO','PROGRAMA') AS tipo,
                b.nombre as casa,
                p.raiz,
                sum(p.plantas) as plantas
        FROM program_add_pto as p
        LEFT JOIN breeders as b ON b.id=p.casa_id
        $where
        GROUP BY p.variedad,p.temporada_obj,p.producto,p.fecha_siembra,p.tipo,p.casa_id,p.raiz
";

$result=$conexion->query($query);

$response = array();

while($row = $result->fetch_assoc()){
    $response[]=$row;
}

echo json_encode($response);

$conexion->close();

 ?> 