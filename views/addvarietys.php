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

<h4>Reporte de Variedades</h4>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
              <label for="">Fecha Inicial</label>  
              <input class="form-control" type="date" value="" name="" id="finicialAV">
              <label for="">Fecha Final</label>  
              <input class="form-control" type="date" value="" name="" id="ffinalAV">
			  <br><select name="" id="namefinca" class="form-control"></select>
              <br><select name="" id="nameflor" class="form-control"></select>  
              <br><input id="listarAV" type="submit" onclick="listarAV()" class="btn btn-success" value="Consultar">
            </div>

        </div>
        <div class="col-sm-10">
            <table  class="display" id="rAddVarieties" style="width:100%">
                <thead>
                    <tr>
                        <th>Finca</th>
						<th>Bloque</th>
						<th>Variedad</th>
						<th>Cosecha</th>
                        <th>Fecha Siembra</th>
                        <th>Nueva Fecha</th>
                        <th>Plantas</th>
                        <th>Nuevas Plantas</th>
                        <th>Porcentaje</th>
                        <th>Semana Cambio</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </div>
</div>

<script src="scripts/scripts_Add_Var.js"></script>