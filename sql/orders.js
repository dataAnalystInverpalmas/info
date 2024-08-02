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
   $('#formOrders').submit(function(e){                         
       e.preventDefault(); //evita el comportambiento normal del submit, es decir, recarga total de la página
       fecha = $.trim($('#fecha').val());    
       casa = $.trim($('#casa').val());
       pref_documento = $.trim($('#pref_documento').val());    
       n_documento = $.trim($('#n_documento').val());    
       var tipo = 0;//cero para trabajar con pedido general                             
           $.ajax({
             url: "CRUD/crud_orders.php",
             type: "POST",
             datatype:"json",    
             data:  {id:id, fecha:fecha, casa:casa, pref_documento:pref_documento, n_documento:n_documento,opcion:opcion, tipo:tipo},    
             success: function(data) {
               //tableOrders.ajax.reload(null, false);
               //listar();
               $('#tableOrders').DataTable().ajax.reload(null, false);
               console.log(data);
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
    origen_id = $.trim($('#origen_id').val());
    propagacion_id = $.trim($('#propagacion_id').val());
    variedad_id = $.trim($('#variedad_id').val());
    cantidad = $.trim($('#cantidad').val());  
    excedente = $.trim($('#excedente').val());  
    event.preventDefault();                         
        $.ajax({
          url: "CRUD/crud_orders.php",
          type: "POST",
          datatype:"json",    
          data:  {order_id:order_id, origen_id:origen_id,propagacion_id:propagacion_id,variedad_id:variedad_id,cantidad:cantidad,excedente:excedente,opcion:opcion, tipo:tipo},    
          success: function(data) {
            //tableOrders.ajax.reload(null, false);
            //listar();
            $('#tableDetails').DataTable().ajax.reload(null, false);
            console.log(data)
           }
        });			        
    //$('#modalCRUD').modal('hide');
    //$('#modalDetails').modal('hide');												     			
    });
    ///////////////////////fin de detalles/////////////// 
   //para limpiar los campos antes de dar de Alta una Persona
   $("#btnNuevo").click(function(){
       opcion = 1; //alta           
       id=null;
       $("#formOrders").trigger("reset");
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
       fecha = fila.find('td:eq(1)').text();
       casa = fila.find('td:eq(2)').text();
       pref_documento = fila.find('td:eq(3)').text();
       n_documento = fila.find('td:eq(4)').text();
   
       $("#fecha").val(fecha);
       $("#casa").val(casa);
       $("#pref_documento").val(pref_documento);
       $("#n_documento").val(n_documento);
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
             url: "CRUD/crud_orders.php",
             type: "POST",
             datatype:"json",    
             data:  {opcion:opcion, id:id},    
             success: function() {
                   //tableOrders.row(fila.parents('tr')).remove().draw();
                   $('#tableOrders').DataTable().ajax.reload(null, false);                  
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
          url: "CRUD/crud_orders.php",
          type: "POST",
          datatype:"json",    
          data:  {opcion:opcion, id:id, tipo:tipo},    
          success: function() {
                //tableOrders.row(fila.parents('tr')).remove().draw();
                $('#tableDetails').DataTable().ajax.reload(null, false);                  
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
        var tableDetails = $('#tableDetails').DataTable({
            dom: 'rtip',
            //responsive: true,
            "destroy": true,
            'processing': true,
            "destroy": true,  
            "ajax":{            
                "url": "CRUD/crud_orders.php", 
                "method": 'POST', //usamos el metodo POST
                "data":{opcion:opcion,tipo: tipo,id:id}, //enviamos opcion 4 para que haga un SELECT
                "dataSrc":"",
                'serverSide': "false",
            },
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            },
            "columns":[
                {"data": "id"},
                {"data": "order_id"},
                {"defaultContent": "<div class='text-center'><div class='btn-group'></button><button class='btn btn-danger btn-sm btnBorrarDetalles'><i class='material-icons'>delete</i></button></div></div>"}
            ]
        }); 

        $(".modal-header").css("background-color", "#007bff");
        $(".modal-header").css("color", "white" );
        $(".modal-title").text("Agregar Detalles a Pedido");		
        $('#modalDetails').modal('show');		   
    });
   
   });   
   
   function listar(){
   
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
   
       var tableOrders = $('#tableOrders').DataTable({
           //responsive: true,
           "destroy": true,
           'processing': true,
           "destroy": true,  
           "ajax":{            
               "url": "CRUD/crud_orders.php", 
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
               {"data": "casa"},
               {"data": "pref_documento"},
               {"data": "num_documento"},
               {"defaultContent": "<div class='text-center'><div class='btn-group'><button class='btn btn-primary btn-sm btnEditar'><i class='material-icons'>edit</i></button><button class='btn btn-success btn-sm btnDetalles'><i class='material-icons'>details</i></button><button class='btn btn-danger btn-sm btnBorrar'><i class='material-icons'>delete</i></button></div></div>"}
           ]
       });     
       
   }