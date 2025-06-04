<?php
//lamar conexion
if (is_file("funciones/conexion.php")){
    include ("funciones/conexion.php");
    require "vendor/autoload.php";
  }else{
    include ("../funciones/conexion.php");
    require "../vendor/autoload.php";
}

if(isset($_FILES["archivoId"])){
    $archivo = $_FILES["archivoId"]["name"];
    $destino = "../archivos/";

    if(move_uploaded_file($_FILES["archivoId"]["tmp_name"],$destino.$archivo)){
        echo "Subido con exito";
        $_SESSION['loadfile'] = 1;
    }else{
        echo "Hay un problema para subir el archivo";
    }
}