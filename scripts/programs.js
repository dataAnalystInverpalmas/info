$(document).ready(function(){
    var table = $('#programsTable').DataTable({
        ajax: {
            url: '../ajax/program_list.php',
            data: function(d){
                // agregar filtros actuales
                d.programa = $('#f_programa').val() || '';
                d.estado = $('#f_estado').val() || '';
                d.variedad = $('#f_variedad').val() || '';
                d.temporada = $('#f_temporada').val() || '';
            }
        },
        columns: [
            { data: 'id' },
            { data: 'programa' },
            { data: 'variedad' },
            { data: 'ciclo' },
            { data: 'fecha_siembra' },
            { data: 'temporada_obj' },
            {data: 'pico'},
            { data: 'ncamas' },
            { data: 'casa_id' },
            { data: 'raiz' },
            { data: 'pm' },
            { data: 'ferradica' },
            { data: 'estado' },
            { data: 'adicional' },
            { data: 'cantidad_pedida' },
            { data: null, render: function(data,type,row){
                return '<button class="btn btn-sm btn-info btn-edit" data-id="'+row.id+'">Editar</button> '
                     + '<button class="btn btn-sm btn-success btn-clone" data-id="'+row.id+'">Nuevo desde</button> '
                     + '<button class="btn btn-sm btn-danger btn-delete" data-id="'+row.id+'">Eliminar</button>';
            }}
        ]
    });

    function reloadCombos(){
        var programa = $('#f_programa').val() || '';
        var estado = $('#f_estado').val() || '';
        $.get('../ajax/program_filters.php', {programa: programa, estado: estado}, function(res){
            if(!res) return;
            // rebuild variedad
            var $var = $('#f_variedad');
            var sel = $var.val();
            $var.empty().append($('<option>').attr('value','').text('Todas'));
            if(res.variedades && res.variedades.length){
                res.variedades.forEach(function(v){ $var.append($('<option>').attr('value',v).text(v)); });
            }
            // try to reselect previous if still present
            $var.val(sel);

            // rebuild temporada
            var $t = $('#f_temporada');
            var sels = $t.val();
            $t.empty().append($('<option>').attr('value','').text('Todas'));
            if(res.temporadas && res.temporadas.length){
                res.temporadas.forEach(function(v){ $t.append($('<option>').attr('value',v).text(v)); });
            }
            $t.val(sels);
        }, 'json');
    }

    // cargar combos al inicio usando valores por defecto (estado por defecto 1)
    reloadCombos();

    // cuando cambien programa o estado, actualizar variedades y temporadas
    $('#f_programa, #f_estado').on('change', function(){ reloadCombos(); });

    $('#btnNew').on('click', function(){
        $('#programForm')[0].reset();
        $('#p_id').val('');
        $('#p_programa').val('');
        // ensure not in clone mode
        $('#programForm').data('clone', false);
        $('#programModal').modal('show');
    });

    // filtro: recargar tabla con filtros
    $('#btnFilter').on('click', function(){
        table.ajax.reload();
    });
    // limpiar filtros
    $('#btnClearFilter').on('click', function(){
        $('#f_programa').val('');
        $('#f_estado').val('1');
        $('#f_variedad').val('');
        $('#f_temporada').val('');
        reloadCombos();
        table.ajax.reload();
    });

    $('#programsTable').on('click', '.btn-edit', function(){
        var id = $(this).data('id');
        $.get('../ajax/program_get.php', {id: id}, function(res){
            if(res.success){
                var d = res.data;
                $('#p_id').val(d.id);
                $('#p_programa').val(d.programa);
                $('#p_variedad').val(d.variedad);
                $('#p_ciclo').val(d.ciclo);
                $('#p_fecha_siembra').val(d.fecha_siembra);
                $('#p_temporada_obj').val(d.temporada_obj);
                $('#p_ncamas').val(d.ncamas);
                $('#p_casa_id').val(d.casa_id);
                $('#p_raiz').val(d.raiz);
                $('#p_pm').val(d.pm);
                $('#p_ferradica').val(d.ferradica);
                $('#p_estado').val(d.estado);
                $('#p_adicional').val(d.adicional);
                $('#p_cantidad_pedida').val(d.cantidad_pedida);
                // ensure not in clone mode (this is an edit)
                $('#programForm').data('clone', false);
                $('#programModal').modal('show');
            } else {
                alert(res.message || 'Error cargando registro');
            }
        }, 'json');
    });

    // clonar: abrir formulario con datos del registro pero creando uno nuevo (id vacio)
    $('#programsTable').on('click', '.btn-clone', function(){
        var id = $(this).data('id');
        $.get('../ajax/program_get.php', {id: id}, function(res){
            if(res.success){
                var d = res.data;
                // poblar formulario pero dejar id vacío para crear nuevo
                $('#p_id').val('');
                $('#p_programa').val(d.programa);
                $('#p_variedad').val(d.variedad);
                $('#p_ciclo').val(d.ciclo);
                $('#p_fecha_siembra').val(d.fecha_siembra);
                $('#p_temporada_obj').val(d.temporada_obj);
                $('#p_ncamas').val(d.ncamas);
                $('#p_casa_id').val(d.casa_id);
                $('#p_raiz').val(d.raiz);
                $('#p_pm').val(d.pm);
                $('#p_ferradica').val(d.ferradica);
                $('#p_estado').val(d.estado);
                $('#p_adicional').val(d.adicional);
                $('#p_cantidad_pedida').val(d.cantidad_pedida);
                // mark form as clone so save handler forces create
                $('#programForm').data('clone', true);
                $('#programModal').modal('show');
            } else {
                alert(res.message || 'Error cargando registro');
            }
        }, 'json');
    });

    $('#saveProgram').on('click', function(){
        var isClone = $('#programForm').data('clone') === true;
        // if clone, temporarily remove name of hidden id input so serializeArray doesn't include it
        var $idField = $('#p_id');
        var hadName = false;
        if(isClone && $idField.length){
            hadName = $idField.attr('name') !== undefined;
            $idField.removeAttr('name');
        }

        var form = $('#programForm').serializeArray();
        var obj = {};
        $.each(form, function(i,v){ obj[v.name]=v.value; });

        // restore id name if removed
        if(isClone && hadName){ $idField.attr('name','id'); }

        var id = obj.id || '';
        var url = (!id || isClone) ? '../ajax/program_create.php' : '../ajax/program_update.php';

        // ensure id isn't sent when cloning
        if(isClone && obj.hasOwnProperty('id')){ delete obj.id; }

        console.log('Saving program (clone=' + isClone + ') ->', url, obj);

        $.post(url, obj, function(res){
            console.log('Server response:', res);
            if(res && res.success){
                $('#programModal').modal('hide');
                // clear clone flag
                $('#programForm').data('clone', false);
                table.ajax.reload(null,false);
            } else {
                var msg = res && res.message ? res.message : 'Error al guardar';
                try { alert(JSON.stringify(res)); } catch(e){ alert(msg); }
            }
        }, 'json').fail(function(xhr){
            // show raw response for debugging
            var txt = xhr && xhr.responseText ? xhr.responseText : 'No response body';
            alert('Error en la petición: ' + txt);
            console.error('AJAX error', xhr);
        });
    });

    $('#programsTable').on('click', '.btn-delete', function(){
        if(!confirm('Eliminar registro?')) return;
        var id = $(this).data('id');
        $.post('../ajax/program_delete.php', {id: id}, function(res){
            if(res.success){ table.ajax.reload(null,false); }
            else { alert(res.message || 'Error al eliminar'); }
        }, 'json');
    });
});
