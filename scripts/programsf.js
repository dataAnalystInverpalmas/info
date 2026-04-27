$(document).ready(function(){
    var table = $('#programfTable').DataTable({
        ajax: {
            url: '../ajax/programf_list.php',
            data: function(d){
                d.programa = $('#ff_programa').val() || '';
                d.estado = $('#ff_estado').val() || '';
                d.variedad = $('#ff_variedad').val() || '';
                d.temporada = $('#ff_temporada').val() || '';
                d.producto = $('#ff_producto').val() || '';
                d.finca = $('#ff_finca').val() || '';
                d.bloque = $('#ff_bloque').val() || '';
                d.ciclo = $('#ff_ciclo').val() || '';
                d.adicional = $('#ff_adicional').val() || '';
                d.semana_siembra = $('#ff_semana_siembra').val() || '';
            }
        },
        footerCallback: function(row, data, start, end, display){
            var api = this.api();
            // Total ncamas (col 7)
            var totalNcamas = api.column(7, {search:'applied'}).data().reduce(function(a, b){
                return a + (parseFloat(b) || 0);
            }, 0);
            $(api.column(7).footer()).html(totalNcamas);
        },
        columns: [
            { data: 'id' },
            { data: 'programa' },
            { data: 'producto' },
            { data: 'variedad' },
            { data: 'temporada_obj' },
            { data: 'finca' },
            { data: 'bloque' },
            { data: 'ncamas' },
            { data: 'ciclo' },
            { data: 'fecha_siembra', render: function(data){
                if(!data) return '';
                var d = new Date(data + 'T00:00:00');
                var tmp = new Date(d.getTime());
                tmp.setDate(tmp.getDate() + 3 - ((tmp.getDay() + 6) % 7));
                var week1 = new Date(tmp.getFullYear(), 0, 4);
                var wk = 1 + Math.round(((tmp - week1) / 86400000 - 3 + ((week1.getDay() + 6) % 7)) / 7);
                var yy = String(tmp.getFullYear()).slice(-2);
                return yy + String(wk).padStart(2, '0');
            }},
            { data: 'ferradica' },
            { data: 'adicional' },
            { data: 'estado' },
            { data: null, render: function(data,type,row){
                return '<button class="btn btn-sm btn-info btn-edit" data-id="'+row.id+'">Editar</button> '
                     + '<button class="btn btn-sm btn-success btn-clone" data-id="'+row.id+'">Nuevo desde</button> '
                     + '<button class="btn btn-sm btn-danger btn-delete" data-id="'+row.id+'">Eliminar</button>';
            }}
        ]
    });

    // Inicializar Select2 en variedad y temporada
    $('#ff_variedad, #ff_temporada').select2({
        placeholder: 'Buscar...',
        allowClear: true,
        width: '100%'
    });

    function reloadCombos(){
        var programa = $('#ff_programa').val() || '';
        var estado = $('#ff_estado').val() || '';
        $.get('../ajax/programf_filters.php', {programa: programa, estado: estado}, function(res){
            if(!res) return;
            // rebuild variedad
            var $var = $('#ff_variedad');
            var sel = $var.val();
            $var.empty().append($('<option>').attr('value','').text('Todas'));
            if(res.variedades && res.variedades.length){
                res.variedades.forEach(function(v){ $var.append($('<option>').attr('value',v).text(v)); });
            }
            $var.val(sel);
            // re-init Select2
            $var.select2({ placeholder: 'Buscar...', allowClear: true, width: '100%' });

            // rebuild temporada
            var $t = $('#ff_temporada');
            var sels = $t.val();
            $t.empty().append($('<option>').attr('value','').text('Todas'));
            if(res.temporadas && res.temporadas.length){
                res.temporadas.forEach(function(v){ $t.append($('<option>').attr('value',v).text(v)); });
            }
            $t.val(sels);
            // re-init Select2
            $t.select2({ placeholder: 'Buscar...', allowClear: true, width: '100%' });

            // rebuild producto
            var $p = $('#ff_producto');
            var selp = $p.val();
            $p.empty().append($('<option>').attr('value','').text('Todos'));
            if(res.productos && res.productos.length){
                res.productos.forEach(function(v){ $p.append($('<option>').attr('value',v).text(v)); });
            }
            $p.val(selp);

            // rebuild finca
            var $f = $('#ff_finca');
            var self = $f.val();
            $f.empty().append($('<option>').attr('value','').text('Todas'));
            if(res.fincas && res.fincas.length){
                res.fincas.forEach(function(v){ $f.append($('<option>').attr('value',v).text(v)); });
            }
            $f.val(self);

            // rebuild bloque
            var $b = $('#ff_bloque');
            var selb = $b.val();
            $b.empty().append($('<option>').attr('value','').text('Todos'));
            if(res.bloques && res.bloques.length){
                res.bloques.forEach(function(v){ $b.append($('<option>').attr('value',v).text(v)); });
            }
            $b.val(selb);
        }, 'json');
    }

    reloadCombos();

    $('#ff_programa, #ff_estado').on('change', function(){ reloadCombos(); });

    $('#btnNewF').on('click', function(){
        $('#programfForm')[0].reset();
        $('#pf_id').val('');
        $('#pf_programa').val('');
        $('#programfForm').data('clone', false);
        $('#programfModal').modal('show');
    });

    $('#btnFilterF').on('click', function(){
        table.ajax.reload();
    });

    $('#btnClearFilterF').on('click', function(){
        $('#ff_programa').val('');
        $('#ff_estado').val('1');
        $('#ff_variedad').val('').trigger('change');
        $('#ff_temporada').val('').trigger('change');
        $('#ff_producto').val('');
        $('#ff_finca').val('');
        $('#ff_bloque').val('');
        $('#ff_ciclo').val('');
        $('#ff_adicional').val('');
        $('#ff_semana_siembra').val('');
        reloadCombos();
        table.ajax.reload();
    });

    $('#programfTable').on('click', '.btn-edit', function(){
        var id = $(this).data('id');
        $.get('../ajax/programf_get.php', {id: id}, function(res){
            if(res.success){
                var d = res.data;
                $('#pf_id').val(d.id);
                $('#pf_programa').val(d.programa);
                $('#pf_producto').val(d.producto);
                $('#pf_variedad').val(d.variedad);
                $('#pf_temporada_obj').val(d.temporada_obj);
                $('#pf_finca').val(d.finca);
                $('#pf_bloque').val(d.bloque);
                $('#pf_ncamas').val(d.ncamas);
                $('#pf_ciclo').val(d.ciclo);
                $('#pf_fecha_siembra').val(d.fecha_siembra);
                $('#pf_ferradica').val(d.ferradica);
                $('#pf_adicional').val(d.adicional);
                $('#pf_estado').val(d.estado);
                $('#programfForm').data('clone', false);
                $('#programfModal').modal('show');
            } else {
                alert(res.message || 'Error cargando registro');
            }
        }, 'json');
    });

    $('#programfTable').on('click', '.btn-clone', function(){
        var id = $(this).data('id');
        $.get('../ajax/programf_get.php', {id: id}, function(res){
            if(res.success){
                var d = res.data;
                $('#pf_id').val('');
                $('#pf_programa').val(d.programa);
                $('#pf_producto').val(d.producto);
                $('#pf_variedad').val(d.variedad);
                $('#pf_temporada_obj').val(d.temporada_obj);
                $('#pf_finca').val(d.finca);
                $('#pf_bloque').val(d.bloque);
                $('#pf_ncamas').val(d.ncamas);
                $('#pf_ciclo').val(d.ciclo);
                $('#pf_fecha_siembra').val(d.fecha_siembra);
                $('#pf_ferradica').val(d.ferradica);
                $('#pf_adicional').val(d.adicional);
                $('#pf_estado').val(d.estado);
                $('#programfForm').data('clone', true);
                $('#programfModal').modal('show');
            } else {
                alert(res.message || 'Error cargando registro');
            }
        }, 'json');
    });

    $('#saveProgramf').on('click', function(){
        var isClone = $('#programfForm').data('clone') === true;
        var $idField = $('#pf_id');
        var hadName = false;
        if(isClone && $idField.length){
            hadName = $idField.attr('name') !== undefined;
            $idField.removeAttr('name');
        }

        var form = $('#programfForm').serializeArray();
        var obj = {};
        $.each(form, function(i,v){ obj[v.name]=v.value; });

        if(isClone && hadName){ $idField.attr('name','id'); }

        var id = obj.id || '';
        var url = (!id || isClone) ? '../ajax/programf_create.php' : '../ajax/programf_update.php';

        if(isClone && obj.hasOwnProperty('id')){ delete obj.id; }

        $.post(url, obj, function(res){
            if(res && res.success){
                $('#programfModal').modal('hide');
                $('#programfForm').data('clone', false);
                table.ajax.reload(null,false);
            } else {
                var msg = res && res.message ? res.message : 'Error al guardar';
                try { alert(JSON.stringify(res)); } catch(e){ alert(msg); }
            }
        }, 'json').fail(function(xhr){
            var txt = xhr && xhr.responseText ? xhr.responseText : 'No response body';
            alert('Error en la petición: ' + txt);
            console.error('AJAX error', xhr);
        });
    });

    $('#programfTable').on('click', '.btn-delete', function(){
        if(!confirm('Eliminar registro?')) return;
        var id = $(this).data('id');
        $.post('../ajax/programf_delete.php', {id: id}, function(res){
            if(res.success){ table.ajax.reload(null,false); }
            else { alert(res.message || 'Error al eliminar'); }
        }, 'json');
    });
});
