<?php
//////////////conexion////////////////////
include ('../funciones/conexion.php');
//funciones personalizadas
//carbon
require "../vendor/autoload.php";

$query ="UPDATE plane AS p 
        SET p.gh_id=( 
            SELECT g.id FROM greenhouses as g 
            LEFT JOIN farms as f ON f.id=g.finca_id 
            WHERE f.nombre=p.finca AND p.bloque=g.bloque AND p.tabla=g.tabla AND p.nave=g.nave AND p.cama=g.cama ) ";

$result=$conexion->query($query);

$query ="
        SELECT p.finca,p.bloque,p.tabla,p.nave,p.cama,p.producto,p.variedad,p.temporada,p.fecha_siembra,p.tipo_siembra,p.plantas,g.longitud,g.ancho,p.cosecha_reem 
        FROM plane as p 
        LEFT JOIN greenhouses AS g ON p.gh_id=g.id 
        ORDER BY `p`.`finca` DESC";

$result=$conexion->query($query);

/* if($query->num_rows > 0){
    $delimiter = ";";
    $filename = "members_" . date('Y-m-d') . ".csv";
    
    //create a file pointer
    $f = fopen('php://memory', 'w');
    
    //set column headers
    $fields = array('finca', 'bloque', 'tabla', 'nave', 'cama', 'producto', 'variedad', 'temporada', 'fecha_siembra', 'tipo_siembra', 'plantas', 'longitud', 'ancho', 'cosecha_reem');
    fputcsv($f, $fields, $delimiter);
    
    //output each row of the data, format line as csv and write to file pointer
    while($row = $query->fetch_assoc()){
        $lineData = array(  
                            $row['finca'], 
                            $row['bloque'], 
                            $row['tabla'], 
                            $row['nave'], 
                            $row['cama'],
                            $row['producto'],
                            $row['variedad'],
                            $row['temporada'],
                            $row['fecha_siembra'],
                            $row['tipo_siembra'],
                            $row['plantas'],
                            $row['longitud'], 
                            $row['ancho'],   
                            $row['cosecha_reem']                        
                        );
        fputcsv($f, $lineData, $delimiter);
    }
    
    //move back to beginning of file
    fseek($f, 0);
    
    //set headers to download file rather than displayed
    header("Content-Transfer-Encoding: UTF-8");
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '";');
    header("Pragma: no-cache");
    header("Expires: 0");
    //output all remaining data on a file pointer
    fpassthru($f);
}
exit; */
