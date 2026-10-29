$(document).ready(function(){
    function resolveUrl(path) {
        if (typeof window.getEndpointUrl === 'function') {
            return window.getEndpointUrl(path);
        }
        if (!path) return '';
        if (/^https?:\/\//i.test(path)) return path;
        var clean = String(path).replace(/^(\.\.\/|\/)+/, '');
        var m = window.location.pathname.match(/^(.*\/info)(?:\/.*)?$/i);
        var base = m ? m[1] : '';
        return (base ? base.replace(/\/+$/, '') + '/' : '') + clean;
    }

    function notify(msg, type, title) {
        if (typeof window.showAppToast === 'function') {
            window.showAppToast(msg, type, title);
        } else {
            alert(msg);
        }
    }

    function showModal(selector) {
        var el = document.querySelector(selector);
        if (el && window.bootstrap && window.bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(el).show();
        } else {
            $(selector).modal('show');
        }
    }

    function hideModal(selector) {
        var el = document.querySelector(selector);
        if (el && window.bootstrap && window.bootstrap.Modal) {
            var inst = bootstrap.Modal.getInstance(el);
            if (inst) inst.hide();
            else $(selector).modal('hide');
        } else {
            $(selector).modal('hide');
        }
    }

    function toInt(value){
        var n = parseInt(value, 10);
        return isNaN(n) ? 0 : n;
    }

    // Toggle Sidebar
    $('#btnToggleSidebar').on('click', function () {
        var $sidebar = $('#crudSidebarCol');
        var $main = $('#crudMainCol');
        var $btnText = $('#btnToggleSidebarText');

        if ($sidebar.is(':visible')) {
            $sidebar.hide();
            $main.removeClass('col-md-8 col-lg-9').addClass('col-12');
            $btnText.text('Mostrar filtros');
            $(this).find('.material-icons').text('filter_list');
        } else {
            $sidebar.show();
            $main.removeClass('col-12').addClass('col-md-8 col-lg-9');
            $btnText.text('Ocultar filtros');
            $(this).find('.material-icons').text('filter_list_off');
        }

        if (arrangementTable) {
            setTimeout(function() {
                arrangementTable.columns.adjust().draw();
            }, 200);
        }
    });

    var arrangementTable = $('#arrangementTable').DataTable({
        ajax: {
            url: resolveUrl('ajax/arrangement_list.php'),
            data: function(d){
                d.tipo = $('#f_aa_tipo').val() || '';
                d.aplicar = $('#f_aa_aplicar').val() || '';
            }
        },
        columns: [
            { 
                data: 'id', 
                defaultContent: '',
                render: function(data) {
                    return '<span class="badge-id">#' + (data || '') + '</span>';
                }
            },
            { data: 'tipo', defaultContent: '' },
            { data: 'aplicar', defaultContent: '' },
            { 
                data: 'seccion', 
                defaultContent: '',
                render: function(data) {
                    return '<span class="text-secondary font-weight-bold">' + (data != null ? data : '') + '</span>';
                }
            },
            { 
                data: 'orden', 
                defaultContent: '',
                render: function(data) {
                    return '<span class="badge badge-light border px-2 py-1">' + (data != null ? data : '') + '</span>';
                }
            },
            { 
                data: 'calc_conciclo', 
                className: 'text-center',
                defaultContent: '',
                render: function(data) {
                    if (parseInt(data, 10) === 1) {
                        return '<span class="badge badge-success px-2 py-1"><span class="material-icons align-middle" style="font-size:0.9rem;">check</span> Sí</span>';
                    }
                    return '<span class="badge badge-light text-muted border px-2 py-1">No</span>';
                }
            },
            { 
                data: null, 
                orderable: false,
                className: 'text-center',
                render: function(data, type, row){
                    var rowData = encodeURIComponent(JSON.stringify(row));
                    return '<div class="action-btn-group">' +
                           '<button type="button" class="btn btn-action-edit btn-edit-aa" data-row="' + rowData + '" title="Editar registro">' +
                           '<span class="material-icons">edit</span> <span>Editar</span>' +
                           '</button>' +
                           '<button type="button" class="btn btn-action-delete btn-delete-aa" data-row="' + rowData + '" title="Eliminar registro">' +
                           '<span class="material-icons">delete_outline</span> <span>Eliminar</span>' +
                           '</button>' +
                           '</div>';
                }
            }
        ],
        language: {
            processing:     "Cargando datos...",
            search:         "Buscar en tabla:",
            searchPlaceholder: "Escriba para filtrar...",
            lengthMenu:     "Mostrar _MENU_ registros",
            info:           "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty:      "Mostrando 0 a 0 de 0 registros",
            infoFiltered:   "(filtrado de _MAX_ registros totales)",
            loadingRecords: "Cargando registros...",
            zeroRecords:    "No se encontraron registros coincidentes",
            emptyTable:     "No hay datos disponibles en esta tabla",
            paginate: {
                first:      "Primero",
                previous:   "Anterior",
                next:       "Siguiente",
                last:       "Último"
            }
        },
        pageLength: 15,
        lengthMenu: [10, 15, 25, 50, 100],
        responsive: true
    });

    $('#btnFilterArrangement').on('click', function(){ arrangementTable.ajax.reload(); });
    $('#btnClearArrangement').on('click', function(){
        $('#f_aa_tipo').val('');
        $('#f_aa_aplicar').val('');
        arrangementTable.ajax.reload();
    });

    $('#btnNewArrangement').on('click', function(){
        $('#arrangementForm')[0].reset();
        $('#aa_id').val('');
        $('#aa_old_tipo').val('');
        $('#aa_old_aplicar').val('');
        $('#aa_calc_conciclo').val('0');

        $('#modalArrangementTitle').text('Nuevo Registro Arrangement');
        $('#modalArrangementSubtitle').text('Completar campos para definir un nuevo tipo y aplicación');
        $('#modalArrangementIcon').text('add_circle');
        showModal('#arrangementModal');
    });

    $('#arrangementTable').on('click', '.btn-edit-aa', function(){
        var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
        $('#aa_id').val(row.id || '');
        $('#aa_tipo').val(row.tipo || '');
        $('#aa_aplicar').val(row.aplicar || '');
        $('#aa_seccion').val(row.seccion || '');
        $('#aa_orden').val(row.orden || '');
        $('#aa_calc_conciclo').val(row.calc_conciclo != null ? String(row.calc_conciclo) : '0');

        $('#aa_old_tipo').val(row.tipo || '');
        $('#aa_old_aplicar').val(row.aplicar || '');

        $('#modalArrangementTitle').text('Editar Arrangement #' + (row.id || ''));
        $('#modalArrangementSubtitle').text('Modifica los valores configurados para este tipo');
        $('#modalArrangementIcon').text('edit_note');
        showModal('#arrangementModal');
    });

    $('#saveArrangement').on('click', function(){
        var payload = {
            id: $('#aa_id').val(),
            tipo: ($('#aa_tipo').val() || '').trim(),
            aplicar: ($('#aa_aplicar').val() || '').trim(),
            seccion: toInt($('#aa_seccion').val()),
            orden: toInt($('#aa_orden').val()),
            calc_conciclo: toInt($('#aa_calc_conciclo').val()),
            old_tipo: $('#aa_old_tipo').val(),
            old_aplicar: $('#aa_old_aplicar').val()
        };

        if(!payload.tipo || !payload.aplicar){
            alert('Por favor complete los campos obligatorios: Tipo y Aplicar.');
            return;
        }

        var url = payload.id ? resolveUrl('ajax/arrangement_update.php') : resolveUrl('ajax/arrangement_create.php');
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1 mr-1"></span> Guardando...');

        $.post(url, payload, function(res){
            $btn.prop('disabled', false).html('<span class="material-icons align-middle me-1 mr-1" style="font-size: 1rem;">save</span> Guardar Datos');
            if(res && res.success){
                hideModal('#arrangementModal');
                notify('Arrangement guardado exitosamente', 'success');
                arrangementTable.ajax.reload(null, false);
            } else {
                notify((res && res.message) ? res.message : 'Error al guardar arrangement', 'danger');
            }
        }, 'json').fail(function(xhr){
            $btn.prop('disabled', false).html('<span class="material-icons align-middle me-1 mr-1" style="font-size: 1rem;">save</span> Guardar Datos');
            notify('Error en la petición: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'), 'danger');
        });
    });

    $('#arrangementTable').on('click', '.btn-delete-aa', function(){
        if(!confirm('¿Está seguro de eliminar este registro de arrangement? Esta acción no se puede deshacer.')) return;
        var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
        $.post(resolveUrl('ajax/arrangement_delete.php'), {
            id: row.id || '',
            tipo: row.tipo || '',
            aplicar: row.aplicar || ''
        }, function(res){
            if(res && res.success){
                notify('Arrangement eliminado con éxito', 'info');
                arrangementTable.ajax.reload(null, false);
            } else {
                notify((res && res.message) ? res.message : 'Error al eliminar', 'danger');
            }
        }, 'json');
    });
});
