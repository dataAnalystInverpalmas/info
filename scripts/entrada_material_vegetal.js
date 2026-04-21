$(document).ready(function () {

    /* ---------------------------------------------------------------
       Fechas por defecto (01-enero del año actual → hoy)
    --------------------------------------------------------------- */
    var hoy  = new Date();
    var mes  = String(hoy.getMonth() + 1).padStart(2, '0');
    var dia  = String(hoy.getDate()).padStart(2, '0');
    var ano  = hoy.getFullYear();
    $('#emv_fecha_ini').val(ano + '-01-01');
    $('#emv_fecha_fin').val(ano + '-' + mes + '-' + dia);

    /* ---------------------------------------------------------------
       Poblar select de proveedores (breeders)
    --------------------------------------------------------------- */
    $.post('CRUD/crud_emv.php', { opcion: 'breeders' }, function (data) {
        var $sel = $('#emv_proveedor');
        $sel.empty().append('<option value="">— seleccione —</option>');
        $.each(data, function (i, nombre) {
            $sel.append($('<option>').val(nombre).text(nombre));
        });
    }, 'json');

    /* ---------------------------------------------------------------
       Poblar datalist de variedades
    --------------------------------------------------------------- */
    $.post('CRUD/crud_emv.php', { opcion: 'variedades' }, function (data) {
        var $list = $('#listVariedades').empty();
        $.each(data, function (i, nombre) {
            $list.append($('<option>').val(nombre));
        });
    }, 'json');

    /* ---------------------------------------------------------------
       Inicializar DataTable principal
    --------------------------------------------------------------- */
    var tableEmv = $('#tableEmv').DataTable({
        processing: true,
        ajax: {
            url: 'CRUD/crud_emv.php',
            type: 'POST',
            data: function () {
                return {
                    opcion: '4',
                    fecha_ini: $('#emv_fecha_ini').val(),
                    fecha_fin: $('#emv_fecha_fin').val()
                };
            },
            dataSrc: ''
        },
        columns: [
            { data: 'id',        title: 'Id' },
            { data: 'fecha',     title: 'Fecha' },
            { data: 'maquila',   title: 'Maquila' },
            { data: 'proveedor', title: 'Proveedor' },
            { data: 'remision',  title: 'Remisión' },
            { data: 'destino',   title: 'Destino' },
            { data: 'material',  title: 'Material' },
            {
                data: null,
                title: 'Acciones',
                orderable: false,
                render: function (d, t, row) {
                    return '<button class="btn btn-sm btn-info btnEmvDetalles mr-1" data-id="' + row.id + '" title="Detalles">'
                         + '<i class="material-icons" style="font-size:16px;vertical-align:middle">list_alt</i></button>'
                         + '<button class="btn btn-sm btn-warning btnEmvEditar mr-1" data-id="' + row.id + '" title="Editar">'
                         + '<i class="material-icons" style="font-size:16px;vertical-align:middle">edit</i></button>'
                         + '<button class="btn btn-sm btn-danger btnEmvBorrar" data-id="' + row.id + '" title="Borrar">'
                         + '<i class="material-icons" style="font-size:16px;vertical-align:middle">delete</i></button>';
                }
            }
        ]
    });

    /* ---------------------------------------------------------------
       Función pública para recargar la tabla principal (usada por el botón Consultar)
    --------------------------------------------------------------- */
    window.emvListar = function () {
        tableEmv.ajax.reload(null, false);
    };

    /* ---------------------------------------------------------------
       Estado del formulario cabecera
    --------------------------------------------------------------- */
    var opcionEmv = '1';
    var nuevoEntradaId = null;

    function abrirModalCRUD(titulo, colorHeader) {
        $('#modalEmvCRUDTitle').text(titulo);
        $('#modalEmvCRUD .modal-header')
            .removeClass('bg-info bg-primary bg-warning')
            .addClass(colorHeader);
        $('#btnEmvVerDetalles').hide();
        nuevoEntradaId = null;
        $('#modalEmvCRUD').modal('show');
    }

    /* Nuevo */
    $('#btnEmvNuevo').on('click', function () {
        opcionEmv = '1';
        $('#formEmv')[0].reset();
        $('#emv_id').val('');
        abrirModalCRUD('Nueva Entrada de Material Vegetal', 'bg-info');
    });

    /* Guardar (insert / update) */
    $('#formEmv').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'CRUD/crud_emv.php',
            type: 'POST',
            dataType: 'json',
            data: {
                opcion:     opcionEmv,
                id:         $('#emv_id').val(),
                fecha:      $('#emv_fecha').val(),
                maquila:    $('#emv_maquila').val(),
                proveedor:  $('#emv_proveedor').val(),
                remision:   $('#emv_remision').val(),
                destino:    $('#emv_destino').val(),
                material:   $('#emv_material').val()
            },
            success: function (data) {
                tableEmv.ajax.reload(null, false);
                if (opcionEmv === '1' && data.id) {
                    nuevoEntradaId = data.id;
                    $('#btnEmvVerDetalles')
                        .data('id', data.id)
                        .show();
                    $('#modalEmvCRUDTitle').text('Entrada #' + data.id + ' guardada');
                } else {
                    $('#modalEmvCRUD').modal('hide');
                }
            },
            error: function () {
                alert('Error al guardar. Revise la consola del servidor.');
            }
        });
    });

    /* Botón "Ir a Detalles" (aparece tras crear) */
    $('#btnEmvVerDetalles').on('click', function () {
        var id = $(this).data('id');
        $('#modalEmvCRUD').modal('hide');
        // pequeño retardo para que el modal se cierre primero
        setTimeout(function () { abrirDetalles(id); }, 350);
    });

    /* Editar */
    $(document).on('click', '.btnEmvEditar', function () {
        opcionEmv = '2';
        var tr  = $(this).closest('tr');
        var row = tableEmv.row(tr).data();
        $('#emv_id').val(row.id);
        $('#emv_fecha').val(row.fecha);
        $('#emv_maquila').val(row.maquila);
        $('#emv_proveedor').val(row.proveedor);
        $('#emv_remision').val(row.remision);
        $('#emv_destino').val(row.destino);
        $('#emv_material').val(row.material);
        abrirModalCRUD('Editar Entrada #' + row.id, 'bg-warning');
    });

    /* Borrar */
    $(document).on('click', '.btnEmvBorrar', function () {
        var id = $(this).data('id');
        if (!confirm('¿Eliminar la entrada #' + id + ' y todos sus detalles?')) return;
        $.post('CRUD/crud_emv.php', { opcion: '3', id: id }, function () {
            tableEmv.ajax.reload(null, false);
        }, 'json');
    });

    /* Abrir modal de detalles */
    $(document).on('click', '.btnEmvDetalles', function () {
        abrirDetalles($(this).data('id'));
    });

    /* ---------------------------------------------------------------
       DataTable de detalles (se inicializa la primera vez)
    --------------------------------------------------------------- */
    var tableDetalles = null;

    function abrirDetalles(id) {
        $('#det_entrada_id').val(id);
        $('#modalEmvDetallesTitle').text('Detalles — Entrada #' + id);
        $('#formEmvDetalle')[0].reset();
        resetSumaCampos();

        if (tableDetalles && $.fn.DataTable.isDataTable('#tableEmvDetalles')) {
            tableDetalles.ajax.reload(null, false);
        } else {
            tableDetalles = $('#tableEmvDetalles').DataTable({
                processing: true,
                ajax: {
                    url: 'CRUD/crud_emv.php',
                    type: 'POST',
                    data: function () {
                        return { opcion: '5', entrada_id: $('#det_entrada_id').val() };
                    },
                    dataSrc: ''
                },
                columns: [
                    { data: 'id',                title: 'Id' },
                    { data: 'variedad',          title: 'Variedad' },
                    { data: 'cantidad_recibida', title: 'Cant. Recibida' },
                    { data: 'facturado',         title: 'Facturado' },
                    { data: 'reposicion',        title: 'Reposición' },
                    { data: 'excedente',         title: 'Excedente' },
                    { data: 'obsequio',          title: 'Obsequio' },
                    { data: 'adicional',         title: 'Adicional' },
                    {
                        data: 'raiz', title: 'Raíz',
                        render: function (v) { return v == 1 ? 'Con raíz' : 'Sin raíz'; }
                    },
                    { data: 'observacion', title: 'Observación', defaultContent: '' },
                    {
                        data: null, title: 'Acción', orderable: false,
                        render: function (d, t, row) {
                            return '<button class="btn btn-sm btn-danger btnEmvBorrarDet" data-id="' + row.id + '" title="Eliminar">'
                                 + '<i class="material-icons" style="font-size:16px;vertical-align:middle">delete</i></button>';
                        }
                    }
                ]
            });
        }

        $('#modalEmvDetalles').modal('show');
    }

    /* ---------------------------------------------------------------
       Auto-cálculo de cantidad_recibida
    --------------------------------------------------------------- */
    function calcularTotal() {
        var total = (parseInt($('#det_facturado').val())  || 0)
                  + (parseInt($('#det_reposicion').val()) || 0)
                  + (parseInt($('#det_excedente').val())  || 0)
                  + (parseInt($('#det_obsequio').val())   || 0)
                  + (parseInt($('#det_adicional').val())  || 0);
        $('#det_cantidad_recibida').val(total);
    }

    function resetSumaCampos() {
        $('#det_facturado, #det_reposicion, #det_excedente, #det_obsequio, #det_adicional').val(0);
        $('#det_cantidad_recibida').val(0);
        $('#det_raiz').prop('checked', false);
    }

    $(document).on('input', '.emv-suma', calcularTotal);

    /* ---------------------------------------------------------------
       Guardar nuevo detalle
    --------------------------------------------------------------- */
    $('#formEmvDetalle').on('submit', function (e) {
        e.preventDefault();

        var facturado  = parseInt($('#det_facturado').val())  || 0;
        var reposicion = parseInt($('#det_reposicion').val()) || 0;
        var excedente  = parseInt($('#det_excedente').val())  || 0;
        var obsequio   = parseInt($('#det_obsequio').val())   || 0;
        var adicional  = parseInt($('#det_adicional').val())  || 0;
        var suma       = facturado + reposicion + excedente + obsequio + adicional;
        var recibida   = parseInt($('#det_cantidad_recibida').val()) || 0;

        if (recibida !== suma) {
            alert('La cantidad recibida (' + recibida + ') no coincide con la suma (' + suma + ').\n'
                + 'Revise los valores de facturado, reposición, excedente, obsequio y adicional.');
            return;
        }

        if ($('#det_variedad').val().trim() === '') {
            alert('Seleccione una variedad.');
            return;
        }

        $.ajax({
            url: 'CRUD/crud_emv.php',
            type: 'POST',
            dataType: 'json',
            data: {
                opcion:     '7',
                entrada_id: $('#det_entrada_id').val(),
                variedad:   $('#det_variedad').val().trim(),
                facturado:  facturado,
                reposicion: reposicion,
                excedente:  excedente,
                obsequio:   obsequio,
                adicional:  adicional,
                raiz:       $('#det_raiz').is(':checked') ? 1 : 0,
                observacion: $('#det_observacion').val()
            },
            success: function () {
                if (tableDetalles) tableDetalles.ajax.reload(null, false);
                $('#formEmvDetalle')[0].reset();
                resetSumaCampos();
                $('#det_variedad').focus();
            },
            error: function () {
                alert('Error al guardar el detalle.');
            }
        });
    });

    /* ---------------------------------------------------------------
       Borrar detalle
    --------------------------------------------------------------- */
    $(document).on('click', '.btnEmvBorrarDet', function () {
        var id = $(this).data('id');
        if (!confirm('¿Eliminar el detalle #' + id + '?')) return;
        $.post('CRUD/crud_emv.php', { opcion: '6', id: id }, function () {
            if (tableDetalles) tableDetalles.ajax.reload(null, false);
        }, 'json');
    });

});
