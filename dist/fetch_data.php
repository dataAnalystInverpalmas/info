<?php
//lamar conexion
if (is_file("funciones/conexion.php")){
	include ("funciones/conexion.php");
	}
	else {
	include ("../funciones/conexion.php");
	}
//carbon

if(isset($_POST["action"])){
    $query="SELECT * FROM varieties WHERE estado='1' ";

    if(isset($_POST["producto"])){
        $producto_filter = implode("','", $_POST["producto"]);
        $query .= "
        AND producto IN('".trim($producto_filter)."')
        ";
    }

    if(isset($_POST["color"])){
        $color_filter = implode("','",$_POST["color"]);
        $query.=" 
        AND color IN('".trim($color_filter)."')
        ";
    }

    if(isset($_POST["nombre"])){
        $variedad_filter = implode("','",$_POST["nombre"]);
        $query.=" 
        AND nombre IN('".trim($variedad_filter)."')
        ";
    }

    $resultado=$conexion->query($query);
    $total_rows=$resultado->num_rows;
    $output = '';
    if ($total_rows > 0){
        $output.='
        <img src='' />
        <div class="table">
        <table>
        <tr>
        <th>Producto</th><th>Color</th><th>Variedad</th>
        </tr>
        ';
        while($row=$resultado->fetch_assoc()){
            $output.='
            <tr>
            <td>'.$row["producto"].'</td><td>'.$row["color"].'</td><td>'.$row["nombre"].'</td>
            </tr>
            ';
        }
        ?>
        </table>
        </div>
        <?php
    }else{
        echo $output="No se encontró nada".$query;
    }
    echo $output;
}
?>