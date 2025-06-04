<?php

if (is_file("funciones/conexion.php")){
        include ("funciones/conexion.php");
      }else{
        include ("../funciones/conexion.php");
}

if(isset($_POST))
{
        $finca=$_POST['finca'];
        $variedad=$_POST['variedad'];
        $producto=$_POST['producto'];
        $grado=$_POST['grado'];
        $valor=$_POST['valor'];
        $fuente =$_POST['fuente'];
        $user_id=$_SESSION['id'];
    
        $queryIdF = "SELECT id FROM farms WHERE nombre='$finca' ";
        $resIDF = $conexion->query($queryIdF);
        $idFinca = $resIDF->fetch_row();
        $idF = $idFinca['0']; 

        $queryIdP = "SELECT id FROM products WHERE nombre='$producto' ";
        $resIDP = $conexion->query($queryIdP);
        $idProducto = $resIDP->fetch_row();
        $idP = $idProducto['0']; 
        
        $queryIdV = "SELECT id FROM varieties WHERE nombre='$variedad' and producto='$producto' ";
        $resIDV = $conexion->query($queryIdV);
        $idvari = $resIDV->fetch_row();
        $idV = $idvari['0']; 

        $queryIdG = "SELECT id FROM grades WHERE grado='$grado' and producto_id='$idP' ";
        $resIDG = $conexion->query($queryIdG);
        $idGrado = $resIDG->fetch_row();
        $idG = $idGrado['0'];         

        $sql="INSERT INTO table_qualities (finca, variedad, fuente, grado, valor, user_id) 
                VALUES ($idF,$idV,'$fuente',$idG,$valor,$user_id)";//'$fecha',
        $result=$conexion->query($sql);

        //causas de nacional//

        $sqlID = "SELECT max(id) as idQ FROM table_qualities";
        $resTQ = $conexion->query($sqlID);
        $idT = $resTQ->fetch_row();
        $idTQ = $idT['0'];

                if (isset($_POST['acaros'])){
                        $val=$_POST['acaros'];
                        $cause = 1;
                        $sqlC = "INSERT INTO table_nalcauses (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }
                
                if (isset($_POST['thrips'])){
                        $val=$_POST['thrips'];
                        $cause = 2;
                        $sqlC = "INSERT INTO table_nalcauses (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['botritis'])){
                        $val=$_POST['botritis'];
                        $cause = 3;
                        $sqlC = "INSERT INTO table_nalcauses (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['velloso'])){
                        $val=$_POST['velloso'];
                        $cause = 4;
                        $sqlC = "INSERT INTO table_nalcauses (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['fusarium'])){
                        $val=$_POST['fusarium'];
                        $cause = 5;
                        $sqlC = "INSERT INTO table_nalcauses (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['heteros'])){
                        $val=$_POST['heteros'];
                        $cause = 6;
                        $sqlC = "INSERT INTO table_nalcauses (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['babosa'])){
                        $val=$_POST['babosa'];
                        $cause = 7;
                        $sqlC = "INSERT INTO table_nalcauses (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['rajado'])){
                        $val=$_POST['rajado'];
                        $cause = 8;
                        $sqlC = "INSERT INTO table_nalcauses (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['torcido'])){
                        $val=$_POST['torcido'];
                        $cause = 9;
                        $sqlC = "INSERT INTO table_nalcauses (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['dos_puntos'])){
                        $val=$_POST['dos:puntos'];
                        $cause = 10;
                        $sqlC = "INSERT INTO table_nalcauses (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['calidad'])){
                        $val=$_POST['calidad'];
                        $cause = 1;
                        $sqlC = "INSERT INTO table_causes_no_select (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['corto'])){
                        $val=$_POST['corto'];
                        $cause = 2;
                        $sqlC = "INSERT INTO table_causes_no_select (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['debil'])){
                        $val=$_POST['debil'];
                        $cause = 3;
                        $sqlC = "INSERT INTO table_causes_no_select (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['torcido'])){
                        $val=$_POST['torcido'];
                        $cause = 4;
                        $sqlC = "INSERT INTO table_causes_no_select (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['tPuntos'])){
                        $val=$_POST['tPuntos'];
                        $cause = 5;
                        $sqlC = "INSERT INTO table_causes_no_select (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }

                if (isset($_POST['tDelgados']) and $grado=='FANCY'){
                        $val=$_POST['tDelgados'];
                        $cause = 6;
                        $sqlC = "INSERT INTO table_causes_no_select (qualities_id,causa,muestra,valor) 
                        VALUES ($idTQ,$cause,$valor,$val)";
                $resultC=$conexion->query($sqlC);
                }
var_dump($sql);
}

?>
