/* $(document).ready( function () {
    $('#table_id').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        }
    });
}); */

$(document).ready(function() {
    $('#table_covid').DataTable( {
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                parseInt(pageTotal) 
            );
        },
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        "order": [[ 4, "desc" ]]
    } );
} );

//funcion para tabla de no registros
$(document).ready(function() {
    $('#table_covid_without').DataTable( {
        "footerCallback": function ( row, data, start, end, display1 ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 3 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 3 ).footer() ).html(
                parseInt(pageTotal) 
            );
        },
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        "order": [[ 3, "desc" ]]
    } );
} );

//funcion para tabla de variedades adicionales
$(document).ready(function() {
    $('#table_add_varieties').DataTable( {
        "footerCallback": function ( row, data, start, end, display1 ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 8 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 8, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 8 ).footer() ).html(
                parseInt(pageTotal) 
            );
        },
        "language": {
            "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
        },
        "order": [[ 1, "asc" ]]
    } );
} );

//para talba de pto
$(document).ready(function() {
    $('#table_pto').DataTable( {
        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 2 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 2, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 2 ).footer() ).html(
                'Camas '+pageTotal +' ( '+ total +' total)'
            );
        }
    } );
} );


$(document).ready(function() {
    $('#example').DataTable( {
 
    } );
} );

$(document).ready(function() {
    $('#cosechas').DataTable( {
        "paging":   false,
        "info":     false,
        order: [[0, 'asc']],
        rowGroup: {
            startRender: null,
            endRender: function ( rows, group ) {
 
                var BSum = rows
                    .data()
                    .pluck(5)
                    .reduce( function (a, b) {
                        return Math.round(a + b*1);
                    }, 0);
 
                return $('<tr/>')
                    .append( '<td class="font-weight-bold bolded" colspan="5">Total Bloque: '+group+'</td>' )
                    .append( '<td class="font-weight-bold bolded">'+BSum.toFixed(1)+'</td>' )
                    .append( '<td/>' )
                    .append( '<td colspan="7">'+'</td>');
            },
            dataSrc: 0
        }
    } );
} );
