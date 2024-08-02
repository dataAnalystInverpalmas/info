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

<h4>Reporte de Calidades</h4>

<div class="container-fluid">
    <div class="row">

         <div class="col-sm-2">
            <div class="form-group">
              <label for="">Fecha Inicial</label>  
              <input class="form-control" type="date" value="" name="" id="finicial">
              <label for="">Fecha Final</label>  
              <input class="form-control" type="date" value="" name="" id="ffinal">
              <br><select name="" id="nflor" class="form-control"></select>  
              <br><input id="listar" type="submit" onclick="listarCalidades()" class="btn btn-success" value="Consultar">
            </div>

        </div>
        <div class="col-sm-10">
            <table  class="display" id="rCalidades" style="width:100%">
                <thead>
                    <tr>
                        <th>Variedad</th>
                        <th>%SEL</th>
                        <th>%FAN</th>
                        <th>%STD</th>
                        <th>%SHR</th>
                        <th>%NAL</th>
                        <th>Causas Nacional</th>
                        <th>Causas No Selecto</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </div>
</div>

<script src="scripts/scriptsQualities.js"></script>