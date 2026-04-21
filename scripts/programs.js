$(document).ready(function(){
    function normalizeDateString(raw){
        if(!raw) return '';
        var s = String(raw).trim();
        if(!s) return '';

        // Si viene con hora, tomar solo YYYY-MM-DD
        if (s.length >= 10 && s.indexOf('-') > -1) {
            return s.substring(0, 10);
        }

        // Convertir formatos con / a YYYY-MM-DD
        if (s.indexOf('/') > -1) {
            var parts = s.split('/');
            if (parts.length === 3) {
                // dd/mm/yyyy
                if (parts[2].length === 4) {
                    return parts[2] + '-' + String(parts[1]).padStart(2, '0') + '-' + String(parts[0]).padStart(2, '0');
                }
                // yyyy/mm/dd
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
        var val = (typeof value !== 'undefined') ? value : $('#p_fecha_siembra').val();
        $('#p_fecha_siembra_iso').text(getIsoWeekLabel(val));
    }

    function setProgramDateField(rawValue){
        var normalized = normalizeDateString(rawValue);
        $('#p_fecha_siembra').val(normalized);
        updateIsoWeekHint(normalized);
    }

    // Exponer helper global para invocación directa desde markup si aplica
    window.updateProgramIsoWeek = updateIsoWeekHint;

    var table = $('#programsTable').DataTable({
        ajax: {
            url: '../ajax/program_list.php',
            data: function(d){
                // agregar filtros actuales
                d.programa = $('#f_programa').val() || '';
                d.estado = $('#f_estado').val() || '';
                d.variedad = $('#f_variedad').val() || '';
                d.temporada = $('#f_temporada').val() || '';
                d.ciclo = $('#f_ciclo').val() || '';
                d.adicional = $('#f_adicional').val() || '';
                d.semana_siembra = $('#f_semana_siembra').val() || '';
            }
        },
        footerCallback: function(row, data, start, end, display){
            var api = this.api();
            var total = api.column(7, {search:'applied'}).data().reduce(function(a, b){
                return a + (parseFloat(b) || 0);
            }, 0);
            $(api.column(7).footer()).html(total);
        },
        columns: [
            { data: 'id' },
            { data: 'programa' },
            { data: 'variedad' },
            { data: 'ciclo' },
            { data: 'fecha_siembra', render: function(data){
                if(!data) return '';
                var d = new Date(data + 'T00:00:00');
                // ISO week number calculation
                var tmp = new Date(d.getTime());
                tmp.setDate(tmp.getDate() + 3 - ((tmp.getDay() + 6) % 7));
                var week1 = new Date(tmp.getFullYear(), 0, 4);
                var wk = 1 + Math.round(((tmp - week1) / 86400000 - 3 + ((week1.getDay() + 6) % 7)) / 7);
                var yy = String(tmp.getFullYear()).slice(-2);
                return yy + String(wk).padStart(2, '0');
            }},
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

    // Inicializar Select2 en variedad y temporada
    $('#f_variedad, #f_temporada').select2({
        placeholder: 'Buscar...',
        allowClear: true,
        width: '100%'
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
            // re-init Select2
            $var.select2({ placeholder: 'Buscar...', allowClear: true, width: '100%' });

            // rebuild temporada
            var $t = $('#f_temporada');
            var sels = $t.val();
            $t.empty().append($('<option>').attr('value','').text('Todas'));
            if(res.temporadas && res.temporadas.length){
                res.temporadas.forEach(function(v){ $t.append($('<option>').attr('value',v).text(v)); });
            }
            $t.val(sels);
            // re-init Select2
            $t.select2({ placeholder: 'Buscar...', allowClear: true, width: '100%' });
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
        updateIsoWeekHint();
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
        $('#f_variedad').val('').trigger('change');
        $('#f_temporada').val('').trigger('change');
        $('#f_ciclo').val('');
        $('#f_adicional').val('');
        $('#f_semana_siembra').val('');
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
                setProgramDateField(d.fecha_siembra);
                $('#p_temporada_obj').val(d.temporada_obj);
                $('#p_ncamas').val(d.ncamas);
                $('#p_casa_id').val(d.casa_id);
                $('#p_pico').val(d.pico);
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
                setProgramDateField(d.fecha_siembra);
                $('#p_temporada_obj').val(d.temporada_obj);
                $('#p_ncamas').val(d.ncamas);
                $('#p_casa_id').val(d.casa_id);
                $('#p_pico').val(d.pico);
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

    $('#p_fecha_siembra').on('change input blur', function(){
        updateIsoWeekHint(this.value);
    });

    $('#programModal').on('shown.bs.modal', function(){
        updateIsoWeekHint();
    });

    updateIsoWeekHint();
});
