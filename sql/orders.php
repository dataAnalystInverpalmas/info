<?php

//lamar conexion
if (is_file("funciones/conexion.php")){
    include ("funciones/conexion.php");
    require "vendor/autoload.php";
  }else{
    include ("../funciones/conexion.php");
    require "../vendor/autoload.php";
}

?>

<h4>Recepción PM y Esquejes</h4>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
              <label for="">Fecha Inicial</label>  
              <input class="form-control" type="date" value="" name="" id="fecha_ini">
              <label for="">Fecha Final</label>  
              <input class="form-control" type="date" value="" name="" id="fecha_fin">
              <br>
              <select name="" id="tipo" class="form-control">
                <option value="0">Programa</option>
                <option value="1">Adicional</option>
              </select>  
              <br><input id="listar" type="submit" onclick="listar()" class="btn btn-success" value="Consultar">
            </div>

        </div>
        <div class="col-sm-10">
            <div class="row" id="nuevo">
                <div class="col-sm-4">            
                    <button id="btnNuevo" type="button" class="btn btn-info" data-toggle="modal"><i class="material-icons">library_add</i></button>    
                </div>    
            </div> 
            <div class="row">
            <div class="col-sm-12">
            <!-- <div class="table-responsive"> -->        
                <table id="tableOrders" class="display" style="width:100%" >
                    <thead class="text-center">
                        <tr>
                            <th>Id</th>
                            <th>Fecha</th>
                            <th>Casa</th>                                
                            <th>Pref Documento</th>  
                            <th># Documento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>                           
                    </tbody>        
                </table>               
            <!-- </div> -->
        </div>
        </div>  
        </div>
    </div>
</div>

<!--Modal para CRUD-->
<div class="modal fade" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
        <form id="formOrders">    
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                        <label for="" class="col-form-label">fecha:</label>
                        <input type="date" class="form-control" id="fecha">
                        </div>
                        </div>
                        <div class="col-lg-6">
                        <div class="form-group">
                        <label for="" class="col-form-label">casa</label>
                        <input type="text" class="form-control" id="casa">
                        </div> 
                    </div>    
                </div>
                <div class="row"> 
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label">pref_documento</label>
                    <input type="text" class="form-control" id="pref_documento">
                    </div>               
                    </div>
                    <div class="col-lg-5">
                    <div class="form-group">
                    <label for="" class="col-form-label">n_documento</label>
                    <input type="number" class="form-control" id="n_documento">
                    </div>
                    </div>  
                </div>               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button type="submit" id="btnGuardar" class="btn btn-dark">Guardar</button>
            </div>
        </form>    
        </div>
    </div>
</div>  

<!--Modal para CRUD-->
<div class="modal fade" id="modalDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
        <form id="formOrders">    
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-10">
                        <div class="form-group">
                            <label for="" class="col-form-label">orden:</label>
                            <input type="text" class="form-control" id="order_id">
                            <label for="" class="col-form-label">origen:</label>
                            <input type="text" class="form-control" id="origen_id">
                            <label for="" class="col-form-label">propagación:</label>
                            <input type="text" class="form-control" id="propagacion_id">
                            <label for="" class="col-form-label">variedad:</label>
                            <input type="text" class="form-control" id="variedad_id">
                            <label for="" class="col-form-label">cantidad:</label>
                            <input type="text" class="form-control" id="cantidad">
                            <label for="" class="col-form-label">excedente:</label>
                            <input type="text" class="form-control" id="excedente">
                            <button type="submit" class="btn btn-success btnAgregarDetalles">Agregar</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-10">
                            <!-- <div class="table-responsive"> -->        
                            <table id="tableDetails" class="display" style="width:100%" >
                                <thead class="text-center">
                                    <tr>
                                        <th>Id</th>
                                        <th>order_id</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>                           
                                </tbody>        
                            </table>               
                            <!-- </div> -->
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>

<script src="scripts/orders.js"></script>