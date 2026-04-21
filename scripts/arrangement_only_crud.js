$(document).ready(function(){
    function toInt(value){
        var n = parseInt(value, 10);
        return isNaN(n) ? 0 : n;
    }

    var arrangementTable = $('#arrangementTable').DataTable({
        ajax: {
            url: '../ajax/arrangement_list.php',
            data: function(d){
                d.tipo = $('#f_aa_tipo').val() || '';
                d.aplicar = $('#f_aa_aplicar').val() || '';
            }
        },
        columns: [
            { data: 'id', defaultContent: '' },
            { data: 'tipo', defaultContent: '' },
            { data: 'aplicar', defaultContent: '' },
            { data: 'seccion', defaultContent: '' },
            { data: 'orden', defaultContent: '' },
            { data: 'calc_conciclo', defaultContent: '' },
            { data: null, render: function(data, type, row){
                return '<button class="btn btn-sm btn-info btn-edit-aa" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Editar</button>' +
                       '<button class="btn btn-sm btn-danger btn-delete-aa" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Eliminar</button>';
            }}
        ]
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
        $('#arrangementModal').modal('show');
    });

    $('#arrangementTable').on('click', '.btn-edit-aa', function(){
        var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
        $('#aa_id').val(row.id || '');
        $('#aa_tipo').val(row.tipo || '');
        $('#aa_aplicar').val(row.aplicar || '');
        $('#aa_seccion').val(row.seccion || '');
        $('#aa_orden').val(row.orden || '');
        $('#aa_calc_conciclo').val(row.calc_conciclo || '');

        $('#aa_old_tipo').val(row.tipo || '');
        $('#aa_old_aplicar').val(row.aplicar || '');
        $('#arrangementModal').modal('show');
    });

    $('#saveArrangement').on('click', function(){
        var payload = {
            id: $('#aa_id').val(),
            tipo: $('#aa_tipo').val(),
            aplicar: $('#aa_aplicar').val(),
            seccion: toInt($('#aa_seccion').val()),
            orden: toInt($('#aa_orden').val()),
            calc_conciclo: toInt($('#aa_calc_conciclo').val()),
            old_tipo: $('#aa_old_tipo').val(),
            old_aplicar: $('#aa_old_aplicar').val()
        };

        var url = payload.id ? '../ajax/arrangement_update.php' : '../ajax/arrangement_create.php';
        $.post(url, payload, function(res){
            if(res && res.success){
                $('#arrangementModal').modal('hide');
                arrangementTable.ajax.reload(null, false);
            } else {
                alert((res && res.message) ? res.message : 'Error al guardar arrangement');
            }
        }, 'json').fail(function(xhr){
            alert('Error en la peticion: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'));
        });
    });

    $('#arrangementTable').on('click', '.btn-delete-aa', function(){
        if(!confirm('Eliminar registro de arrangement?')) return;
        var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
        $.post('../ajax/arrangement_delete.php', {
            id: row.id || '',
            tipo: row.tipo || '',
            aplicar: row.aplicar || ''
        }, function(res){
            if(res && res.success){
                arrangementTable.ajax.reload(null, false);
            } else {
                alert((res && res.message) ? res.message : 'Error al eliminar');
            }
        }, 'json');
    });
});
