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

<h4>Registro de Evaluaciones</h4>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
              <label for="">Fecha Inicial</label>  
              <input class="form-control" type="date" value="" name="" id="fecha_ini">
              <label for="">Fecha Final</label>  
              <input class="form-control" type="date" value="" name="" id="fecha_fin">
              <br>
              <input id="listar" type="submit" onclick="listar()" class="btn btn-success" value="Consultar">
            </div>

        </div>
        <div class="col-sm-10">
            <div class="row" id="nuevo">
                <div class="col-sm-4">            
                    <button id="btnNuevo" type="button" class="btn btn-info" data-toggle="modal"><i class="material-icons">library_add</i></button>    
                </div>    
            </div> 
            <div class="row">
            <div class="table-responsive">
            <!-- <div class="table-responsive"> -->        
                <table id="tableOrders" class="table table-bordered table-sm display" style="width:100%" >
                    <thead class="text-center">
                        <tr>
                            <th>Id</th>
                            <th>Fecha</th>
                            <th>Evaluador</th>                                
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
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="" class="col-form-label">fecha</label>
                            <input type="date" class="form-control" id="fecha">
                        </div>
                        <div class="form-group">
                            <select name="evaluador" id="evaluador" class="form-control">
                            </select>
                        </div> 
                    </div>    
                </div>             
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <button type="submit" id="btnGuardar" class="btn btn-success">Guardar</button>
            </div>
        </form>    
        </div>
    </div>
</div>  

<!--Modal para CRUD-->
<div class="modal fade bs-example-modal-lg" id="modalDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="container-fluid">
                <div class="modal-body">
                    <form id="formOrders">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <input type="hidden" class="form-control" id="order_id">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select name="" id="flor" class="form-control" onchange="fetch_varieties(this.value)" required></select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <select name="" id="variedad" class="form-control" >
                                                    <option value="">Variedad</option>
                                                </select>
                                            </div>
                                        </div>  
                                    </div>
                                    <div id="puntajes">
                                        <div class="form-row">
                                            <div class="form-group col-sm">  
                                                <input placeholder="color" id="color" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">    
                                                <input placeholder="ciclo" id="ciclo" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">  
                                                <input placeholder="produc" id="productividad" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">  
                                                <input placeholder="tCabeza" id="tCabeza" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">  
                                                <input placeholder="pFuerte" id="pFuerte" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-sm">   
                                                <input placeholder="fApertura" id="fApertura" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="gTallo" id="gTallo" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="longitud" id="longitud" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="rFusarium" id="rFusarium" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="sEnfermedad" id="sEnfermedades" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-sm">   
                                                <input placeholder="follaje" id="follaje" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="pSpray" id="pSpray" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="sMini" id="sMini" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="" id="" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                            <div class="form-group col-sm">
                                                <input placeholder="" id="" class="form-control"  maxlength="1" size="1" type="number" min="1" max="3">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-sm-12">
                                                <input placeholder="Comentario" type="text" name="" id="comentario" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row"> 
                                        <button type="submit" class="btn btn-lg btn-block btn-success btnAgregarDetalles">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="table-responsive">
                                    <!-- <div class="table-responsive"> -->        
                                    <table id="tableDetails" class="table table-bordered table-sm" style="width:100%" >
                                        <thead class="text-center">
                                            <tr>
                                                <th>Id</th>
                                                <th>order_id</th>
                                                <th>Variedad</th>
                                                <th>Item</th>
                                                <th>Valor</th>
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
</div>
<script src="scripts/load_varieties.js"></script>
<script src="scripts/orders.js"></script>