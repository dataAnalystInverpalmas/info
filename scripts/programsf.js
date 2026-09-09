$(document).ready(function(){
    function normalizeDateString(raw){
        if(!raw) return '';
        var s = String(raw).trim();
        if(!s) return '';

        if (s.length >= 10 && s.indexOf('-') > -1) {
            return s.substring(0, 10);
        }

        if (s.indexOf('/') > -1) {
            var parts = s.split('/');
            if (parts.length === 3) {
                if (parts[2].length === 4) {
                    return parts[2] + '-' + String(parts[1]).padStart(2, '0') + '-' + String(parts[0]).padStart(2, '0');
                }
                if (parts[0].length === 4) {
                    return parts[0] + '-' + String(parts[1]).padStart(2, '0') + '-' + String(parts[2]).padStart(2, '0');
                }
            }
        }

        return s;
    }

    function getIsoWeekLabel(dateString){
        var normalized = normalizeDateString(dateString);
        if(!normalized) return 'Semana ISO: -';

        var d = new Date(normalized + 'T00:00:00');
        if(isNaN(d.getTime())) return 'Semana ISO: -';
        var tmp = new Date(d.getTime());
        tmp.setDate(tmp.getDate() + 3 - ((tmp.getDay() + 6) % 7));
        var week1 = new Date(tmp.getFullYear(), 0, 4);
        var wk = 1 + Math.round(((tmp - week1) / 86400000 - 3 + ((week1.getDay() + 6) % 7)) / 7);
        return 'Semana ISO: ' + tmp.getFullYear() + '-W' + String(wk).padStart(2, '0');
    }

    function updateIsoWeekHint(value){
        var val = (typeof value !== 'undefined') ? value : $('#pf_fecha_siembra').val();
        $('#pf_fecha_siembra_iso').text(getIsoWeekLabel(val));
    }

    window.updateProgramfIsoWeek = updateIsoWeekHint;
    $('#pf_fecha_siembra').on('input change', function(){ updateIsoWeekHint(this.value); });
    updateIsoWeekHint($('#pf_fecha_siembra').val());

    $('#ff_temporada').select2({
        width: '100%',
        multiple: true,
        tags: true,
        allowClear: true,
        tokenSeparators: [','],
        placeholder: 'ej: FE2707, FE2708'
    });

    var table = $('#programfTable').DataTable({
        ajax: {
            url: '../ajax/programf_list.php',
            data: function(d){
                d.programa = $('#ff_programa').val() || '';
                d.estado = $('#ff_estado').val() || '';
                d.variedad = $('#ff_variedad').val() || '';
                d.temporada = $('#ff_temporada').val() ? $('#ff_temporada').val().join(',') : '';
                d.producto = $('#ff_producto').val() || '';
                d.finca = $('#ff_finca').val() || '';
                d.bloque = $('#ff_bloque').val() || '';
                d.ciclo = $('#ff_ciclo').val() || '';
                d.adicional = $('#ff_adicional').val() || '';
                d.fecha_inicio = $('#ff_fecha_inicio').val() || '';
                d.fecha_fin = $('#ff_fecha_fin').val() || '';
                d.semana_siembra = $('#ff_semana_siembra').val() || '';
                d.color = $('#ff_color').val() || '';
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

    $('#ff_variedad, #ff_color').select2({
        placeholder: 'Buscar...',
        allowClear: true,
        width: '100%'
    });

    function reloadCombos(){
        var programa = $('#ff_programa').val() || '';
        var estado = $('#ff_estado').val() || '';
        $.get('../ajax/programf_filters.php', {programa: programa, estado: estado}, function(res){
            if(!res) return;
            var $var = $('#ff_variedad');
            var sel = $var.val();
            $var.empty().append($('<option>').attr('value','').text('Todas'));
            if(res.variedades && res.variedades.length){
                res.variedades.forEach(function(v){ $var.append($('<option>').attr('value',v).text(v)); });
            }
            $var.val(sel);
            $var.select2({ placeholder: 'Buscar...', allowClear: true, width: '100%' });

            var $p = $('#ff_producto');
            var selp = $p.val();
            $p.empty().append($('<option>').attr('value','').text('Todos'));
            if(res.productos && res.productos.length){
                res.productos.forEach(function(v){ $p.append($('<option>').attr('value',v).text(v)); });
            }
            $p.val(selp);

            var $f = $('#ff_finca');
            var self = $f.val();
            $f.empty().append($('<option>').attr('value','').text('Todas'));
            if(res.fincas && res.fincas.length){
                res.fincas.forEach(function(v){ $f.append($('<option>').attr('value',v).text(v)); });
            }
            $f.val(self);

            var $b = $('#ff_bloque');
            var selb = $b.val();
            $b.empty().append($('<option>').attr('value','').text('Todos'));
            if(res.bloques && res.bloques.length){
                res.bloques.forEach(function(v){ $b.append($('<option>').attr('value',v).text(v)); });
            }
            $b.val(selb);

            var $c = $('#ff_color');
            var selc = $c.val();
            $c.empty().append($('<option>').attr('value','').text('Todos'));
            if(res.colores && res.colores.length){
                res.colores.forEach(function(v){
                    if(v === null || v === undefined || v === '') return;
                    $c.append($('<option>').attr('value', v).text(v));
                });
            }
            $c.val(selc);
            $c.select2({ placeholder: 'Buscar...', allowClear: true, width: '100%' });
        }, 'json');
    }

    reloadCombos();

    $('#ff_programa, #ff_estado').on('change', function(){ reloadCombos(); });

    $('#btnNewF').on('click', function(){
        $('#programfForm')[0].reset();
        $('#pf_id').val('');
        $('#pf_programa').val('');
        updateIsoWeekHint('');
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
        $('#ff_temporada').val(null).trigger('change');
        $('#ff_producto').val('');
        $('#ff_finca').val('');
        $('#ff_bloque').val('');
        $('#ff_ciclo').val('');
        $('#ff_adicional').val('');
        $('#ff_fecha_inicio').val('');
        $('#ff_fecha_fin').val('');
        $('#ff_semana_siembra').val('');
        $('#ff_color').val('');
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
                updateIsoWeekHint(d.fecha_siembra);
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
                updateIsoWeekHint(d.fecha_siembra);
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
