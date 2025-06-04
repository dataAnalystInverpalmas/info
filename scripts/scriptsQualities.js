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
    /////////////////////fechas///////////////////////////////////
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

listarCalidades();
        
});


      ///////////////////////////////////////////////////////////////
var listarCalidades = function(){ 
    var flor = $("#nflor").val();
    var fecha_ini =  $("#finicial").val(); 
    var fecha_fin =  $("#ffinal").val(); 
    var tabla = $('#rCalidades').DataTable({
            "order": [[ 0, "asc" ]],
            "scrollY": "300px",
            "scrollCollapse": true,
            'scrollX': true,
            'processing': true,
            'serverSide': false,
            'serverMethod': 'post',
        dom: 'Blfrtip',
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        buttons: [
            'excelHtml5',
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                pageSize: 'LETTER'
            },
            'copyHtml5'
        ],
        "destroy": true,
        "ajax": {
            "url": "CRUD/qualitiesData.php",
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
            {"data": "nombre"},
            {"data": "SEL"},
            {"data": "FAN"},
            {"data": "STD"},
            {"data": "SHR"},
            {"data": "NAL"},
            {"data": "causas"},
            {"data": "no_selecto"},
        ]
        });
    }