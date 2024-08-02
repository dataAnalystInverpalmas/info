<?php

//lamar conexion
if (is_file("funciones/conexion.php")){
    include ("funciones/conexion.php");
    require "vendor/autoload.php";
  }else{
    include ("../funciones/conexion.php");
    require "../vendor/autoload.php";
}



//include ("CRUD/qualitiesData.php");

use Carbon\Carbon;

?>

<h4>Presupuesto con asignación de área</h4>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
              <label for="">Fecha Inicial</label>  
              <input class="form-control" type="date" value="" name="" id="finicialProy">
              <label for="">Fecha Final</label>  
              <input class="form-control" type="date" value="" name="" id="ffinalProy">
              <br><select name="" id="proy_year" class="form-control"></select>
              <br><input id="" type="submit" onclick="listarProy()" class="btn btn-success" value="Consultar">
            </div>

        </div>
        <div class="col-sm-10">
            <table  class="display" id="table_proy_anual" style="width:100%">
                <thead>
                    <tr>
                        <th>Finca</th>
                        <th>Bloque</th>
                        <th>Flor</th>
                        <th width="30px">Variedad</th>
                        <th>Temporada</th>
                        <th>Ciclo</th>
                        <th>Fecha Siembra</th>
                        <th>Tipo</th>
                        <th>Plantas</th>
                        <th>Cod Variedad</th>
                        <th>Cod Temporada</th>
                        <th>Siembra</th>
                        <th>Color</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align:right">Camas:</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="scripts/scriptsProy.js"></script>
