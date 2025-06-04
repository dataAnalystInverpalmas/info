$(document).ready(function(){
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
        document.getElementById('finicial').value=ano+"-"+mes_i+"-"+dia_i;
        document.getElementById('ffinal').value=ano+"-"+mes+"-"+dia;
    /////////////////////fechas///////////////////////////////////////        
            ///////////////////CARGA COMBO FLOR////
            $.ajax({
            type: "POST",
            url: "CRUD/fv_fetchProducts.php",
            data: {},
            success: function(response)
            {
                $('#nflor').html(response).fadeIn();
            }
            });
            ////////////////////////////////////////////
    
        listar();
    

    });
         ///////////////REPORTE//////////////////////
         var listar = function(){ 
            var flor = $("#nflor").val();
            var fecha_ini =  $("#finicial").val(); 
            var fecha_fin =  $("#ffinal").val(); 
            var tabla = $('#rFloreros').DataTable({
                "order": [[ 0, "desc" ]],
                "scrollY": "300px",
                "scrollCollapse": true,
                 "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
 
                    // converting to interger to find total
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
         
                    // computing column Total of the complete result 
                    var monTotal = api
                        .column( 6 , { page: 'current'})
                        .data()
                        .reduce( function (a, b, _,{ length }) {
                            return Math.round(intVal(a) + intVal(b) / length);
                        }, 0 );
                        
                    var tueTotal = api
                        .column( 7 , { page: 'current'})
                        .data()
                        .reduce( function (a, b, _,{ length }) {
                            return Math.round(intVal(a) + intVal(b) / length) ;
                        }, 0 );
                        
                    // Update footer by showing the total with the reference of the column index 
                $( api.column( 0 ).footer() ).html('Total');
                    $( api.column( 6 ).footer() ).html(monTotal);
                    $( api.column( 7 ).footer() ).html(tueTotal);
                }, 
                'scrollX': true,
                'processing': true,
                'serverSide': false,
                'serverMethod': 'post',
                dom: 'Blfrtip',
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
                },
                buttons: [
                    'excelHtml5','pdfHtml5','copyHtml5'
                ],
                "destroy": true,
                "ajax": {
                    "url": "CRUD/fv_reportData.php",
                    "dataSrc": "",
                    "method": "post",
                    "data": function(data) {
                            // Read values
                           data.flor = flor;
                           data.fini = fecha_ini;
                           data.ffin = fecha_fin;
                    }
                },
                "columns": [
                    {"data": "id"},
                    {"data": "variedad"},
                    {"data": "origen"},
                    {"data": "tipo"},
                    {"data": "grupo_descripcion"},
                    {"data": "viaje"},
                    {"data": "granel"},
                    {"data": "puntaje"},
                    {"data": "vida"},
                    {"data": "CONSISTENCIA_COLOR"},
                    {"data": "PETALOS"},
                    {"data": "VELOCIDAD_APERTURA"},
                    {"data": "FORMA_APERTURA"},
                    {"data": "TALLO"},
                    {"data": "FOLLAJE"},
                    {"data": "FINAL_OPTIMO"},
                    {"data": "NO_CONFORME"},
                    {"data": "inconformidad_florero"},
                    {"data": "observacion"},
                    {"data": "fecha_florero"}
                ],
                "columnDefs": [
                    { "searchable": false, "targets": 10 }
                  ]
            });
            /////////////////////////////////////////////////////
            $('#rFloreros tbody').on('click', 'tr', function () {
                var data = tabla.row( this ).data();
            } );
        }