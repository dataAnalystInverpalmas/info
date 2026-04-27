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
    var selectsConfig = {};
    try { selectsConfig = JSON.parse($root.attr('data-selects') || '{}'); } catch(e) { selectsConfig = {}; }

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
        meta.columns.forEach(function (c) {
            html += '<input type="text" class="form-control form-control-sm mr-1 mb-1 f-col" data-col="' + escHtml(c.name) + '" placeholder="Filtrar ' + escHtml(c.name) + '">';
        });
        $('#crudFilters').html(html);
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
                $('#m_' + colName).html(html);
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
            $('#m_' + c.name).val(row[c.name] == null ? '' : row[c.name]);
        });
    }

    function clearModal() {
        $('#crud_id').val('');
        meta.columns.forEach(function (c) {
            if (!c.editable || c.is_pk) return;
            $('#m_' + c.name).val('');
        });
    }

    function initTable() {
        var columns = meta.columns.map(function (c) {
            return { data: c.name, defaultContent: '' };
        });

        columns.push({
            data: null,
            orderable: false,
            render: function (data, type, row) {
                return '<button class="btn btn-sm btn-info btn-edit-c mr-1" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Editar</button>' +
                       '<button class="btn btn-sm btn-danger btn-del-c" data-row="' + encodeURIComponent(JSON.stringify(row)) + '">Eliminar</button>';
            }
        });

        var head = '<tr>';
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

    $('#btnCrudFilter').on('click', function () { if (dt) dt.ajax.reload(); });
    $('#btnCrudClear').on('click', function () {
        $('.f-col').val('');
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
