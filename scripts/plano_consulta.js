$(document).ready(function(){
    
    var table = null;

    // ===== Initialize DataTable =====
    function initializeDataTable() {
        if (table) {
            table.destroy();
        }
        
        table = $('#tablaPlanoConsulta').DataTable({
            ajax: {
                url: '../ajax/plano_consulta.php?accion=listar',
                data: function(d) {
                    d.finca = $('#pc_filter_finca').val() || '';
                    d.bloque = $('#pc_filter_bloque').val() || '';
                    d.tabla = $('#pc_filter_tabla').val() || '';
                    d.nave = $('#pc_filter_nave').val() || '';
                    d.tipo_siembra = $('#pc_filter_tipo_siembra').val() || '';
                    d.variedad = $('#pc_filter_variedad').val() || '';
                    d.cosecha = $('#pc_filter_cosecha').val() || '';
                    d.semana_siembra = $('#pc_filter_semana_siembra').val() || '';
                }
            },
            columns: [
                { data: 'finca' },
                { data: 'bloque' },
                { data: 'tabla' },
                { data: 'nave' },
                { data: 'cama' },
                { data: 'fecha_siembra' },
                { data: 'semana_siembra' },
                { data: 'origen' },
                { data: 'tipo_siembra' },
                { data: 'variedad_original' },
                { data: 'cosecha_original' },
                { data: 'variedad_reem' },
                { data: 'cosecha_reem' },
                { data: 'plantas' }
            ],
            pageLength: 25,
            processing: true,
            serverSide: true,
            responsive: false
        });
    }

    // ===== Initialize cascading filter logic =====
    function initializeCascadingFilters() {
        
        // Reload dependent dropdowns based on current selections
        function reloadDependentFilters() {
            var finca = $('#pc_filter_finca').val() || '';
            var bloque = $('#pc_filter_bloque').val() || '';
            var tabla = $('#pc_filter_tabla').val() || '';
            
            // Prepare filter parameters for AJAX call
            var filterParams = {
                finca: finca,
                bloque: bloque,
                tabla: tabla,
                nave: $('#pc_filter_nave').val() || '',
                tipo_siembra: $('#pc_filter_tipo_siembra').val() || '',
                variedad: $('#pc_filter_variedad').val() || '',
                cosecha: $('#pc_filter_cosecha').val() || '',
                semana_siembra: $('#pc_filter_semana_siembra').val() || ''
            };
            
            $.ajax({
                url: '../ajax/plano_consulta_filters.php',
                data: filterParams,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    if (!res) return;
                    
                    // Update Bloque dropdown (if Finca selected)
                    if (finca) {
                        var $bloque = $('#pc_filter_bloque');
                        var prevBloque = $bloque.val();
                        $bloque.empty().append($('<option>').attr('value','').text('Todos'));
                        if (res.bloques && res.bloques.length) {
                            res.bloques.forEach(function(b) {
                                $bloque.append($('<option>').attr('value', b).text(b));
                            });
                        }
                        // Try to reselect if still available
                        if (prevBloque && $bloque.find('option[value="'+prevBloque+'"]').length) {
                            $bloque.val(prevBloque);
                        } else {
                            $bloque.val('');
                        }
                        $bloque.prop('disabled', false);
                        $bloque.closest('.form-group').find('small').hide();
                    } else {
                        // Reset Bloque if Finca empty
                        $('#pc_filter_bloque').empty().append($('<option>').attr('value','').text('Todos')).val('').prop('disabled', true);
                        $('#pc_filter_bloque').closest('.form-group').find('small').show();
                    }
                    
                    // Update Tabla dropdown (if Bloque selected)
                    if (finca && bloque) {
                        var $tabla = $('#pc_filter_tabla');
                        var prevTabla = $tabla.val();
                        $tabla.empty().append($('<option>').attr('value','').text('Todas'));
                        if (res.tablas && res.tablas.length) {
                            res.tablas.forEach(function(t) {
                                $tabla.append($('<option>').attr('value', t).text(t));
                            });
                        }
                        if (prevTabla && $tabla.find('option[value="'+prevTabla+'"]').length) {
                            $tabla.val(prevTabla);
                        } else {
                            $tabla.val('');
                        }
                        $tabla.prop('disabled', false);
                        $tabla.closest('.form-group').find('small').hide();
                    } else {
                        // Reset Tabla if Bloque empty
                        $('#pc_filter_tabla').empty().append($('<option>').attr('value','').text('Todas')).val('').prop('disabled', true);
                        $('#pc_filter_tabla').closest('.form-group').find('small').show();
                    }
                    
                    // Update Nave dropdown (if Tabla selected)
                    if (finca && bloque && tabla) {
                        var $nave = $('#pc_filter_nave');
                        var prevNave = $nave.val();
                        $nave.empty().append($('<option>').attr('value','').text('Todas'));
                        if (res.naves && res.naves.length) {
                            res.naves.forEach(function(n) {
                                $nave.append($('<option>').attr('value', n).text(n));
                            });
                        }
                        if (prevNave && $nave.find('option[value="'+prevNave+'"]').length) {
                            $nave.val(prevNave);
                        } else {
                            $nave.val('');
                        }
                        $nave.prop('disabled', false);
                        $nave.closest('.form-group').find('small').hide();
                    } else {
                        // Reset Nave if Tabla empty
                        $('#pc_filter_nave').empty().append($('<option>').attr('value','').text('Todas')).val('').prop('disabled', true);
                        $('#pc_filter_nave').closest('.form-group').find('small').show();
                    }
                    
                    // Update Tipos de Siembra (if Finca+Bloque selected)
                    if (finca && bloque) {
                        var $tipo = $('#pc_filter_tipo_siembra');
                        var prevTipo = $tipo.val();
                        $tipo.empty().append($('<option>').attr('value','').text('Todos'));
                        if (res.tiposSiembra && res.tiposSiembra.length) {
                            res.tiposSiembra.forEach(function(ts) {
                                $tipo.append($('<option>').attr('value', ts).text(ts));
                            });
                        }
                        if (prevTipo && $tipo.find('option[value="'+prevTipo+'"]').length) {
                            $tipo.val(prevTipo);
                        }
                    }
                    
                    // Update Variedades
                    var $var = $('#pc_filter_variedad');
                    var prevVar = $var.val();
                    $var.empty().append($('<option>').attr('value','').text('Todas'));
                    if (res.variedades && res.variedades.length) {
                        res.variedades.forEach(function(v) {
                            $var.append($('<option>').attr('value', v).text(v));
                        });
                    }
                    if (prevVar && $var.find('option[value="'+prevVar+'"]').length) {
                        $var.val(prevVar);
                    }
                    
                    // Update Cosechas
                    var $cos = $('#pc_filter_cosecha');
                    var prevCos = $cos.val();
                    $cos.empty().append($('<option>').attr('value','').text('Todas'));
                    if (res.cosechas && res.cosechas.length) {
                        res.cosechas.forEach(function(c) {
                            $cos.append($('<option>').attr('value', c).text(c));
                        });
                    }
                    if (prevCos && $cos.find('option[value="'+prevCos+'"]').length) {
                        $cos.val(prevCos);
                    }
                    
                    // Update Semanas de Siembra
                    var $sem = $('#pc_filter_semana_siembra');
                    var prevSem = $sem.val();
                    $sem.empty().append($('<option>').attr('value','').text('Todas'));
                    if (res.semanasSiembra && res.semanasSiembra.length) {
                        res.semanasSiembra.forEach(function(s) {
                            $sem.append($('<option>').attr('value', s).text(s));
                        });
                    }
                    if (prevSem && $sem.find('option[value="'+prevSem+'"]').length) {
                        $sem.val(prevSem);
                    }
                }
            });
        }
        
        // Bind cascading change events
        $('#pc_filter_finca, #pc_filter_bloque, #pc_filter_tabla, #pc_filter_tipo_siembra').on('change', function() {
            reloadDependentFilters();
        });
        
        // Initial load of dependent filters
        reloadDependentFilters();
    }

    // ===== Button Events =====
    $('#pc_btn_filter').on('click', function() {
        if (table) {
            table.ajax.reload();
        }
    });

    $('#pc_btn_clear_filters').on('click', function() {
        $('#pc_filter_finca').val('');
        $('#pc_filter_bloque').val('').prop('disabled', true);
        $('#pc_filter_tabla').val('').prop('disabled', true);
        $('#pc_filter_nave').val('').prop('disabled', true);
        $('#pc_filter_tipo_siembra').val('');
        $('#pc_filter_variedad').val('');
        $('#pc_filter_cosecha').val('');
        $('#pc_filter_semana_siembra').val('');
        
        // Reinitialize cascading filters
        initializeCascadingFilters();
        
        // Reload table
        if (table) {
            table.ajax.reload();
        }
    });

    // Toggle filters sidebar
    $('#btnToggleFilters').on('click', function() {
        $('.app-content').toggleClass('filters-hidden');
    });

    // ===== Initialize on page load =====
    initializeDataTable();
    initializeCascadingFilters();
});

