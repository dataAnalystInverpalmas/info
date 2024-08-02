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
    document.getElementById('finicialProy').value=ano+"-"+mes_i+"-"+dia_i;
    document.getElementById('ffinalProy').value=ano+"-"+mes+"-"+dia;
    /////////////////////fechas///////////////////////////////////////        
    listarProy();

     ///////////////////CARGA COMBO tipo////
     $.ajax({
        type: "POST",
        url: "CRUD/proy_kind.php",
        data: {},
        success: function(response)
        {
            $('#proy_year').html(response).fadeIn();
        }
        });
        ////////////////////////////////////////////    

});

var listarProy = function(){ 
    var fecha_ini =  $("#finicialProy").val(); 
    var fecha_fin =  $("#ffinalProy").val(); 
    var tipo = $("#proy_year").val();

    var tabla = $('#table_proy_anual').DataTable({
        "order": [[ 6, "asc" ]],
        "scrollY": "300px",
        "scrollCollapse": true,
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            var ncol = 8
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
            $( api.column( ncol ).footer() ).html(
                ''+pageTotal/960 +' de '+ total/960 +' '
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
            "url": "CRUD/pto_proy.php",
            "dataSrc": "",
            "method": "post",
            "datetype": "json",
            'serverSide': "false",
            "data": function(data) {
                    // Read values
                   data.fini = fecha_ini;
                   data.ffin = fecha_fin;
                   data.tipo = tipo;
            },
            "error": function( jqXHR, textStatus, errorThrown ) {
                if ( console && console.log ) {
                    console.log( "La solicitud a fallado: " +  textStatus + jqXHR);
                }
            }
        }
        ,
        "cache": false,
        "columns": [
            {"data": "finca"},
            {"data": "bloque"},
            {"data": "producto"},
            {"data": "variedad"},
            {"data": "temporada"},
            {"data": "ciclo"},
            {"data": "fecha_siembra"},
            {"data": "tipo"},
            {"data": "plantas"},
            {"data": "codvari"},
            {"data": "cod_temporada"},
            {"data": "siembra"},
            {"data": "color"}
        ]
    });
}