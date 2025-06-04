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
//////////////////////////////////////////////////////////////
 document.getElementById('fecha_fin').value=ano+"-"+mes+"-"+dia;
//////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////     
 document.getElementById('fecha_ini').value=ano+"-"+mes+"-"+dia;
/////////////////////fechas///////////////////////////////////////  

var id, opcion;

listar();

var fila; //captura la fila, para editar o eliminar
//submit para el Alta y Actualización
$('#formLabors').submit(function(e){                         
    e.preventDefault(); //evita el comportambiento normal del submit, es decir, recarga total de la página
    variedad = $.trim($('#variedad').val());    
    temporada_obj = $.trim($('#temporada_obj').val());
    fecha_ensarte_r = $.trim($('#fecha_ensarte_r').val());    
    esquejes_ensarte = $.trim($('#esquejes_ensarte').val());    
    fecha_cosecha_r = $.trim($('#fecha_cosecha_r').val());    
    esquejes_cosecha = $.trim($('#esquejes_cosecha').val());
    banco = $.trim($('#banco').val());

    var tipo= $("#tipo").val();                             
        $.ajax({
          url: "CRUD/crud.php",
          type: "POST",
          datatype:"json",    
          data:  {id:id, variedad:variedad, temporada_obj:temporada_obj, fecha_ensarte_r:fecha_ensarte_r, esquejes_ensarte:esquejes_ensarte, fecha_cosecha_r:fecha_cosecha_r ,esquejes_cosecha:esquejes_cosecha,opcion:opcion, tipo:tipo, banco:banco},    
          success: function(data) {
            //tablaLabores.ajax.reload(null, false);
            //listar();
            console.log('Respuesta correcta:', response);
            $('#tablaLabores').DataTable().ajax.reload(null, false);
           }
        });			        
    $('#modalCRUD').modal('hide');											     			
});

//para limpiar los campos antes de dar de Alta una Persona
$("#btnNuevo").click(function(){
    opcion = 1; //alta           
    id=null;
    $("#formLabors").trigger("reset");
    $(".modal-header").css( "background-color", "#17a2b8");
    $(".modal-header").css( "color", "white" );
    $(".modal-title").text("Alta de Labor");
    $('#modalCRUD').modal('show');	    
});

//Editar        
$(document).on("click", ".btnEditar", function(){		        
    opcion = 2;//editar
    fila = $(this).closest("tr");	        
    id = parseInt(fila.find('td:eq(0)').text()); //capturo el ID		            
    variedad = fila.find('td:eq(1)').text();
    temporada_obj = fila.find('td:eq(2)').text();
    fecha_ensarte_r = fila.find('td:eq(3)').text();
    esquejes_ensarte = fila.find('td:eq(4)').text();
    fecha_cosecha_r = fila.find('td:eq(5)').text();
    esquejes_cosecha = fila.find('td:eq(6)').text();
    banco = fila.find('td:eq(7)').text();

    $("#variedad").val(variedad);
    $("#temporada_obj").val(temporada_obj);
    $("#fecha_ensarte_r").val(fecha_ensarte_r);
    $("#esquejes_ensarte").val(esquejes_ensarte);
    $("#fecha_cosecha_r").val(fecha_cosecha_r);
    $("#esquejes_cosecha").val(esquejes_cosecha);
    $("#banco").val(banco);

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
          url: "CRUD/crud.php",
          type: "POST",
          datatype:"json",    
          data:  {opcion:opcion, id:id},    
          success: function() {
                //tablaLabores.row(fila.parents('tr')).remove().draw();
                $('#tablaLabores').DataTable().ajax.reload(null, false);
                console.log('Respuesta correcta:', response);                  
           }
        });	
    }
 }); 

});   

function listar(){

    opcion = 4;
    var fecha_ini =  $("#fecha_ini").val(); 
    var fecha_fin =  $("#fecha_fin").val();   
    var tipo= $("#tipo").val();
    var nuevo = document.getElementById("nuevo");
    if (tipo=="0"){
        nuevo.style.display="none";
    }else{
        nuevo.style.display="block";
    }

    var tablaLabores = $('#tablaLabores').DataTable({
        //responsive: true,
        "destroy": true,
        'processing': true,
        "destroy": true,  
        "ajax":{            
            "url": "CRUD/crud.php", 
            "method": 'POST', //usamos el metodo POST
            "data":{opcion:opcion,fecha_ini:fecha_ini,fecha_fin:fecha_fin,tipo:tipo}, //enviamos opcion 4 para que haga un SELECT
            "dataSrc":"",
            'serverSide': "false",
        },
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        "columns":[
            {"data": "id"},
            {"data": "variedad"},
            {"data": "temporada_obj"},
            {"data": "fecha_ensarte_r"},
            {"data": "esquejes_ensarte"},
            {"data": "fecha_cosecha_r"},
            {"data": "esquejes_cosecha"},
            {"data": "banco"},
            {"data": "plantas"},
            {"defaultContent": "<div class='text-center'><div class='btn-group'><button class='btn btn-primary btn-sm btnEditar'><i class='material-icons'>edit</i></button><button class='btn btn-danger btn-sm btnBorrar'><i class='material-icons'>delete</i></button></div></div>"}
        ]
    });     
    
}