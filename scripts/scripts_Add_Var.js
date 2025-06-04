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
        document.getElementById('finicialAV').value=ano+"-"+mes_i+"-"+dia_i;
        document.getElementById('ffinalAV').value=ano+"-"+mes+"-"+dia;
    /////////////////////fechas///////////////////////////////////////        
            ///////////////////CARGA COMBO FLOR////
            $.ajax({
            type: "POST",
            url: "CRUD/fv_fetchProducts.php",
            data: {},
            success: function(response)
            {
                $('#nameflor').html(response).fadeIn();
            }
            });
            ////////////////////////////////////////////
            ///////////////////CARGA COMBO FLOR////
            $.ajax({
            type: "POST",
            url: "CRUD/fetchFarms.php",
            data: {},
            success: function(response)
            {
                $('#namefinca').html(response).fadeIn();
            }
            });
            ////////////////////////////////////////////
    
        listarAV();
           
    });
         ///////////////REPORTE//////////////////////
         var listarAV = function(){ 
            var flor = $("#nameflor").val();
            var finca = $("#namefinca").val();
            var fecha_ini =  $("#finicialAV").val(); 
            var fecha_fin =  $("#ffinalAV").val(); 
    
            var tabla = $('#rAddVarieties').DataTable({
                //responsive: true,
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
                    "url": "CRUD/av_fetchData.php",
                    "error": function(jqXHR, ajaxOptions, thrownError) {
                      console.log(thrownError + "\r\n" + jqXHR.statusText + "\r\n" + jqXHR.responseText + "\r\n" + ajaxOptions.responseText);
                    },
                    "dataSrc": "",
                    "method": "post",
                    "data": function(data) {
                            // Read values
                           data.flor = flor;
                           data.finca = finca;
                           data.fini = fecha_ini;
                           data.ffin = fecha_fin;
                    }
                },
                cache: false,
                "columns": [
                    {"data": "finca"},
                    {"data": "bloque"},
                    {"data": "variedad"},
                    {"data": "temporada"},
                    {"data": "fecha_siembra"},
                    {"data": "nfecha"},
                    {"data": "plantas"},
                    {"data": "nplantas"},
                    {"data": "porcentaje"},
                    {"data": "sem"}
                ]
            });
        }