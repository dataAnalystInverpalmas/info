
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
        document.getElementById('finicialPTO').value=ano+"-"+mes_i+"-"+dia_i;
        document.getElementById('ffinalPTO').value=ano+"-"+mes+"-"+dia;
    /////////////////////fechas///////////////////////////////////////        
            ///////////////////CARGA COMBO FLOR////
            $.ajax({
            type: "POST",
            url: "CRUD/pto_y.php",
            data: {},
            success: function(response)
            {
                $('#pto_year').html(response).fadeIn();
            }
            });
            ////////////////////////////////////////////
            ///////////////////CARGA COMBO FLOR////
            $.ajax({
                type: "POST",
                url: "CRUD/pto_casa.php",
                data: {},
                success: function(response)
                {
                    $('#pto_casa').html(response).fadeIn();
                }
            });
            ////////////////////////////////////////////
    
    listarPTO();
           
});
/////////////////REPORTE//////////////////////
var listarPTO = function(){ 
    //var pto_y = $("#pto_y").val();
    var fecha_ini =  $("#finicialPTO").val(); 
    var fecha_fin =  $("#ffinalPTO").val(); 
    var year_p = $("#pto_year").val();
    var casa = $("#pto_casa").val();
    var tabla = $('#table_pto_anual').DataTable({
        "order": [[ 4, "asc" ]],
        "scrollY": "300px",
        "scrollCollapse": true,
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            var ncol = 11
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( ncol )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( ncol, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( ncol  ).footer() ).html(
                pageTotal +' de '+ total
            );
        },
        //responsive: true,
        "destroy": true,
        'scrollX': true,
        'processing': true,
        dom: 'Blfrtip',
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        buttons: [
            'excelHtml5','pdfHtml5'
        ],
        "destroy": true,
        "ajax": {
            "url": "CRUD/pto.php",
            "dataSrc": "",
            "method": "post",
            "datetype": "json",
            'serverSide': "false",
            "data": function(data) {
                    // Read values
                   data.fini = fecha_ini;
                   data.ffin = fecha_fin;
                   data.year_p = year_p;
                   data.casa = casa;
            },
            "error": function( jqXHR, textStatus, errorThrown ) {
                if ( console && console.log ) {
                    console.log( "La solicitud a fallado: " +  textStatus);
                }
            }
        }
        ,
        "cache": false,
        "columns": [
            {"data": "producto"},
            {"data": "variedad"},
            {"data": "temporada_obj"},
            {"data": "ciclo"},
            {"data": "fecha_siembra"},
            {"data": "fecha_ensarte"},
            {"data": "fecha_cosecha"},
            {"data": "fecha_pico"},
            {"data": "casa"},
            {"data": "tipo"},
            {"data": "programa"},
            {"data": "raiz"},
            {"data": "plantas"}
        ]
    });
}