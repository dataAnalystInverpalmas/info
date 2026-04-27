/**
 * scripts/tarea_panel.js
 * Panel de imágenes + bitácora para una tarea específica.
 */
var _panelTareaId = null;

function abrirPanelTarea(tarea_id, nombre) {
    _panelTareaId = tarea_id;
    $('#panelTareaTitulo').text('#' + tarea_id + ' — ' + nombre);
    $('#galeriaImagenes').empty();
    $('#listaBitacora').empty();
    $('#sinImagenes').show();
    $('#sinBitacora').show();

    // Activar primera pestaña
    $('#tabsPanel a:first').tab('show');

    cargarImagenes();
    cargarBitacoraTarea();

    $('#modalPanelTarea').modal('show');
}

// ── Imágenes ──────────────────────────────────────────────────────────────────

function cargarImagenes() {
    if (!_panelTareaId) return;
    $.getJSON('ajax/tarea_imagenes.php?accion=list&tarea_id=' + _panelTareaId, function (resp) {
        var galeria = $('#galeriaImagenes').empty();
        if (resp.success && resp.data.length > 0) {
            $('#sinImagenes').hide();
            resp.data.forEach(function (img) {
                var card = $('<div>').css({
                    position: 'relative',
                    display: 'inline-block',
                    margin: '4px'
                });
                var thumb = $('<img>')
                    .attr('src', img.url)
                    .attr('title', img.nombre_original)
                    .css({ width: '100px', height: '80px', objectFit: 'cover', borderRadius: '4px', cursor: 'zoom-in', border: '1px solid #ddd' })
                    .on('click', function () { abrirLightbox(img.url); });
                var btnDel = $('<button>')
                    .addClass('btn btn-xs btn-danger')
                    .css({ position: 'absolute', top: '2px', right: '2px', padding: '1px 4px', fontSize: '10px', lineHeight: 1 })
                    .html('&times;')
                    .on('click', function (e) {
                        e.stopPropagation();
                        eliminarImagen(img.id);
                    });
                var nombre = $('<div>').css({ fontSize: '10px', maxWidth: '100px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap', color: '#555' })
                    .text(img.nombre_original);
                card.append(thumb, btnDel, nombre);
                galeria.append(card);
            });
        } else {
            $('#sinImagenes').show();
        }
    });
}

function subirImagen() {
    var file = $('#inputImagen')[0].files[0];
    if (!file) return;
    var fd = new FormData();
    fd.append('accion', 'upload');
    fd.append('tarea_id', _panelTareaId);
    fd.append('imagen', file);

    $.ajax({
        url: 'ajax/tarea_imagenes.php',
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function (resp) {
            if (resp.success) {
                cargarImagenes();
            } else {
                alert('Error: ' + (resp.mensaje || 'No se pudo subir'));
            }
        },
        error: function () { alert('Error de conexión al subir imagen.'); }
    });
    // Limpiar input para permitir mismo archivo
    $('#inputImagen').val('');
}

function eliminarImagen(img_id) {
    if (!confirm('¿Eliminar esta imagen?')) return;
    $.post('ajax/tarea_imagenes.php', { accion: 'delete', id: img_id, tarea_id: _panelTareaId }, function (resp) {
        if (resp.success) {
            cargarImagenes();
        } else {
            alert(resp.mensaje || 'No se pudo eliminar');
        }
    }, 'json');
}

function abrirLightbox(url) {
    $('#lightboxImg').attr('src', url);
    $('#lightbox').css('display', 'flex');
}

function cerrarLightbox() {
    $('#lightbox').hide();
    $('#lightboxImg').attr('src', '');
}

// ── Bitácora de tarea ─────────────────────────────────────────────────────────

function cargarBitacoraTarea() {
    if (!_panelTareaId) return;
    $.getJSON('ajax/bitacora.php?accion=historial&tarea_id=' + _panelTareaId, function (resp) {
        var lista = $('#listaBitacora').empty();
        var registros = Array.isArray(resp) ? resp : (resp.data || []);
        if (registros.length === 0) {
            $('#sinBitacora').show();
            return;
        }
        $('#sinBitacora').hide();
        registros.forEach(function (r) {
            var iconos = { creacion: '🟢', actualizacion: '✏️', imagen: '🖼️', nota: '📝' };
            var icono = iconos[r.tipo_registro] || '📋';

            var item = $('<div>').addClass('border-bottom pb-2 mb-2');

            var encabezado = $('<div>').addClass('d-flex justify-content-between');
            encabezado.append(
                $('<span>').html('<strong>' + icono + ' ' + escapeHtml(r.tipo_registro) + '</strong>')
            );
            encabezado.append(
                $('<small>').addClass('text-muted').text(r.autor + ' · ' + r.fecha_registro)
            );
            item.append(encabezado);
            item.append($('<div>').text(r.descripcion));

            // Mostrar diff de campos
            if (r.cambios_json) {
                var cambios;
                try { cambios = JSON.parse(r.cambios_json); } catch(e) { cambios = null; }
                if (cambios && Object.keys(cambios).length > 0) {
                    var det = $('<details>').addClass('mt-1');
                    det.append($('<summary>').addClass('small text-muted').text('Campos modificados'));
                    var tbl = $('<table>').addClass('table table-sm table-bordered mb-0 small');
                    tbl.append($('<thead>').html('<tr><th>Campo</th><th>Antes</th><th>Después</th></tr>'));
                    var tbody = $('<tbody>');
                    $.each(cambios, function (campo, val) {
                        var tr = $('<tr>');
                        tr.append($('<td>').text(campo));
                        tr.append($('<td>').addClass('text-danger').text(val.antes ?? '—'));
                        tr.append($('<td>').addClass('text-success').text(val.despues ?? '—'));
                        tbody.append(tr);
                    });
                    tbl.append(tbody);
                    det.append(tbl);
                    item.append(det);
                }
            }

            lista.append(item);
        });
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}
