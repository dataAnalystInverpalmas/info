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

<h4>Labores Siembra</h4>

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
                <option value="1">Reemplazo</option>
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
                <table id="tablaLabores" class="display" style="width:100%" >
                    <thead class="text-center">
                        <tr>
                            <th>Id</th>
                            <th>Variedad</th>
                            <th>Temporada</th>                                
                            <th>Fecha Ensarte</th>  
                            <th>Esquejes Ensartados</th>
                            <th>Fecha Cosecha</th>
                            <th>Esquejes Cosechados</th>
                            <th>Banco</th>
                            <th>Esquejes</th>
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
        <form id="formLabors">    
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                        <label for="" class="col-form-label">Variedad:</label>
                        <input type="text" class="form-control" id="variedad">
                        </div>
                        </div>
                        <div class="col-lg-6">
                        <div class="form-group">
                        <label for="" class="col-form-label">Temporada</label>
                        <input type="text" class="form-control" id="temporada_obj">
                        </div> 
                    </div>    
                </div>
                <div class="row"> 
                    <div class="col-lg-6">
                    <div class="form-group">
                    <label for="" class="col-form-label">Fecha Ensarte</label>
                    <input type="date" class="form-control" id="fecha_ensarte_r">
                    </div>               
                    </div>
                    <div class="col-lg-5">
                    <div class="form-group">
                    <label for="" class="col-form-label">Esquejes Ensartados</label>
                    <input type="number" class="form-control" id="esquejes_ensarte">
                    </div>
                    </div>  
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                        <label for="" class="col-form-label">Fecha Cosecha</label>
                        <input type="date" class="form-control" id="fecha_cosecha_r">
                        </div>
                    </div>    
                    <div class="col-lg-5">    
                        <div class="form-group">
                        <label for="" class="col-form-label">Esquejes Cosechados</label>
                        <input type="number" class="form-control" id="esquejes_cosecha">
                        </div>            
                    </div>  
                </div>
                <div class="row">
                    <div class="col-lg-10">
                        <div class="form-group">
                            <label for="" class="col-form-label">Banco</label>
                            <input type="text" class="form-control" id="banco">
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

<script src="scripts/main_ls.js"></script>