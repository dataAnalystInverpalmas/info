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

    var VARIEDADES_URL = resolveUrl('ajax/fv_fetchProducts.php');
    var FINCAS_URL = resolveUrl('ajax/fetchFarms.php');

    function catselRow(text, checked) {
        var ico = $('<span class="catsel-ico" style="display:inline-block; width:1.5em; text-align:center; color:#00796B;"></span>').text(checked ? '☑' : '☐');
        if (!checked) ico.css('color', '#94a3b8');
        return $('<div class="catsel-row d-flex align-items-center"></div>').append(ico).append($('<span></span>').text(text));
    }

    function initSel($sel, url, withCheck, isModal) {
        $.get(url, function (html) {
            if (html && html.indexOf('<option') >= 0) {
                $sel.html(html);
            }
            var opts = {
                width: '100%',
                placeholder: $sel.data('placeholder') || 'Seleccionar / buscar...',
                allowClear: true
            };
            if (isModal) {
                opts.dropdownParent = $('#arrangementsModal');
            }
            if (withCheck) {
                opts.templateResult = function (state) {
                    if (!state.id) return $('<span class="catsel-ph" style="opacity:0.6;"></span>').text(state.text || '');
                    return catselRow(state.text, String($sel.val()) === String(state.id));
                };
                opts.templateSelection = function (state) {
                    if (!state.id) return $('<span class="catsel-ph" style="opacity:0.6;"></span>').text(state.text || '');
                    return catselRow(state.text, true);
                };
            }
            $sel.select2(opts);
            var pending = $sel.data('pending');
            if (pending !== undefined && pending !== null && pending !== '') {
                $sel.val(pending).trigger('change');
            }
            $sel.removeData('pending');
        });
    }

    function setSel($sel, val) {
        if ($sel.hasClass('select2-hidden-accessible')) {
            $sel.val(val).trigger('change');
        } else {
            $sel.data('pending', val || '');
        }
    }

    initSel($('#ar_variedad'), VARIEDADES_URL, true, true);
    initSel($('#ar_finca'), FINCAS_URL, true, true);
    initSel($('#f_arr_variedad'), VARIEDADES_URL, false, false);
    initSel($('#f_arr_finca'), FINCAS_URL, false, false);
    initSel($('#copy_variedad_origen'), VARIEDADES_URL, true, false);
    initSel($('#copy_variedad_destino'), VARIEDADES_URL, true, false);

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

        if (arrangementsTable) {
            setTimeout(function() {
                arrangementsTable.columns.adjust().draw();
            }, 200);
        }
    });

    var arrangementsTable = $('#arrangementsTable').DataTable({
        ajax: {
            url: resolveUrl('ajax/arrangements_list.php'),
            data: function(d){
                d.variedad = $('#f_arr_variedad').val() || '';
                d.finca = $('#f_arr_finca').val() || '';
                d.tipo = $('#f_arr_tipo').val() || '';
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
            { data: 'variedad', defaultContent: '' },
            { data: 'finca', defaultContent: '' },
            { data: 'tipo', defaultContent: '' },
            { data: 'aplicar', defaultContent: '' },
            { data: 'medidat', defaultContent: '' },
            { 
                data: 'valor', 
                defaultContent: '',
                render: function(data) {
                    var n = parseFloat(data);
                    return isNaN(n) ? data : n.toFixed(2);
                }
            },
            { 
                data: null, 
                orderable: false,
                className: 'text-center',
                render: function(data, type, row){
                    var rowData = encodeURIComponent(JSON.stringify(row));
                    return '<div class="action-btn-group">' +
                           '<button type="button" class="btn btn-action-edit btn-edit-arr" data-row="' + rowData + '" title="Editar registro">' +
                           '<span class="material-icons">edit</span> <span>Editar</span>' +
                           '</button>' +
                           '<button type="button" class="btn btn-action-delete btn-delete-arr" data-row="' + rowData + '" title="Eliminar registro">' +
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

    $('#btnFilterArrangements').on('click', function(){ arrangementsTable.ajax.reload(); });
    $('#btnClearArrangements').on('click', function(){
        $('#f_arr_variedad').val('').trigger('change');
        $('#f_arr_finca').val('').trigger('change');
        $('#f_arr_tipo').val('');
        arrangementsTable.ajax.reload();
    });

    $('#btnCopyArrangements').on('click', function(){
        var variedadOrigen = ($('#copy_variedad_origen').val() || '').trim();
        var variedadDestino = ($('#copy_variedad_destino').val() || '').trim();

        if(!variedadOrigen || !variedadDestino){
            alert('Debe seleccionar la variedad origen y la variedad destino.');
            return;
        }

        if(variedadOrigen.toLowerCase() === variedadDestino.toLowerCase()){
            alert('La variedad origen y destino deben ser distintas.');
            return;
        }

        if(!confirm('Se copiarán todas las configuraciones de "' + variedadOrigen + '" hacia "' + variedadDestino + '". ¿Desea continuar?')) return;

        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1 mr-1"></span> Copiando...');

        $.post(resolveUrl('ajax/arrangements_copy_variety.php'), {
            variedad_origen: variedadOrigen,
            variedad_destino: variedadDestino
        }, function(res){
            $btn.prop('disabled', false).html('<span class="material-icons me-1 mr-1" style="font-size: 1.05rem;">copy_all</span> Copiar');
            if(res && res.success){
                var msg = '¡Configuración copiada exitosamente!';
                if(typeof res.copied_count !== 'undefined'){
                    msg += ' Registros insertados: ' + res.copied_count;
                }
                if(typeof res.skipped_count !== 'undefined'){
                    msg += '. Registros omitidos (ya existían): ' + res.skipped_count;
                }
                notify(msg, 'success');
                arrangementsTable.ajax.reload(null, false);
            } else {
                notify((res && res.message) ? res.message : 'Error al copiar datos', 'danger');
            }
        }, 'json').fail(function(xhr){
            $btn.prop('disabled', false).html('<span class="material-icons me-1 mr-1" style="font-size: 1.05rem;">copy_all</span> Copiar');
            notify('Error en la petición: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'), 'danger');
        });
    });

    $('#btnNewArrangements').on('click', function(){
        $('#arrangementsForm')[0].reset();
        $('#ar_id').val('');
        $('#ar_old_variedad').val('');
        $('#ar_old_finca').val('');
        $('#ar_old_tipo').val('');
        $('#ar_old_aplicar').val('');
        $('#ar_variedad').val('').trigger('change');
        $('#ar_finca').val('').trigger('change');

        $('#modalArrTitle').text('Nuevo Registro Arrangement');
        $('#modalArrSubtitle').text('Completar campos para vincular insumo y labor a la variedad');
        $('#modalArrIcon').text('add_circle');
        showModal('#arrangementsModal');
    });

    $('#arrangementsTable').on('click', '.btn-edit-arr', function(){
        var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
        $('#ar_id').val(row.id || '');
        setSel($('#ar_variedad'), row.variedad || '');
        setSel($('#ar_finca'), row.finca || '');
        $('#ar_tipo').val(row.tipo || '');
        $('#ar_aplicar').val(row.aplicar || '');
        $('#ar_medidat').val(row.medidat || '');
        $('#ar_valor').val(row.valor || '');

        $('#ar_old_variedad').val(row.variedad || '');
        $('#ar_old_finca').val(row.finca || '');
        $('#ar_old_tipo').val(row.tipo || '');
        $('#ar_old_aplicar').val(row.aplicar || '');

        $('#modalArrTitle').text('Editar Arrangement #' + (row.id || ''));
        $('#modalArrSubtitle').text('Modifica los valores configurados para este registro');
        $('#modalArrIcon').text('edit_note');
        showModal('#arrangementsModal');
    });

    $('#saveArrangements').on('click', function(){
        var payload = {
            id: $('#ar_id').val(),
            variedad: ($('#ar_variedad').val() || '').trim(),
            finca: ($('#ar_finca').val() || '').trim(),
            tipo: ($('#ar_tipo').val() || '').trim(),
            aplicar: ($('#ar_aplicar').val() || '').trim(),
            medidat: ($('#ar_medidat').val() || '').trim(),
            valor: parseFloat($('#ar_valor').val() || '0'),
            old_variedad: $('#ar_old_variedad').val(),
            old_finca: $('#ar_old_finca').val(),
            old_tipo: $('#ar_old_tipo').val(),
            old_aplicar: $('#ar_old_aplicar').val()
        };

        if(!payload.variedad || !payload.finca || !payload.tipo || !payload.aplicar){
            alert('Por favor complete los campos obligatorios (*).');
            return;
        }

        var url = payload.id ? resolveUrl('ajax/arrangements_update.php') : resolveUrl('ajax/arrangements_create.php');
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1 mr-1"></span> Guardando...');

        $.post(url, payload, function(res){
            $btn.prop('disabled', false).html('<span class="material-icons align-middle me-1 mr-1" style="font-size: 1rem;">save</span> Guardar Datos');
            if(res && res.success){
                hideModal('#arrangementsModal');
                notify('Arrangement guardado exitosamente', 'success');
                arrangementsTable.ajax.reload(null, false);
            } else {
                notify((res && res.message) ? res.message : 'Error al guardar arrangement', 'danger');
            }
        }, 'json').fail(function(xhr){
            $btn.prop('disabled', false).html('<span class="material-icons align-middle me-1 mr-1" style="font-size: 1rem;">save</span> Guardar Datos');
            notify('Error en la petición: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'), 'danger');
        });
    });

    $('#arrangementsTable').on('click', '.btn-delete-arr', function(){
        if(!confirm('¿Está seguro de eliminar este registro de arrangement? Esta acción no se puede deshacer.')) return;
        var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
        $.post(resolveUrl('ajax/arrangements_delete.php'), {
            id: row.id || '',
            variedad: row.variedad || '',
            finca: row.finca || '',
            tipo: row.tipo || '',
            aplicar: row.aplicar || ''
        }, function(res){
            if(res && res.success){
                notify('Arrangement eliminado con éxito', 'info');
                arrangementsTable.ajax.reload(null, false);
            } else {
                notify((res && res.message) ? res.message : 'Error al eliminar', 'danger');
            }
        }, 'json');
    });
});
