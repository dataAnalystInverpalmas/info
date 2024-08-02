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


<h4>Reporte de Evaluaciones</h4>

<div class="container-fluid">
    <div class="row">

         <div class="col-sm-2">
            <div class="form-group">
              <label for="">Fecha Inicial</label>  
              <input class="form-control" type="date" value="" name="" id="finicial">
              <label for="">Fecha Final</label>  
              <input class="form-control" type="date" value="" name="" id="ffinal">
              <br><select name="" id="nflor" class="form-control"></select>  
              <br><input id="listar" type="submit" onclick="listarEvaluaciones()" class="btn btn-success" value="Consultar">
            </div>

        </div>
        <div class="col-sm-10">
            <table  class="display nowrap" id="rEvaluaciones" style="width:100%">
                <thead>
                    <tr>
                        <th>Variedad</th>
                        <th>Evaluador</th>
                        <th>Comentario</th>
                        <th>Color</th>
                        <th>Ciclo</th>
                        <th>Productividad</th>
                        <th>tCabeza</th>
                        <th>pFuerte</th>
                        <th>fApertura</th>
                        <th>gTallo</th>
                        <th>Longitud</th>
                        <th>rFusarium</th>
                        <th>sEnfemedades</th>
                        <th>Follaje</th>
                        <th>pSpray</th>
                        <th>pMini</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </div>
</div>

<script src="scripts/scriptsEvaluations.js"></script>