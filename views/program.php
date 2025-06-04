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

<h4>Presupuesto</h4>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
              <label for="">Fecha Inicial</label>  
              <input class="form-control" type="date" value="" name="" id="finicialPTO">
              <label for="">Fecha Final</label>  
              <input class="form-control" type="date" value="" name="" id="ffinalPTO">
              <!-- <br><select name="" id="flores" class="form-control"></select> -->
              <br><select name="" id="pto_year" class="form-control"></select>  
              <!-- <br><select name="" id="flores" class="form-control"></select> -->
              <br><select name="" id="pto_casa" class="form-control"></select>  
              <br><input id="findPTO" type="submit" onclick="listarPTO()" class="btn btn-success" value="Consultar">
            </div>

        </div>
        <div class="col-sm-10">
            <table  class="display" id="table_pto_anual" style="width:100%">
                <thead>
                    <tr>
                        <th>Flor</th>
                        <th>Variedad</th>
                        <th>Temporada</th>
                        <th>Ciclo</th>
                        <th>Fecha Siembra</th>
                        <th>Fecha Ensarte</th>
                        <th>Fecha Cosecha</th>
                        <th>Fecha Pico</th>
                        <th>Casa</th>
                        <th>Tipo</th>
                        <th>Programa</th>
                        <th>Raiz</th>
                        <th>Plantas</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th colspan="9" style="text-align:right">Camas:</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="scripts/scriptsPTO.js"></script>
