$(document).ready(function(){
var fuente = $("#fuente").val();
document.getElementById('fuente').selectedIndex = 3;
});
////////////////////////////////////////////////////////////////////////////////////
function fetch_variety(val){
    $.ajax({
        type: 'post',
        url: 'CRUD/qfetchData.php',
        data: { get_option:val },
    success: function (response) {
        document.getElementById("variedad").innerHTML=response; 
    }
    });
    $.ajax({
        type: 'post',
        url: 'CRUD/qfetchDataGrades.php',
        data: { get_option:val },
    success: function (response) {
        document.getElementById("grado").innerHTML=response; 
    }
    });
}
  // agregar registros
function addQualities() {

  // get values
  var finca = $("#finca").val();
  var producto = $("#producto").val();
  var variedad = $("#variedad").val();
  var grado = $("#grado").val();
  var valor = $("#valor").val();
  var fuente = $("#fuente").val();
  var acaros =  $("#aca").val();
  var heteros = $("#het").val();
  var thrips =  $("#thr").val();
  var botritis =$("#bot").val();
  var fusarium =$("#fus").val();
  var velloso = $("#vel").val();
  var babosa =  $("#bab").val();
  var rajado =  $("#raj").val();
  var torcido =  $("#tor").val();
  var dosPuntos =  $("#dpt").val();
  var calidad =  $("#cal").val();
  var corto =  $("#cor").val();
  var debil =  $("#deb").val();
  var torcido =  $("#tcd").val();
  var tPuntos =  $("#tpt").val();
  var tDelgados =  $("#tde").val();
  var fecha = '<?php echo $fecha; ?>';

  // agregar registros
  $.post("CRUD/qaddRecord.php", {
      finca: finca,
      variedad: variedad,
      producto: producto,
      grado: grado,
      valor: valor,
      fuente: fuente,
      acaros: acaros,
      heteros: heteros,
      thrips: thrips,
      botritis: botritis,
      fusarium: fusarium,
      velloso: velloso,
      babosa: babosa,
      rajado: rajado,
      torcido: torcido,
      dosPuntos: dosPuntos,
      calidad: calidad,
      corto: corto,
      debil: debil,
      torcido: torcido,
      tPuntos: tPuntos,
      tDelgados: tDelgados,
      fecha: fecha
  }, function (data) {
      // close the popup
      //$("#add_new_record_modal").modal("hide");

      // leer registros
      readRecordsQualities();
      // borrar campos
      $("#valor").val("");
      $("#grado").val("");
      $("#aca").val("");
      $("#het").val("");
      $("#thr").val("");
      $("#bot").val("");
      $("#fus").val("");
      $("#vel").val("");
      $("#bab").val("");
      $("#raj").val("");
      $("#tor").val("");
      $("#dpt").val("");
      $("#cal").val("");
      $("#cor").val("");
      $("#deb").val("");
      $("#tcd").val("");
      $("#tpt").val("");
      $("#tde").val("");

  });
  var div_nal = document.getElementById("contenidoCausas");
  var div_nsel = document.getElementById("causasNS");
  div_nal.style.display = 'none';
  div_nsel.style.display = 'none';
}

function closeModal(){
  $("#finca").val("");
  $("#producto").val("");
  $("#variedad").val("");
  $("#grado").val("");
  $("#aca").val("");
  $("#het").val("");
  $("#thr").val("");
  $("#bot").val("");
  $("#fus").val("");
  $("#vel").val("");
  $("#bab").val("");
  $("#raj").val("");
  $("#tor").val("");
  $("#dpt").val("");
  $("#cal").val("");
  $("#cor").val("");
  $("#deb").val("");
  $("#tcd").val("");
  $("#tpt").val("");
  $("#tde").val("");
}

window.onload = function(){/*hace que se cargue la función lo que predetermina que div estará oculto hasta llamar a la función nuevamente*/
  hideShow('contenidoCausas');/* "contenido_a_mostrar" es el nombre que le dimos al DIV */
  readRecordsQualities();
  hideShow('causasNS');
}

function hideShow(id){
    if (document.getElementById){ //se obtiene el id
      var el = document.getElementById(id); //se define la variable "el" igual a nuestro div
      el.style.display = (el.style.display == 'none') ? 'block' : 'none'; //damos un atributo display:none que oculta el div
    }
}

function readRecordsQualities() {
    $.get("CRUD/qreadRecord.php", {}, function (data, status) {
        $("#records_content_qualities").html(data);
    });
}

function DeleteQualities(id){
    var conf = confirm("¿Está seguro, realmente desea eliminar el registro?");
    if (conf == true) {
        $.post("CRUD/qdeleteDetails.php", {
                id: id
            },
            function (data, status) {
                // reload Users by using readRecords();
                readRecordsQualities();
            }
        );
    } 
 }      
