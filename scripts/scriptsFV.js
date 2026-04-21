/////////////////READY////////////////////////
$(document).ready(function(){
    // SmartWizard initialize
    readRecordsFV()
    $('#smartwizard').on("leaveStep", function(e, anchorObject, stepNumber, stepDirection){
      
      if (stepNumber==0 && stepDirection > 0){
        return guardaDatos();
      }

      // stepDirection contiene el índice del paso destino; si es mayor al actual, vamos hacia adelante
      if (stepNumber == 1 && stepDirection > 1){
        if (bdEvaluaciones.length < totalItemsCount){
          alert('Debe registrar todos los items de evaluación antes de continuar.\nRegistrados: ' + bdEvaluaciones.length + ' de ' + totalItemsCount + '.');
          return false;
        }
      }
      
    });

  // Reiniciar wizard al abrir el modal para que step-1 se muestre correctamente
  $('#exampleModal').on('shown.bs.modal', function () {
    $('#smartwizard').smartWizard("reset");
    $('#smartwizard').smartWizard("goToStep", 0);
  });

  $('#smartwizard').smartWizard({

      theme: 'dots',
      transitionsEffect: 'fade',
      transitionSpeed: '400',
      enableFinishButton: true,
      enableNextButton: true,
      autoAdjustHeight: true,
      completeCallback:function() {},
      callbacks: {},
      getTitleAndStep:function() {},
      toolbarSettings: {
        showNextButton: true,
        showPreviousButton: true,
        toolbarExtraButtons: [
        $('<button></button>').text('Finalizar')
          .addClass('btn btn-success')
          .on('click', function(){
            var totalTallos = totalTallosFv;
            var talloCausas = bdCausas.reduce(function(sum, c){ return sum + (parseInt(c.cantidad) || 0); }, 0);
            if (talloCausas !== totalTallos){
              alert('El total de tallos en causas (' + talloCausas + ') debe ser igual al total de tallos del florero (' + totalTallos + ').');
              return;
            }
            $('#smartwizard').smartWizard("reset");
            //$('#smartwizard').smartWizard("goToStep", 0); 
            bdCausas = []; ///////////varibles globales
            bdEvaluaciones = [];
            document.getElementById("tablaCausas").innerHTML="";
            document.getElementById("tabla").innerHTML="";                            
          })
      ]
      },
      lang: { // Language variables for button
        next: 'Siguiente',
        previous: 'Atrás'
      },
      anchorSettings: {
        markDoneStep: true,
        markAllPreviousStepsAsDone: true
      }
    });
    ///////////////////CARGA COMBO FLOR////
    $.ajax({
      type: "POST",
      url: "CRUD/fv_fetchProducts.php",
      success: function(response)
      {
          $('#flor').html(response).fadeIn();
      }
    });
    ///////////////////CARGA COMBO TIPOS DE FLORERO////
    $.ajax({
      type: "POST",
      url: "CRUD/fv_fetchKinds.php",
      success: function(response)
      {
          $('#tipo').html(response).fadeIn();
      }
    });
    ///////////////////CARGA COMBO ORIGENES////
    $.ajax({
      type: "POST",
      url: "CRUD/fv_fetchOrigen.php",
      success: function(response)
      {
          $('#origen').html(response).fadeIn();
      }
    });
    ///////////////////CARGA COMBO ITEMS////
    $.ajax({
      type: "POST",
      url: "CRUD/fv_fetchItems.php",
      success: function(response)
      {
          $('#item').html(response).fadeIn();
          totalItemsCount = $('#item option[value!=""]').length;
      }
    });
    ///////////////////CARGA COMBO CAUSAS////
    $.ajax({
      type: "POST",
      url: "CRUD/fv_fetchCauses.php",
      success: function(response)
      {
          $('#causa').html(response).fadeIn();
      }
    });
    ////////////DESHABILITAR/////////////////////
    
  });
  //////////////////end ready/////////////////////////////
 
  /////////////////CARGAR AL COMBO VARIEDADES/////////
  function fetch_varieties(val){
      $.ajax({
          type: 'post',
          url: 'CRUD/fv_fetchVarieties.php',
          data: { get_option:val },
        success: function (response) {
          document.getElementById("variedad").innerHTML=response; 
        }
      });
    }
  ///////////////////////////////////////////////
  function getItems(val){
    $.ajax({
        type: 'post',
        url: 'CRUD/fv_fetchItems.php',
        data: { get_option: val },
      success: function (response) {
        document.getElementById("valor").innerHTML=response; 
      }
    });
  }

  function florero(){
    $.ajax({
      type: 'post',
      url: 'CRUD/fv_fetchFlorero.php',  
    success: function (response) {
      document.getElementById("florero").innerHTML=response; 
      document.getElementById("floreroo").innerHTML=response; 
    }
  });
  }
  
function check(){
  if($("#grupo").is(':checked')) {  
    } else {  
      $("#gdescripcion").val("");
      $("#flor").val("");
      $("#origen").val("");
    }  
}
/////////////////GUARDAR TABLA CASUSAS/////////
var bdCausas = []; ///////////varibles globales
var bdEvaluaciones = []; ////variables globales
var totalItemsCount = 0; // total de items de evaluacion disponibles
var totalTallosFv = 0; // total de tallos guardado al pasar del step-1
/////////////////GUARDAR TABLA EVALUACIONES////
function guardaEvaluacion(){

  function Evaluacion(item,valor){
    this.item = item;
    this.valor = valor;
  }

  var item = document.getElementById('item').value;
  var valor = parseInt(document.getElementById('valor').value);

  if (!item || isNaN(valor)){
    alert('Seleccione un item y un valor antes de guardar.');
    return;
  }

  // Validar que el item no este duplicado
  var duplicado = bdEvaluaciones.some(function(ev){ return ev.item === item; });
  if (duplicado){
    alert('El item "' + item + '" ya fue registrado. No se pueden repetir items en la evaluación.');
    renderTablaEvaluacion();
    return;
  }

  var nuevaEvaluacion = new Evaluacion(item,valor);
  //var json = JSON.stringify(nuevaEvaluacion);

$.ajax({
      type: "POST",
      url: "CRUD/fv_addEvaluations.php",
      data: {item: nuevaEvaluacion.item, valor: nuevaEvaluacion.valor}, 
      cache: false,
      success: function(data){
          console.log(data);
          $("#item").val("");
          $("#valor").val("");
      }
  });
  agregar(nuevaEvaluacion);
}

function agregar(nuevaEvaluacion){

  bdEvaluaciones.push(nuevaEvaluacion);
  renderTablaEvaluacion();

}

function renderTablaEvaluacion(){
  var html = '';
  bdEvaluaciones.forEach(function(ev, idx){
    html += '<tr>' +
      '<td>' + ev.item + '</td>' +
      '<td>' + ev.valor + '</td>' +
      '<td><button class="btn btn-sm btn-danger" onclick="eliminarEvaluacion(' + idx + ')">&#128465;</button></td>' +
      '</tr>';
  });
  document.getElementById("tabla").innerHTML = html;
}

function eliminarEvaluacion(idx){
  bdEvaluaciones.splice(idx, 1);
  renderTablaEvaluacion();
}
///////////////////////causas//////////////////////// 
function guardaCausa(){

  function Causa(causa,dias,cantidad){
    this.causa= causa;
    this.dias = dias;
    this.cantidad = cantidad;
  }
    
var causa = document.getElementById('causa').value;
var dias = document.getElementById('dias').value;
var cantidad = parseInt(document.getElementById('cantidad').value) || 0;

  if (!causa || !dias || cantidad < 1){
    alert('Complete todos los campos: causa, días y cantidad de tallos.');
    return;
  }

  var totalTallos = totalTallosFv;
  var tallosRegistrados = bdCausas.reduce(function(sum, c){ return sum + (parseInt(c.cantidad) || 0); }, 0);

  if (tallosRegistrados + cantidad > totalTallos){
    var restantes = totalTallos - tallosRegistrados;
    alert('No puede superar el total de tallos del florero (' + totalTallos + ').\nTallos ya registrados: ' + tallosRegistrados + '. Puede registrar como máximo ' + restantes + ' tallo(s) más.');
    return;
  }

var nuevaCausa = new Causa(causa,dias,cantidad);

$.ajax({
        type: "POST",
        url: "CRUD/fv_addCauses.php",
        data: {causa: nuevaCausa.causa, dias: nuevaCausa.dias, cantidad: nuevaCausa.cantidad}, 
        cache: false,
        success: function(data){
            console.log(data);
            $("#causa").val("");
            $("#dias").val("");
            $("#cantidad").val("");
        }
});

  add(nuevaCausa);

}

function add(nuevaCausa){
  bdCausas.push(nuevaCausa);
  renderTablaCausas();
}

function renderTablaCausas(){
  var html = '';
  bdCausas.forEach(function(c, idx){
    html += '<tr>' +
      '<td>' + c.causa + '</td>' +
      '<td>' + c.dias + '</td>' +
      '<td>' + c.cantidad + '</td>' +
      '<td><button class="btn btn-sm btn-danger" onclick="eliminarCausa(' + idx + ')">&#128465;</button></td>' +
      '</tr>';
  });
  document.getElementById("tablaCausas").innerHTML = html;
}

function eliminarCausa(idx){
  bdCausas.splice(idx, 1);
  renderTablaCausas();
}
/////////////////GRUARDA FORMULARIO GENRAL/////////
function guardaDatos(){
  //$("#formFlorero").slideUp(3000);
    var fecha_corte = $("#fecha_corte").val();
    var fecha = $("#fecha").val();
    if ($("#grupo").is(':checked') === true){
      var grupo = 1;
    }else{
      var grupo = 0;
    }
    if ($("#simulacion").is(':checked') === true){
      var simulacion = 1;
    }else{
      var simulacion = 0;
    }
    var gdescripcion= $("#gdescripcion").val();
    var tipo= $("#tipo").val();
    var producto= $("#flor").val();
    var variedad= $("#variedad").val();
    var tallos = $("#tallos").val();
    totalTallosFv = parseInt(tallos) || 0;
    var pmax = $("#pmax").val();
    var pcorte = $("#pcorte").val();
    var pempaque = $("#pempaque").val();
    var pflorero = $("#pflorero").val();
    var guarde = $("#guarde").val();
    var origen= $("#origen").val();
    var comentario = $("#comentario").val();
  if (fecha==='' || pflorero<1 || pmax<1 || tallos<1 || tipo==='' || variedad==='' || origen==='' ){
    alert("Hay campos obligatorios vacíos. Complete: Fecha Florero, Tipo, Variedad, Origen, Total tallos, Punto máximo apertura y Punto florero.");
    return false;
  }else{
    $.ajax({
      url: 'CRUD/fv_addRecord.php',
      type: 'POST',
      data: {
        fecha_corte: fecha_corte,
        fecha: fecha,
        grupo: grupo,
        gdescripcion: gdescripcion,
        tipo: tipo,
        producto: producto,
        variedad: variedad,
        origen: origen,
        guarde: guarde,
        pmax: pmax,
        tallos: tallos,
        pcorte: pcorte,
        pempaque: pempaque,
        pflorero: pflorero,
        simulacion: simulacion,
        comentario: comentario
      },
      success: function (response) {
        readRecordsFV();
        //var obj = JSON.parse(response);
        console.log(response);
        // Go to step
      if (grupo===1){
        $("#variedad").val("");
        $("#origen").val("");
        $("#tipo").val("");
        $("#pmax").val("");
        $("#pcorte").val("");
        $("#pempaque").val("");
        $("#pflorero").val("");
        $("#tallos").val("");
        $("#simulacion").prop( "checked", false );
        $("#guarde").val("");
        $("#comentario").val("");
      }else{
        $("#gdescripcion").val("");
        $("#tipo").val("");
        $("#flor").val("");
        $("#variedad").val("");
        $("#origen").val("");
        $("#pmax").val("");
        $("#pcorte").val("");
        $("#pempaque").val("");
        $("#pflorero").val("");
        $("#tallos").val("");
        $("#simulacion").prop( "checked", false );
        $("#guarde").val("");
        $("#comentario").val("");
      } 
        //$('#smartwizard').smartWizard("next");
      },error: function (errorThrown, status, error) {
        console.log( status );
      }
    });
  }
    //LIMPIAR FORMULARIO/////////////////////////////
florero();   
 
    //$("#botones").slideDown(2000);
} 

function readRecordsFV() {
  $.get("CRUD/FVreadRecord.php", {}, function (data, status) {
    $("#records_content_fv").html(data);
  });
}

function DeleteFV(id){
  var conf = confirm("¿Está seguro, realmente desea eliminar el registro?");
  if (conf == true) {
      $.post("CRUD/FVdeleteDetails.php", {
              id: id
          },
          function (data, status) {
              // reload Users by using readRecords();
              readRecordsFV();
          }
      );
  } 
}  