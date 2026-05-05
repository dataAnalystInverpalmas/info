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
      <table class="table table-bordered">
        <tr><th>Archivo</th><th>Fecha de modificación</th></tr>
        <?php foreach ($files as $filepath): ?>
        <tr>
          <td><?php echo htmlspecialchars(pathinfo(basename($filepath), PATHINFO_FILENAME), ENT_QUOTES, 'UTF-8'); ?></td>
          <td><?php echo date('F d Y', filemtime($filepath)); ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</div>
