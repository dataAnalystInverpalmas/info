$(document).ready(function(){
    var VARIEDADES_URL = '../ajax/fv_fetchProducts.php';
    var FINCAS_URL = '../ajax/fetchFarms.php';

    function catselRow(text, checked) {
        var ico = $('<span class="catsel-ico"></span>').text(checked ? '\u2611' : '\u2610');
        if (!checked) ico.addClass('muted');
        return $('<div class="catsel-row"></div>').append(ico).append($('<span></span>').text(text));
    }

    function initSel($sel, url, withCheck) {
        $.get(url, function (html) {
            if (html && html.indexOf('<option') >= 0) {
                $sel.html(html);
            }
            var opts = {
                width: '100%',
                placeholder: $sel.data('placeholder') || 'Seleccionar / buscar...',
                allowClear: true
            };
            if (withCheck) {
                opts.templateResult = function (state) {
                    if (!state.id) return $('<span class="catsel-ph"></span>').text(state.text || '');
                    return catselRow(state.text, String($sel.val()) === String(state.id));
                };
                opts.templateSelection = function (state) {
                    if (!state.id) return $('<span class="catsel-ph"></span>').text(state.text || '');
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

    initSel($('#ar_variedad'), VARIEDADES_URL, true);
    initSel($('#ar_finca'), FINCAS_URL, true);
    initSel($('#f_arr_variedad'), VARIEDADES_URL, false);
    initSel($('#f_arr_finca'), FINCAS_URL, false);
    initSel($('#copy_variedad_origen'), VARIEDADES_URL, true);
    initSel($('#copy_variedad_destino'), VARIEDADES_URL, true);

    var arrangementsTable = $('#arrangementsTable').DataTable({
        ajax: {
            url: '../ajax/arrangements_list.php',
            data: function(d){
                d.variedad = $('#f_arr_variedad').val() || '';
                d.finca = $('#f_arr_finca').val() || '';
                d.tipo = $('#f_arr_tipo').val() || '';
            }
        },
        columns: [
            { data: 'id', defaultContent: '' },
            { data: 'variedad', defaultContent: '' },
            { data: 'finca', defaultContent: '' },
            { data: 'tipo', defaultContent: '' },
            { data: 'aplicar', defaultContent: '' },
            { data: 'medidat', defaultContent: '' },
            { data: 'valor', defaultContent: '' },
            { data: null, render: function(data, type, row){
                return '<button class="btn btn-sm btn-info btn-edit-arr" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Editar</button>' +
                       '<button class="btn btn-sm btn-danger btn-delete-arr" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Eliminar</button>';
            }}
        ]
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
            alert('Debe ingresar variedad origen y variedad destino');
            return;
        }

        if(variedadOrigen.toLowerCase() === variedadDestino.toLowerCase()){
            alert('La variedad origen y destino deben ser diferentes');
            return;
        }

        if(!confirm('Se copiaran los datos de "' + variedadOrigen + '" hacia "' + variedadDestino + '". Desea continuar?')) return;

        $.post('../ajax/arrangements_copy_variety.php', {
            variedad_origen: variedadOrigen,
            variedad_destino: variedadDestino
        }, function(res){
            if(res && res.success){
                var msg = 'Copiado completado.';
                if(typeof res.copied_count !== 'undefined'){
                    msg += ' Registros insertados: ' + res.copied_count;
                }
                if(typeof res.skipped_count !== 'undefined'){
                    msg += '. Registros omitidos: ' + res.skipped_count;
                }
                alert(msg);
                arrangementsTable.ajax.reload(null, false);
            } else {
                alert((res && res.message) ? res.message : 'Error al copiar datos');
            }
        }, 'json').fail(function(xhr){
            alert('Error en la peticion: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'));
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
        $('#arrangementsModal').modal('show');
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
        $('#arrangementsModal').modal('show');
    });

    $('#saveArrangements').on('click', function(){
        var payload = {
            id: $('#ar_id').val(),
            variedad: $('#ar_variedad').val(),
            finca: $('#ar_finca').val(),
            tipo: $('#ar_tipo').val(),
            aplicar: $('#ar_aplicar').val(),
            medidat: $('#ar_medidat').val(),
            valor: $('#ar_valor').val(),
            old_variedad: $('#ar_old_variedad').val(),
            old_finca: $('#ar_old_finca').val(),
            old_tipo: $('#ar_old_tipo').val(),
            old_aplicar: $('#ar_old_aplicar').val()
        };

        var url = payload.id ? '../ajax/arrangements_update.php' : '../ajax/arrangements_create.php';
        $.post(url, payload, function(res){
            if(res && res.success){
                $('#arrangementsModal').modal('hide');
                arrangementsTable.ajax.reload(null, false);
            } else {
                alert((res && res.message) ? res.message : 'Error al guardar arrangements');
            }
        }, 'json').fail(function(xhr){
            alert('Error en la peticion: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'));
        });
    });

    $('#arrangementsTable').on('click', '.btn-delete-arr', function(){
        if(!confirm('Eliminar registro de arrangements?')) return;
        var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
        $.post('../ajax/arrangements_delete.php', {
            id: row.id || '',
            variedad: row.variedad || '',
            finca: row.finca || '',
            tipo: row.tipo || '',
            aplicar: row.aplicar || ''
        }, function(res){
            if(res && res.success){
                arrangementsTable.ajax.reload(null, false);
            } else {
                alert((res && res.message) ? res.message : 'Error al eliminar');
            }
        }, 'json');
    });
});
