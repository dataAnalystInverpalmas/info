<style>
div.scroll {
  overflow: auto;
  white-space: nowrap;
  padding: 2px;
  overflow-x: auto;
  display: inline-block;
  float: none; 
}
</style>
<?php
//lamar conexion
use Carbon\Carbon;

$actual=Carbon::now('-5:00');
$fechacarbon=new Carbon($actual);
$fecha=$fechacarbon->format('Y-m-d');

if (is_file("funciones/conexion.php")){
	  include ("funciones/conexion.php");
	}else{
	  include ("../funciones/conexion.php");
}

require "vendor/autoload.php";

?>

<!-- Begin page content -->
<div class="container-fluid">
  <div class="row d-flex justify-content-center">
    <div class="col-sm-2">
        <div class="pull-left">
          <select class="form-control" name="fuente" id="fuente">
            <option value="0">Tipo Clasificación</option>
            <option value="1">Clasificación</option>
            <option value="2">Clasificación Dirigida</option>
            <option value="3">Clasificación Vitrinas</option>
          </select>
        </div>
      </div>
      <div class="col-sm-10">
        <div class="pull-right">
          <button class="btn btn-primary" data-toggle="modal" id="btnVitrinas" data-target="#add_new_record_modal">Agregar Clasificación</button>
        </div>
      </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="table-responsive">
        <br>
        <div id="records_content_qualities"></div>
      </div>
    </div>
  </div>
</div>
<!-- /Content Section --> 
<div class="container-fluid justify-content-center align-items-center">
  <!-- Bootstrap Modals --> 
  <!-- Modal - Add New Record/User -->
  <div class="modal fade" id="add_new_record_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="container-fluid modal-content">
          
        <div class="modal-header">
          <button onclick="closeModal()" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h7 class="modal-title" id="myModalLabel">Agregar Datos Clasificación</h7>
        </div>

        <div class="modal-body">
          <!--<div class="form-group">
            <input type="datetime-local" id="fecha" value=""   class="form-control"/>
          </div>-->
          <div class="form-group">
            <select id="finca" class="form-control" onchange="" required>
            <option value="">Finca</option>
              <?php
                $select=$conexion->query("select nombre from farms group by nombre");
                while($row=mysqli_fetch_array($select))
                {
                  echo "<option>".$row['nombre']."</option>";
                }
            ?>
            </select>
          </div>
          <div class="form-group">
          <select id="producto" class="form-control" onchange="fetch_variety(this.value);" required>
          <option value="">Producto</option>
              <?php
              $select=$conexion->query("select nombre from products group by nombre");
              while($row=mysqli_fetch_array($select))
              {
                echo "<option>".$row['nombre']."</option>";
              }
            ?>
            </select>
          </div>
          <div class="form-group"> 
            <div id="select_box1">
              <select type="text" id="variedad" class="form-control" value="" required></select>
            </div>
          </div>
          <div class="form-group">
            <div id="select_box1">
              <select type="text" id="grado" class="form-control" value="" required></select>
            </div>
          </div>
          <div class="form-group">
            <input type="text" id="valor" class="form-control" value="" placeholder="Valor" required/>
          </div>
          
          <!--causas de nacional--->    
          <div class="form-group row">
            <div class="col-sm-12">
              <a style='cursor: pointer;' onClick="hideShow('contenidoCausas')" title="" class="boton_mostrar"><div id="msg_nal">Causas Nacional</div></a>
            </div>
          </div> 
          <div id="contenidoCausas">
              <div class="row">
                <div class="col-sm">  
                  <input placeholder="ACA" id="aca" class="form-control"  maxlength="4" size="4">
                </div>
                <div class="col-sm">    
                  <input placeholder="HET" id="het" class="form-control"  maxlength="4" size="4">
                  </div>
                <div class="col-sm">  
                  <input placeholder="THR" id="thr" class="form-control"  maxlength="4" size="4">
                </div>
                <div class="col-sm">   
                  <input placeholder="BOT" id="bot" class="form-control"  maxlength="4" size="4">
                </div>
                <div class="col-sm">
                  <input placeholder="DPT" id="dpt" class="form-control"  maxlength="4" size="4">
                </div>
              </div>
              <div class="row">  
                <div class="col-sm">
                  <input placeholder="FUS" id="fus" class="form-control"  maxlength="4" size="4">
                  </div>
                <div class="col-sm">   
                  <input placeholder="VEL" id="vel" class="form-control"  maxlength="4" size="4">
                  </div>
                <div class="col-sm">   
                  <input placeholder="BAB" id="bab" class="form-control"  maxlength="4" size="4">
                  </div>
                <div class="col-sm">   
                  <input placeholder="RAJ" id="raj" class="form-control"  maxlength="4" size="4">
                </div>
                <div class="col-sm">
                  <input placeholder="TOR" id="tor" class="form-control"  maxlength="4" size="4">
                </div>
              </div>
          </div>
          <div class="form-group row">
            <div class="col-sm-12">
              <a style='cursor: pointer;' onClick="hideShow('causasNS')" title="" class="boton_mostrar"><div id="msg_nal">Causas No Selecto</div></a>
            </div>
          </div>
          <div id="causasNS">
              <div class="row">
                <div class="col-sm">  
                  <input placeholder="CAL" id="cal" class="form-control"  maxlength="4" size="4">
                </div>
                <div class="col-sm">    
                  <input placeholder="COR" id="cor" class="form-control"  maxlength="4" size="4">
                  </div>
                <div class="col-sm">  
                  <input placeholder="DEB" id="deb" class="form-control"  maxlength="4" size="4">
                </div>
              </div>
              <div class="row">
                <div class="col-sm">   
                  <input placeholder="TCD" id="tcd" class="form-control"  maxlength="4" size="4">
                </div>
                <div class="col-sm">
                  <input placeholder="TPT" id="tpt" class="form-control"  maxlength="4" size="4">
                </div>
                <div class="col-sm">
                  <input placeholder="TDE" id="tde" class="form-control"  maxlength="4" size="4">
                </div>
              </div>
          </div>   
          <!--FIN causas de nacional--->    
          </div>     
          <div class="modal-footer">
            <button type="button" onclick="closeModal()" class="btn btn-danger " data-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-primary" onclick="addQualities()">Agregar</button>
          </div>

        </div>
      </div>
  </div>
  <!-- // Modal --> 

        <!-- Fin Contenido --> 
</div>

<!--CArgar archivo de scripts-->
<script src="scripts/qualitiesScripts.js"></script>