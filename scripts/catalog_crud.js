$(document).ready(function () {
    var $root = $('#catalogCrudRoot');
    if (!$root.length) return;

    var table = $root.data('table');
    var title = $root.data('title') || ('CRUD ' + table);
    var endpointBase = $root.data('endpoint') || table;

    var urls = {
        meta: '../ajax/crud_meta.php?table=' + encodeURIComponent(table),
        list: '../ajax/' + endpointBase + '_list.php',
        create: '../ajax/' + endpointBase + '_create.php',
        update: '../ajax/' + endpointBase + '_update.php',
        del: '../ajax/' + endpointBase + '_delete.php'
    };

    var meta = null;
    var dt = null;
    var bulkField = $root.attr('data-bulk-field') || '';
    var usesBulk = bulkField !== '';
    var selectedIds = {};
    var selectsConfig = {};
    try { selectsConfig = JSON.parse($root.attr('data-selects') || '{}'); } catch(e) { selectsConfig = {}; }
    var displayConfig = {};
    try { displayConfig = JSON.parse($root.attr('data-display') || '{}'); } catch(e) { displayConfig = {}; }

    // Human-friendly label translations for columns and filters
    var LABEL_MAP = {
        'id': 'ID',
        'nombre': 'Nombre',
        'name': 'Nombre Completo',
        'email': 'Correo Electrónico',
        'password': 'Contraseña',
        'role': 'Rol',
        'users_role': 'Rol de Usuario',
        'dir': 'Ruta / Módulo',
        'nit': 'NIT',
        'ubicacion': 'Ubicación',
        'finca': 'Finca',
        'finca_id': 'Finca',
        'finca_name': 'Finca',
        'arrangement_id': 'Arrangement',
        'arrangement_name': 'Arrangement',
        'insumo': 'Insumo',
        'medida': 'Medida',
        'medidat': 'Medida',
        'dosis': 'Dosis',
        'producto': 'Producto',
        'color': 'Color',
        'ciclo': 'Ciclo',
        'codvari': 'Cód. Variedad',
        'casa_comercial': 'Casa Comercial',
        'estado': 'Estado',
        'fiesta': 'Fiesta',
        'cod_temporada': 'Cód. Temporada',
        'año': 'Año',
        'ano_fiesta': 'Año Fiesta',
        'fecha_pico': 'Fecha Pico',
        'fecha_fiesta': 'Fecha Fiesta',
        'tipo': 'Tipo',
        'ncosecha': 'N° Cosecha',
        'bloque': 'Bloque',
        'tabla': 'Tabla',
        'nave': 'Nave',
        'cama': 'Cama',
        'longitud': 'Longitud',
        'ancho': 'Ancho',
        'matas': 'Matas',
        'aplicar': 'Aplicar',
        'seccion': 'Sección',
        'orden': 'Orden',
        'calc_conciclo': 'Calc. Con Ciclo',
        'valor': 'Valor',
        'variedad': 'Variedad'
    };

    var TABLE_ICONS = {
        'breeders': 'biotech',
        'supplies': 'inventory_2',
        'varieties': 'local_florist',
        'seasons': 'date_range',
        'greenhouses': 'deck',
        'users': 'people_alt',
        'roles': 'security',
        'arrangement': 'settings',
        'arrangements': 'tune'
    };

    function formatLabel(name) {
        if (!name) return '';
        var lower = String(name).toLowerCase();
        if (LABEL_MAP[lower]) return LABEL_MAP[lower];
        return String(name).replace(/_/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); });
    }

    function escHtml(v) {
        return String(v == null ? '' : v)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function inputType(col) {
        var name = (col.name || '').toLowerCase();
        var t = (col.type || '').toLowerCase();
        if (name === 'password') return 'password';
        if (name === 'email') return 'email';
        if (['tinyint', 'smallint', 'mediumint', 'int', 'bigint'].indexOf(t) >= 0) return 'number';
        if (['decimal', 'float', 'double'].indexOf(t) >= 0) return 'number';
        if (t.indexOf('date') >= 0) return 'date';
        return 'text';
    }

    // Toggle Sidebar visibility for wide screen view
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

        if (dt) {
            setTimeout(function() {
                dt.columns.adjust().draw();
            }, 200);
        }
    });

    function buildFilters() {
        var html = '';
        var filtersConfig = null;
        try { filtersConfig = JSON.parse($root.attr('data-filters') || 'null'); } catch(e) { filtersConfig = null; }
        var list = null;
        var map = null;
        if (Array.isArray(filtersConfig)) {
            list = filtersConfig.filter(function (n) { return typeof n === 'string'; });
        } else if (filtersConfig && typeof filtersConfig === 'object') {
            map = filtersConfig;
            list = Object.keys(map);
        }
        var names = list || meta.columns.map(function (c) { return c.name; });

        names.forEach(function (name) {
            var cfg = map ? map[name] : null;
            var isUrl = typeof cfg === 'string' && cfg.length > 0;
            var lbl = formatLabel(name);

            html += '<div class="filter-group mb-2">';
            html += '<label class="filter-label">' + escHtml(lbl) + '</label>';
            if (isUrl) {
                html += '<select class="form-control form-control-sm f-col" data-col="' + escHtml(name) + '" data-url="' + escHtml(cfg) + '"><option value="">Todos</option></select>';
            } else {
                html += '<input type="text" class="form-control form-control-sm f-col" data-col="' + escHtml(name) + '" placeholder="Filtrar ' + escHtml(lbl.toLowerCase()) + '...">';
            }
            html += '</div>';
        });

        $('#crudFilters').html(html);

        $('#crudFilters select.f-col[data-url]').each(function () {
            var $sel = $(this);
            var url = $sel.data('url');
            var colName = $sel.data('col');
            var friendlyName = formatLabel(colName);

            $.get(url, function (optHtml) {
                if (optHtml) $sel.html(optHtml);
                $sel.select2({
                    width: '100%',
                    placeholder: 'Seleccionar ' + friendlyName.toLowerCase() + '...',
                    allowClear: true
                });
                var pending = $sel.data('pending');
                if (pending) {
                    $sel.val(pending).trigger('change');
                    $sel.removeData('pending');
                }
            });
        });
    }

    function catselRow(text, checked) {
        var ico = $('<span class="catsel-ico" style="display:inline-block; width:1.5em; text-align:center; color:#00796B;"></span>').text(checked ? '☑' : '☐');
        if (!checked) ico.css('color', '#94a3b8');
        return $('<div class="catsel-row d-flex align-items-center"></div>').append(ico).append($('<span></span>').text(text));
    }

    function initCatsel($sel) {
        $sel.select2({
            width: '100%',
            placeholder: 'Seleccionar / buscar...',
            allowClear: true,
            dropdownParent: $('#catalogModal'),
            templateResult: function (state) {
                if (!state.id) return $('<span class="catsel-ph" style="opacity:0.6;"></span>').text(state.text || '');
                return catselRow(state.text, String($sel.val()) === String(state.id));
            },
            templateSelection: function (state) {
                if (!state.id) return $('<span class="catsel-ph" style="opacity:0.6;"></span>').text(state.text || '');
                return catselRow(state.text, true);
            }
        });
        var pending = $sel.data('pending');
        if (pending !== undefined && pending !== null && pending !== '') {
            $sel.val(pending).trigger('change');
        }
        $sel.removeData('pending');
    }

    function buildModalFields() {
        var html = '<input type="hidden" id="crud_id">';
        html += '<div class="row">';

        var editableCols = meta.columns.filter(function (c) { return c.editable && !c.is_pk; });

        editableCols.forEach(function (c) {
            var req = c.nullable ? '' : 'required';
            var reqBadge = c.nullable ? '<span class="text-muted font-weight-normal small ml-1">(opcional)</span>' : ' <span class="text-danger">*</span>';
            var lbl = formatLabel(c.name);
            var colWidth = (editableCols.length > 3) ? 'col-md-6' : 'col-12';
            if (c.name === 'dir') colWidth = 'col-12';

            html += '<div class="' + colWidth + ' mb-3">';
            html += '<label class="form-label" for="m_' + escHtml(c.name) + '">' + escHtml(lbl) + reqBadge + '</label>';

            if (selectsConfig[c.name]) {
                html += '<select ' + req + ' class="form-control" id="m_' + escHtml(c.name) + '"><option value="">Cargando opciones...</option></select>';
            } else {
                var itype = inputType(c);
                var step = (itype === 'number' && (c.type || '').indexOf('dec') >= 0) ? ' step="0.01"' : '';
                html += '<input ' + req + ' type="' + itype + '"' + step + ' class="form-control" id="m_' + escHtml(c.name) + '" placeholder="Ingrese ' + escHtml(lbl.toLowerCase()) + '...">';
            }
            html += '</div>';
        });

        html += '</div>';
        $('#crudModalFields').html(html);

        // Cargar opciones para los selects de modal
        Object.keys(selectsConfig).forEach(function (colName) {
            var cfg = selectsConfig[colName];
            var $sel = $('#m_' + colName);
            if (Array.isArray(cfg)) {
                var htmlOpt = '<option value="">-- Seleccionar --</option>';
                cfg.forEach(function (item) {
                    var v = (typeof item === 'object' && item !== null) ? (item.id !== undefined ? item.id : item.value) : item;
                    var l = (typeof item === 'object' && item !== null) ? (item.label || item.nombre || item.text || v) : item;
                    htmlOpt += '<option value="' + escHtml(v) + '">' + escHtml(l) + '</option>';
                });
                $sel.html(htmlOpt);
                initCatsel($sel);
            } else if (typeof cfg === 'string' && cfg.length > 0) {
                $.get(cfg, function (htmlOpt) {
                    $sel.html(htmlOpt);
                    initCatsel($sel);
                });
            }
        });
    }

    function gatherFilters() {
        var q = {};
        $('.f-col').each(function () {
            var k = $(this).data('col');
            var v = $(this).val();
            if (v !== '' && v !== null) q[k] = v;
        });
        return q;
    }

    function gatherPayload() {
        var p = {};
        var id = $('#crud_id').val();
        if (id) p.id = id;
        meta.columns.forEach(function (c) {
            if (!c.editable || c.is_pk) return;
            var v = $('#m_' + c.name).val();
            p[c.name] = v;
        });
        return p;
    }

    function fillModal(row) {
        $('#crud_id').val(row[meta.pk] || row.id || '');
        meta.columns.forEach(function (c) {
            if (!c.editable || c.is_pk) return;
            var val = row[c.name] == null ? '' : row[c.name];
            if (selectsConfig[c.name]) {
                var $sel = $('#m_' + c.name);
                if ($sel.hasClass('select2-hidden-accessible')) {
                    $sel.val(val).trigger('change');
                } else {
                    $sel.data('pending', val);
                }
            } else {
                $('#m_' + c.name).val(val);
            }
        });
    }

    function clearModal() {
        $('#crud_id').val('');
        meta.columns.forEach(function (c) {
            if (!c.editable || c.is_pk) return;
            if (selectsConfig[c.name]) {
                var $sel = $('#m_' + c.name);
                $sel.removeData('pending');
                if ($sel.hasClass('select2-hidden-accessible')) {
                    $sel.val(null).trigger('change');
                }
            } else {
                $('#m_' + c.name).val('');
            }
        });
    }

    function rowIdOf(row) {
        return row != null && meta && meta.pk != null && row[meta.pk] !== undefined ? row[meta.pk] : (row && row.id);
    }

    function updateBulkUI() {
        var count = Object.keys(selectedIds).length;
        var $count = $('#bulkCount');
        if ($count.length) {
            $count.text(count + ' seleccionado' + (count === 1 ? '' : 's'));
            if (count > 0) {
                $count.removeClass('badge-info').addClass('badge-success');
            } else {
                $count.removeClass('badge-success').addClass('badge-info');
            }
        }
    }

    function applySelectionHighlight() {
        $('#catalogTable .gh-row-select').each(function () {
            var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
            var id = rowIdOf(row);
            $(this).closest('tr').toggleClass('crud-row-selected', id != null && !!selectedIds[id]);
        });
    }

    function initTable() {
        var columns = [];
        if (usesBulk) {
            urls.bulk = '../ajax/' + endpointBase + '_bulk.php';
            columns.push({
                data: null,
                orderable: false,
                className: 'text-center',
                width: '38px',
                defaultContent: '',
                render: function (data, type, row) {
                    var id = rowIdOf(row);
                    var rowJson = encodeURIComponent(JSON.stringify(row));
                    var checked = id != null && !!selectedIds[id] ? ' checked' : '';
                    return '<input type="checkbox" class="crud-checkbox gh-row-select" data-row="' + rowJson + '"' + checked + '>';
                }
            });
        }

        var metaColumns = meta.columns.map(function (c) {
            var colDef = { data: c.name, defaultContent: '' };
            if (displayConfig[c.name]) {
                colDef.render = function (data, type, row) {
                    return escHtml((row && row[displayConfig[c.name]]) || data);
                };
            } else if (c.is_pk) {
                colDef.render = function (data) {
                    return '<span class="badge-id">#' + escHtml(data) + '</span>';
                };
            }
            return colDef;
        });

        columns = columns.concat(metaColumns);

        // Actions column
        columns.push({
            data: null,
            orderable: false,
            className: 'text-center',
            width: '145px',
            render: function (data, type, row) {
                var rowData = encodeURIComponent(JSON.stringify(row));
                return '<div class="action-btn-group">' +
                       '<button type="button" class="btn btn-action-edit btn-edit-c" data-row="' + rowData + '" title="Editar registro">' +
                       '<span class="material-icons">edit</span> <span>Editar</span>' +
                       '</button>' +
                       '<button type="button" class="btn btn-action-delete btn-del-c" data-row="' + rowData + '" title="Eliminar registro">' +
                       '<span class="material-icons">delete_outline</span> <span>Eliminar</span>' +
                       '</button>' +
                       '</div>';
            }
        });

        // Build Table Header
        var head = '<tr>';
        if (usesBulk) {
            head += '<th class="text-center" style="width:38px;"><input type="checkbox" class="crud-checkbox" id="selectAllCells" title="Seleccionar todas visibles"></th>';
        }
        meta.columns.forEach(function (c) {
            head += '<th>' + escHtml(formatLabel(c.name)) + '</th>';
        });
        head += '<th class="text-center" style="width:145px;">Acciones</th></tr>';
        $('#catalogTable thead').html(head);

        dt = $('#catalogTable').DataTable({
            ajax: {
                url: urls.list,
                data: function (d) {
                    var f = gatherFilters();
                    Object.keys(f).forEach(function (k) { d[k] = f[k]; });
                }
            },
            columns: columns,
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

        dt.on('draw', function () {
            if (usesBulk) {
                $('#selectAllCells').prop('checked', false);
                applySelectionHighlight();
            }
        });

        $('#catalogTable').on('click', '.btn-edit-c', function () {
            var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
            fillModal(row);
            var idVal = row[meta.pk] || row.id || '';
            $('#modalActionTitle').text('Editar Registro #' + idVal);
            $('#modalActionSubtitle').text('Modifica los campos del registro seleccionado');
            $('#modalIcon').text('edit_note');
            $('#catalogModal').modal('show');
        });

        $('#catalogTable').on('click', '.btn-del-c', function () {
            if (!confirm('¿Está seguro de eliminar este registro? Esta acción no se puede deshacer.')) return;
            var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
            var payload = {};
            if (meta.pk && row[meta.pk] !== undefined && row[meta.pk] !== null && row[meta.pk] !== '') {
                payload.id = row[meta.pk];
            } else {
                meta.columns.forEach(function (c) { payload[c.name] = row[c.name]; });
            }
            $.post(urls.del, payload, function (res) {
                if (res && res.success) {
                    dt.ajax.reload(null, false);
                } else {
                    alert((res && res.message) ? res.message : 'Error al eliminar el registro');
                }
            }, 'json').fail(function (xhr) {
                alert('Error al comunicar con el servidor: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'));
            });
        });
    }

    if (usesBulk) {
        $('#catalogTable').on('click', '.gh-row-select', function () {
            var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
            var id = rowIdOf(row);
            if (id == null) return;
            if ($(this).prop('checked')) selectedIds[id] = true;
            else delete selectedIds[id];
            updateBulkUI();
            $(this).closest('tr').toggleClass('crud-row-selected', !!selectedIds[id]);
        });

        $('#catalogTable').on('click', '#selectAllCells', function () {
            var checked = $(this).prop('checked');
            $('#catalogTable .gh-row-select').each(function () {
                var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
                var id = rowIdOf(row);
                $(this).prop('checked', checked);
                if (id != null) {
                    if (checked) selectedIds[id] = true;
                    else delete selectedIds[id];
                }
            });
            updateBulkUI();
            applySelectionHighlight();
        });

        $('#btnApplyLongitud').on('click', function () {
            var ids = Object.keys(selectedIds);
            if (!ids.length) {
                alert('No hay registros seleccionados para aplicar');
                return;
            }
            var val = $('#bulkLongitud').val();
            if (val === '' || isNaN(val)) {
                alert('Ingrese un valor numérico para ' + formatLabel(bulkField));
                return;
            }
            var num = Math.round(parseFloat(val) * 100) / 100;
            if (num < 0) {
                alert('El valor no puede ser negativo');
                return;
            }
            $.post(urls.bulk, { ids: ids, [bulkField]: num }, function (res) {
                if (res && res.success) {
                    alert((res.message || 'Actualización exitosa') + ' aplicado a ' + res.affected + ' registro(s)');
                    selectedIds = {};
                    $('#bulkLongitud').val('');
                    updateBulkUI();
                    if (dt) dt.ajax.reload(null, false);
                } else {
                    alert((res && res.message) ? res.message : 'Error al aplicar valor masivo');
                }
            }, 'json').fail(function (xhr) {
                alert('Error de petición: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'));
            });
        });
    }

    $('#btnCrudFilter').on('click', function () { if (dt) dt.ajax.reload(); });
    $('#btnCrudClear').on('click', function () {
        $('.f-col').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).val('').trigger('change');
            } else {
                $(this).val('');
            }
        });
        if (dt) dt.ajax.reload();
    });

    $('#btnCrudNew').on('click', function () {
        clearModal();
        $('#modalActionTitle').text('Nuevo Registro');
        $('#modalActionSubtitle').text('Completa los campos para agregar un elemento a ' + title);
        $('#modalIcon').text('add_circle');
        $('#catalogModal').modal('show');
    });

    $('#btnCrudSave').on('click', function () {
        var payload = gatherPayload();
        var id = $('#crud_id').val();
        if (id) payload.id = id;
        var url = id ? urls.update : urls.create;

        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Guardando...');

        $.post(url, payload, function (res) {
            $btn.prop('disabled', false).html('<span class="material-icons align-middle mr-1" style="font-size: 1rem;">save</span> Guardar Datos');
            if (res && res.success) {
                $('#catalogModal').modal('hide');
                if (dt) dt.ajax.reload(null, false);
            } else {
                alert((res && res.message) ? res.message : 'Error al guardar los datos');
            }
        }, 'json').fail(function (xhr) {
            $btn.prop('disabled', false).html('<span class="material-icons align-middle mr-1" style="font-size: 1rem;">save</span> Guardar Datos');
            alert('Error de petición: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta del servidor'));
        });
    });

    $.getJSON(urls.meta, function (res) {
        if (!res || !res.success) {
            alert((res && res.message) ? res.message : 'No se pudo cargar metadata de la tabla');
            return;
        }
        meta = res;

        // Set Icon dynamically based on table
        if (TABLE_ICONS[table]) {
            $('#catalogHeaderIcon').text(TABLE_ICONS[table]);
        }

        $('#catalogTitle').text(title);
        $('#catalogSubtitle').text('Tabla ' + table);
        buildFilters();
        buildModalFields();
        initTable();
    }).fail(function (xhr) {
        alert('Error cargando metadata: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'));
    });
});
