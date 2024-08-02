$(document).ready(function() {

    //////////////////////fechas////////////////////////////////
    var fecha = new Date(); //Fecha actual
    var mes = fecha.getMonth()+1; //obteniendo mes
    var dia = fecha.getDate(); //obteniendo dia
    var ano = fecha.getFullYear(); //obteniendo año
    if(dia<10)
        dia='0'+dia; //agrega cero si el menor de 10
    if(mes<10)
        mes='0'+mes //agrega cero si el menor de 10
    var mes_i = "01";
    var dia_i= "01";

//////////////////////////////////////////////////////////////
document.getElementById('fecha_ini').value=ano+"-"+mes_i+"-"+dia_i;     
document.getElementById('fecha_fin').value=ano+"-"+mes+"-"+dia;
/////////////////////fechas///////////////////////////////////////  

var id, opcion;

listar_reports();

var fila; //captura la fila, para editar o eliminar
//submit para el Alta y Actualización
$('#formReports').submit(function(e){                         
   e.preventDefault(); //evita el comportambiento normal del submit, es decir, recarga total de la página
   fecha = $.trim($('#fecha').val());    
   finca = $.trim($('#finca').val());
   area = $.trim($('#area').val());
   flor = $.trim($('#flor').val());
   tipod = $.trim($('#tipod').val());
   //id = parseInt(fila.find('td:eq(0)').text()); //capturo el ID	 
   var tipo = 0;//cero para trabajar con pedido general                             
       $.ajax({
         url: "CRUD/crud_reports.php",
         type: "POST",
         datatype:"json",    
         data:  {fecha:fecha, finca:finca, area:area, flor:flor, tipod:tipod ,opcion:opcion, tipo:tipo},    
         success: function(data) {
           //tableReports.ajax.reload(null, false);
           //listar();
           $('#tableReports').DataTable().ajax.reload(null, false);
           
          }
       });			        
   $('#modalCRUD').modal('hide');
   $('#modalDetails').modal('hide');												     			
});


///////////////////////agregar detalles///////////////////////
$(document).on("click", ".btnAgregarDetalles", function(){     
tipo = 1;
opcion = 7;                      
order_id = $.trim($('#order_id').val());
variedad = $.trim($('#variedad').val());
color = $.trim($('#color').val());
comentario = $.trim($('#comentario').val());


event.preventDefault();                         
    $.ajax({
      url: "CRUD/crud_reports.php",
      type: "POST",
      datatype:"json",    
      data:  {
        order_id:order_id,
        variedad:variedad,
        color:color,
        comentario:comentario,
        opcion:opcion, 
        tipo:tipo
        },    
      success: function(data) {
        //tableReports.ajax.reload(null, false);
        //listar();
        $('#tableReportsDetails').DataTable().ajax.reload(null, false);
        console.log(data)
       }
    });			        
    //$('#modalCRUD').modal('hide');
    //$('#modalDetails').modal('hide');
    // borrar campos
    $("#color").val("");	
    $("#comentario").val("");	
});
///////////////////////fin de detalles/////////////// 
//para limpiar los campos antes de dar de Alta una Persona
$("#btnNuevo").click(function(){
   opcion = 1; //alta           
   id=null;
   $("#formReports").trigger("reset");
   $(".modal-header").css( "background-color", "#17a2b8");
   $(".modal-header").css( "color", "white" );
   $(".modal-title").text("Registra Evaluadores");
   $('#modalCRUD').modal('show');	    
});

//Editar        
$(document).on("click", ".btnEditar", function(){		        
   opcion = 2;//editar
   fila = $(this).closest("tr");	        
   id = parseInt(fila.find('td:eq(0)').text()); //capturo el ID		            
   fecha = fila.find('td:eq(1)').text();
   evaluador = fila.find('td:eq(2)').text();
   
   $("#fecha").val(fecha);
   $("#evaluador").val(evaluador);
   $(".modal-header").css("background-color", "#007bff");
   $(".modal-header").css("color", "white" );
   $(".modal-title").text("Editar Labor");		
   $('#modalCRUD').modal('show');		   
});

//Borrar
$(document).on("click", ".btnBorrar", function(){
   fila = $(this);           
   id = parseInt($(this).closest('tr').find('td:eq(0)').text()) ;		
   opcion = 3; //eliminar        
   var respuesta = confirm("¿Está seguro de borrar el registro "+id+"?");                
   if (respuesta) {            
       $.ajax({
         url: "CRUD/crud_reports.php",
         type: "POST",
         datatype:"json",    
         data:  {opcion:opcion, id:id},    
         success: function() {
               //tableReports.row(fila.parents('tr')).remove().draw();
               $('#tableReports').DataTable().ajax.reload(null, false);                  
          }
       });	
   }
}); 

 //Borrar
$(document).on("click", ".btnBorrarDetalles", function(){
fila = $(this);           
id = parseInt($(this).closest('tr').find('td:eq(0)').text()) ;		
opcion = 6; //eliminar 
tipo = 1;       
var respuesta = confirm("¿Está seguro de borrar el registro "+id+"?");                
if (respuesta) {
    event.preventDefault();            
    $.ajax({
      url: "CRUD/crud_reports.php",
      type: "POST",
      datatype:"json",    
      data:  {opcion:opcion, id:id, tipo:tipo},    
      success: function() {
            //tableReports.row(fila.parents('tr')).remove().draw();
            $('#tableReportsDetails').DataTable().ajax.reload(null, false);                  
       },
      error: function(){
          console.log("Error");
      }
    });	
}
}); 

 //Editar        
$(document).on("click", ".btnDetalles", function(){		        
    opcion = 5;//editar
    fila = $(this).closest("tr");        
    id = parseInt(fila.find('td:eq(0)').text()); //capturo el ID		            
    var tipo = 1;//cero para trabajar con pedido general   
    $("#order_id").val(id);
    var tableReportsDetails = $('#tableReportsDetails').DataTable({
        dom: 'frtip',
        //responsive: true,
        "order": [[ 0, "desc" ]],
        "destroy": true,
        'processing': true,
        "destroy": true,
        pageLength: 5, 
        "ajax":{            
            "url": "CRUD/crud_reports.php", 
            "method": 'POST', //usamos el metodo POST
            "data":{opcion:opcion,tipo: tipo,id:id}, //enviamos opcion 5 para EDITAR
            "dataSrc":"",
            'serverSide': "false",
        },
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        "columns":[
            {"data": "id"},
            {"data": "order_id"},
            {"data": "nombre"},
            {"data": "item"},
            {"data": "valor"},
            {"defaultContent": "<div class='text-center'><div class='btn-group'></button><button class='btn btn-danger btn-sm btnBorrarDetalles'><i class='material-icons'>delete</i></button></div></div>"}
        ]
    }); 

    $(".modal-header").css("background-color", "#007bff");
    $(".modal-header").css("color", "white" );
    $(".modal-title").text("Agregar Puntajes y Comentario");		
    $('#modalDetails').modal('show');		   
});

///////////////////CARGA EVALUADORES////
$.ajax({
    type: "POST",
    url: "CRUD/fv_fetchEvaluators.php",
    success: function(response)
    {
        $('#evaluador').html(response).fadeIn();
    }
  });

});   

function listar_reports(){

   opcion = 4;
   var fecha_ini =  $("#fecha_ini").val(); 
   var fecha_fin =  $("#fecha_fin").val();   
   var tipo = 0;//cero para trabajar con pedido general   
   var nuevo = document.getElementById("nuevo");

   if (tipo=="1"){
       nuevo.style.display="none";
   }else{
       nuevo.style.display="block";
   }

   var tableReports = $('#tableReports').DataTable({
       //responsive: true,
       "destroy": true,
       'processing': true,
       "destroy": true,  
       "ajax":{            
           "url": "CRUD/crud_reports.php", 
           "method": 'POST', //usamos el metodo POST
           "data":{opcion:opcion,fecha_ini:fecha_ini,fecha_fin:fecha_fin,tipo: tipo}, //enviamos opcion 4 para que haga un SELECT
           "dataSrc":"",
           'serverSide': "false",
       },
       "language": {
           "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
       },
       "columns":[
           {"data": "id"},
           {"data": "fecha"},
           {"data": "finca"},
           {"data": "area"},
           {"data": "flor"},
           {"data": "tipo_dato"},
           {"defaultContent": "<div class='text-center'><div class='btn-group'><button class='btn btn-success btn-sm btnDetalles'><i class='material-icons'>details</i></button><button class='btn btn-danger btn-sm btnBorrar'><i class='material-icons'>delete</i></button></div></div>"}//<button class='btn btn-primary btn-sm btnEditar'><i class='material-icons'>edit</i></button>
       ]
   }); 
   
   new $.fn.dataTable.FixedColumns( tableReports, {
    // options
    fixedColumns: {
        left: 2
    }
    } );
   
}