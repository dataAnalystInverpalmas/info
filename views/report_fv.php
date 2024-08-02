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

<h4>Reporte de Floreros</h4>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
            <div class="form-group">
              <label for="">Fecha Inicial</label>  
              <input class="form-control" type="date" value="" name="" id="finicial">
              <label for="">Fecha Final</label>  
              <input class="form-control" type="date" value="" name="" id="ffinal">
              <br><select name="" id="nflor" class="form-control"></select>  
              <br><input id="listar" type="submit" onclick="listar()" class="btn btn-success" value="Consultar">
            </div>

        </div>
        <div class="col-sm-10">
            <table  class="display" id="rFloreros" style="width:100%">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>Variedad</th>
                        <th>Origen</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Viaje Simulado</th>
                        <th>Guarde Granel</th>
                        <th>Evaluación Calificación</th>
                        <th>Vida Florero</th>
                        <th>Constistencia Color</th>
                        <th>Petalos</th>
                        <th>Velocidad Apertura</th>
                        <th>Forma Apertura</th>
                        <th>Tallo</th>
                        <th>Follaje</th>
                        <th>Final Optimo %</th>
                        <th>No conforme %</th>
                        <th>Inconformidades causa % - dias</th>
                        <th>Observaciones</th>
                        <th>Fecha_Florero</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th colspan="10"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script src="scripts/scripts_reportsFV.js"></script>