//Código para Datables

//$('#example').DataTable(); //Para inicializar datatables de la manera más simple

$(document).ready(function() { 
  
    var tableBQ = $('#panelBQ').DataTable({
    //para cambiar el lenguaje a español
    select: true,
    dom: 'QfrtipB',
    buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ],
    searchPanes: {
        order: ['Finca','Bloque', 'Tabla', 'Nave'],
        layout: 'columns-1',
        cascadePanes: true,
        dtOpts: {
            dom: "tp",
            paging: true,
            pagingType: 'numbers',
            searching: true,
            select: {
                style: 'multi'
            }
        }
    },
    columnDefs: [
        {
            searchPanes: {
            },
            targets: [4]
        }
    ],
    responsive: true,
        dom: '<"dtsp-verticalContainer"<"dtsp-verticalPanes"P><"dtsp-dataTable"frtip>>',
        pageLength: 20,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sSearch": "Buscar:",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast":"Último",
                "sNext":"Siguiente",
                "sPrevious": "Anterior"
			},
			"sProcessing":"Procesando...",
        },    
    });
    //para llevar datos con ajax

    /*$('button').click( function() {
            var data = table.$('input, select').serialize();
        alert(
            data.substr( 0, 120 )+'...'
            )
        } );*/
});