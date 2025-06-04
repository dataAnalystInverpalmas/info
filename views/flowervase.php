<?php
//lamar conexion
if(is_file("funciones/conexion.php")){
	  include ("funciones/conexion.php");
	}
else{
  include ("../funciones/conexion.php");
}

require "vendor/autoload.php";

?>

<style>
  .modal {
    padding: 0 !important;
  }
  .modal .modal-dialog {
    width: 100%;
    max-width: none;
    height: 100%;
    margin: 0;
  }
  .modal .modal-content {
    height: 100%;
    border: 0;
    border-radius: 0;
  }
  .modal .modal-body {
    overflow-y: auto;
}
</style>

<div class="container-fluid">
  <div class="row d-flex justify-content-center">
    <div class="col-sm-2">
      <div class="pull-left">
        <div class="row d-flex justify-content-center mt-200"> 
          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal"> Nuevo </button>
        </div> <!-- Modal -->
      </div>
    </div>
    <div class="col-sm-10">
        <div class="pull-right">
          <h4>Registro de floreros</h4>
        </div>
      </div>  
  </div>
  <div class="row">
    <div class="col">
      <div class="table-responsive">
        <br>
        <div id="records_content_fv"></div>
      </div>
    </div>
  </div>
</div>
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="container-fluid modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Registro de floreros</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <!-- <div class="container-fluid" id="smartwizard"> -->
          <div class="" id="smartwizard">
            <ul class="nav">
              <li>
                <a class="nav-link" href="#step-1">Floreros</a>
              </li>
              <li>
                <a class="nav-link" href="#step-2">Evaluación</a>
              </li>
              <li>
                <a class="nav-link" href="#step-3">Causas</a>
              </li>
            </ul> 
            <div class="tab-content">
                      <!-- <div style="height: calc(100%); width: calc(100%); overflow-y: scroll;" id="step-1" class="tab-pane" role="tabpanel">-->
              <div id="step-1" class="tab-pane" role="tabpanel">
                        <!--<div class="row" id="formFlorero">
                          <div class="col-sm-12"> -->
                <div class="row">
                  <div class="col-sm-2">    
                    <div class="form-group"> 
                      <label for="">Fecha Corte</label>   
                      <input type="date" name="" id="fecha_corte" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-2">    
                    <div class="form-group">  
                      <label for="">Fecha Florero</label>    
                      <input type="date" name="" id="fecha" class="form-control">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-check">    
                      <input type="checkbox" name="grupo" id="grupo" class="form-check-input" onclick="check(this.value)">
                      <label for="" class="form-check-label">Agrupar con anterior</label>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="form-group">    
                      <textarea name="" rows="2" id="gdescripcion" class="form-control gd" placeholder="Para Agrupar con anterior, use casilla de verificación." readonly></textarea>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-2">
                    <div class="form-group">
                      <select name="" id="tipo" class="form-control">
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">
                      <select name="" id="origen" class="form-control">
                      </select>
                    </div>
                  </div>  
                  <div class="col-sm-2">
                    <div class="form-check">    
                      <input type="checkbox" name="" id="simulacion" class="form-check-input">
                      <label for="" class="form-check-label">Simulación de viaje</label>
                    </div>
                  </div>  
                  <div class="col-sm-2">
                    <div class="form-group">
                      <select name="" id="flor" class="form-control" onchange="fetch_varieties(this.value)" required></select>
                    </div>
                  </div>
                  <div class="col-sm-4">
                    <div class="form-group">
                      <select name="" id="variedad" class="form-control" >
                        <option value="">Variedad</option>
                      </select>
                    </div>
                  </div>   
                </div>   
                <div class="row">
                  <div class="col-sm-2">
                    <div class="form-group">
                      <input type="number" name="tallos" id="tallos" class="form-control" placeholder="Total tallos">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">    
                      <input type="number" name="guarde" id="guarde" class="form-control" placeholder="Dias de guarde">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">    
                      <input type="number" name="" id="pmax" class="form-control" placeholder="Punto max apertura">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">    
                      <input type="number" name="" id="pcorte" class="form-control" placeholder="Punto corte">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">    
                      <input type="number" name="" id="pempaque" class="form-control" placeholder="Punto empaque">
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="form-group">    
                      <input type="number" name="" id="pflorero" class="form-control" placeholder="Punto florero">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <textarea type="text-area" class="form-control" rows="3" id="comentario" placeholder="Comentarios finales"></textarea>
                    </div>
                  </div>
                </div>
              </div>
              
              <div id="step-2" class="tab-pane" role="tabpanel">
                <div class="row h-100" id="formEvaluacion">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <select name="" id="item" placeholder="Item" class="form-control" onclick="getItems(this.value)"></select>
                    </div>
                    <div class="form-group">
                      <select name="" id="valor" class="form-control" placeholder="Valor"></select>
                    </div>
                      <input type="submit" class="btn btn-success" id="guardaEvaluacion" onclick="guardaEvaluacion()">
                    <div class="form-group">
                      <br><label for=""><span class="badge badge-secondary" id="florero"></span></label>
                    </div>
                  </div>
                  <div class="col-sm-6" id="resultEvaluacion">
                    <table class="table">
                      <thead>
                        <tr><th>Item</th><th>Valor</th></tr>
                      </thead>
                      <tbody id="tabla"></tbody>
                    </table>
                  </div>
                </div>
              </div>
              <div  id="step-3" class="tab-pane" role="tabpanel">
                <div class="row" id="formCausas">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <select name="" id="causa" class="form-control" placeholder="Causa"></select>
                    </div>
                    <div class="form-group">  
                      <input type="number" name="" id="dias" class="form-control" placeholder="#Dias">
                    </div>
                    <div class="form-group">  
                      <input type="number" name="" id="cantidad" class="form-control" placeholder="#Tallos">
                    </div>
                      <input type="submit" class="btn btn-success" id="guardaEvaluacion" onclick="guardaCausa()">
                    <div class="form-group">
                      <br><label for=""><span class="badge badge-secondary" id="floreroo"></span></label>
                    </div>
                  </div>
                  <div class="col-sm-6" id="resultCausas">
                    <table class="table">
                      <thead>
                        <tr><th>Causa</th><th>Dias</th><th>Cantidad</th></tr>
                      </thead>
                      <tbody id="tablaCausas"></tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!--CArgar archivo de scripts-->
<script src="scripts/scriptsFV.js"></script>