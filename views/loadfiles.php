<?php
//lamar conexion
/* include ('funciones/conexion.php');

//archivo plano
$file = new SplFileObject('archivos/Conceptos.txt');

while (!$file->eof())
{
  $line = $file->fgets();
  list($tipo,$medida,$revisa)=explode(',',$line);
  
  $sql = "INSERT INTO indicator_transactions (tipo,revision,medida) 
          VALUES ('$tipo','$revisa','$medida')";
  $conexion->query($sql);
}

$conexion->close(); */

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-4 mx-auto"></div>
      <div class="col-sm-4 mx-auto">

          <form enctype="multipart/form-data" action="" method="post">
            <div class="form-group">
              <label for="exampleFormControlFile1">Cargar archivos</label>
              <input type="file" class="form-control" id="archivoId">
              <input type="button" class="btn btn-primary form-control form-control-lg" value="Subir Archivo" id="boton">
            </div>
          </form>
  
      </div>
    <div class="col-sm-4 mx-auto"></div>
  </div>
  <div class="row">
    <div class="col-sm-12 mx-auto">
    <h5>Lista de archivos y fecha de modificación</h5>
      <?php
      $dir = "archivos/"; //path o ruta de nuestro directorio 
      chdir($dir);
      array_multisort(array_map('filemtime', ($files = glob("*.*"))), SORT_DESC, $files);
      echo "<table class='table table-bordered'>";
      echo "<tr><th>Archivo</th><th>Fecha de modificación</th></tr>";
      foreach($files as $filename)
      {
      if($tr==0)
      {
        echo "<tr>";
      }
      //echo "<td><a href='download.php?path=".$filename."' target='_blank'>".substr($filename, 0, -5)."</a></td>";
      echo "<td>".substr($filename, 0, -5)."</td>";
      echo "<td>".date("F d Y",filemtime($filename))."</td>";
      $tr++;
      if($tr==1)
      {
      echo "</tr>";
      $tr=0;
      }
      
      }
      echo "</table>";
      ?>

    </div>
  </div>
</div>