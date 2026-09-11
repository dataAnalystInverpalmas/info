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

    function escHtml(v) {
        return String(v == null ? '' : v)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function inputType(col) {
        var t = (col.type || '').toLowerCase();
        if (['tinyint', 'smallint', 'mediumint', 'int', 'bigint'].indexOf(t) >= 0) return 'number';
        if (['decimal', 'float', 'double'].indexOf(t) >= 0) return 'number';
        if (t.indexOf('date') >= 0 || t === 'year') return 'text';
        return 'text';
    }

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
            if (isUrl) {
                html += '<select class="form-control form-control-sm f-col" data-col="' + escHtml(name) + '" data-url="' + escHtml(cfg) + '"></select>';
            } else {
                html += '<input type="text" class="form-control form-control-sm f-col" data-col="' + escHtml(name) + '" placeholder="Filtrar ' + escHtml(name) + '">';
            }
        });
        $('#crudFilters').html(html);

        $('#crudFilters select.f-col[data-url]').each(function () {
            var $sel = $(this);
            var url = $sel.data('url');
            $.get(url, function (optHtml) {
                if (optHtml) $sel.html(optHtml);
                $sel.select2({
                    width: '100%',
                    placeholder: 'Filtrar ' + $sel.data('col') + '...',
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
        var ico = $('<span class="catsel-ico"></span>').text(checked ? '\u2611' : '\u2610');
        if (!checked) ico.addClass('muted');
        return $('<div class="catsel-row"></div>').append(ico).append($('<span></span>').text(text));
    }

    function initCatsel($sel) {
        $sel.select2({
            width: '100%',
            placeholder: 'Seleccionar / buscar...',
            allowClear: true,
            templateResult: function (state) {
                if (!state.id) return $('<span class="catsel-ph"></span>').text(state.text || '');
                return catselRow(state.text, String($sel.val()) === String(state.id));
            },
            templateSelection: function (state) {
                if (!state.id) return $('<span class="catsel-ph"></span>').text(state.text || '');
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
        var html = '';
        html += '<input type="hidden" id="crud_id">';
        meta.columns.forEach(function (c) {
            if (!c.editable) return;
            if (c.is_pk) return;
            var req = c.nullable ? '' : 'required';
            if (selectsConfig[c.name]) {
                html += '<div class="form-group">' +
                    '<label class="small text-muted mb-0">' + escHtml(c.name) + '</label>' +
                    '<select ' + req + ' class="form-control" id="m_' + escHtml(c.name) + '"><option value="">Cargando...</option></select>' +
                    '</div>';
            } else {
                html += '<div class="form-group">' +
                    '<label class="small text-muted mb-0">' + escHtml(c.name) + '</label>' +
                    '<input ' + req + ' type="' + inputType(c) + '" class="form-control" id="m_' + escHtml(c.name) + '">' +
                    '</div>';
            }
        });
        $('#crudModalFields').html(html);
        // Cargar opciones para los selects
        Object.keys(selectsConfig).forEach(function (colName) {
            var url = selectsConfig[colName];
            $.get(url, function (html) {
                var $sel = $('#m_' + colName);
                $sel.html(html);
                initCatsel($sel);
            });
        });
    }

    function gatherFilters() {
        var q = {};
        $('.f-col').each(function () {
            var k = $(this).data('col');
            var v = $(this).val();
            if (v !== '') q[k] = v;
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
        if ($count.length) $count.text(count + ' seleccionado' + (count === 1 ? '' : 's'));
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
                defaultContent: '',
                render: function (data, type, row) {
                    var id = rowIdOf(row);
                    var rowJson = encodeURIComponent(JSON.stringify(row));
                    var checked = id != null && !!selectedIds[id] ? ' checked' : '';
                    return '<input type="checkbox" class="gh-row-select" data-row="' + rowJson + '"' + checked + '>';
                }
            });
        }
        var metaColumns = meta.columns.map(function (c) {
            var colDef = { data: c.name, defaultContent: '' };
            if (displayConfig[c.name]) {
                colDef.render = function (data, type, row) {
                    return escHtml((row && row[displayConfig[c.name]]) || data);
                };
            }
            return colDef;
        });
        columns = columns.concat(metaColumns);
        columns.push({
            data: null,
            orderable: false,
            render: function (data, type, row) {
                return '<button class="btn btn-sm btn-info btn-edit-c mr-1" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Editar</button>' +
                       '<button class="btn btn-sm btn-danger btn-del-c" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Eliminar</button>';
            }
        });

        var head = '<tr>';
        if (usesBulk) head += '<th class="text-center" style="width:35px;"><input type="checkbox" id="selectAllCells" title="Seleccionar todos visibles"></th>';
        meta.columns.forEach(function (c) { head += '<th>' + escHtml(c.name) + '</th>'; });
        head += '<th>Acciones</th></tr>';
        $('#catalogTable thead').html(head);

        dt = $('#catalogTable').DataTable({
            ajax: {
                url: urls.list,
                data: function (d) {
                    var f = gatherFilters();
                    Object.keys(f).forEach(function (k) { d[k] = f[k]; });
                }
            },
            columns: columns
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
            $('#catalogModal').modal('show');
        });

        $('#catalogTable').on('click', '.btn-del-c', function () {
            if (!confirm('¿Eliminar registro?')) return;
            var row = JSON.parse(decodeURIComponent($(this).attr('data-row') || '%7B%7D'));
            var payload = {};
            if (meta.pk && row[meta.pk] !== undefined && row[meta.pk] !== null && row[meta.pk] !== '') {
                payload.id = row[meta.pk];
            } else {
                meta.columns.forEach(function (c) { payload[c.name] = row[c.name]; });
            }
            $.post(urls.del, payload, function (res) {
                if (res && res.success) dt.ajax.reload(null, false);
                else alert((res && res.message) ? res.message : 'Error al eliminar');
            }, 'json');
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
                alert('No hay registros seleccionados');
                return;
            }
            var val = $('#bulkLongitud').val();
            if (val === '' || isNaN(val)) {
                alert('Ingrese un valor de ' + bulkField);
                return;
            }
            var num = Math.round(parseFloat(val) * 100) / 100;
            if (num < 0) {
                alert('El valor no puede ser negativo');
                return;
            }
            $.post(urls.bulk, { ids: ids, [bulkField]: num }, function (res) {
                if (res && res.success) {
                    alert((res.message || '') + ' aplicado a ' + res.affected + ' registro(s)');
                    selectedIds = {};
                    $('#bulkLongitud').val('');
                    if (dt) dt.ajax.reload(null, false);
                } else {
                    alert((res && res.message) ? res.message : 'Error al aplicar');
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
        $('#catalogModal').modal('show');
    });

    $('#btnCrudSave').on('click', function () {
        var payload = gatherPayload();
        var id = $('#crud_id').val();
        if (id) payload.id = id;
        var url = id ? urls.update : urls.create;

        $.post(url, payload, function (res) {
            if (res && res.success) {
                $('#catalogModal').modal('hide');
                if (dt) dt.ajax.reload(null, false);
            } else {
                alert((res && res.message) ? res.message : 'Error al guardar');
            }
        }, 'json').fail(function (xhr) {
            alert('Error de petición: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'));
        });
    });

    $.getJSON(urls.meta, function (res) {
        if (!res || !res.success) {
            alert((res && res.message) ? res.message : 'No se pudo cargar metadata');
            return;
        }
        meta = res;
        $('#catalogTitle').text(title);
        $('#catalogSubtitle').text('Tabla ' + table);
        buildFilters();
        buildModalFields();
        initTable();
    }).fail(function (xhr) {
        alert('Error cargando metadata: ' + (xhr && xhr.responseText ? xhr.responseText : 'sin respuesta'));
    });
});
