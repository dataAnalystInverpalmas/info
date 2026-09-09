var dtSolicitudes;

$.fn.dataTable.ext.search.push(function (settings, data) {
    var solicitante = ($('#fSolicitante').val() || '').toLowerCase();
    var tipo = $('#fTipo').val() || '';
    var fechaInicio = $('#fFechaInicio').val() || '';
    var fechaFin = $('#fFechaFin').val() || '';

    var cellSolicitante = (data[1] || '').toLowerCase();
    var cellTipo = data[4] || '';
    var cellFecha = data[0] || '';

    if (solicitante && cellSolicitante.indexOf(solicitante) === -1) return false;
    if (tipo && cellTipo !== tipo) return false;
    if (fechaInicio && cellFecha < fechaInicio) return false;
    if (fechaFin && cellFecha > fechaFin) return false;

    return true;
});

$(document).ready(function () {
    $('#fFechaInicio').val(typeof fFechaInicioDefault !== 'undefined' ? fFechaInicioDefault : '');
    $('#fFechaFin').val(typeof fFechaFinDefault !== 'undefined' ? fFechaFinDefault : '');

    dtSolicitudes = $('#tablaSolicitudes').DataTable({ responsive: true });
    dtSolicitudes.draw();
});

function aplicarFiltros() {
    dtSolicitudes.draw();
}

function limpiarFiltros() {
    $('#fSolicitante').val('');
    $('#fTipo').val('');
    $('#fFechaInicio').val(typeof fFechaInicioDefault !== 'undefined' ? fFechaInicioDefault : '');
    $('#fFechaFin').val(typeof fFechaFinDefault !== 'undefined' ? fFechaFinDefault : '');
    dtSolicitudes.draw();
}

$('#fSolicitante').on('keyup', function () { dtSolicitudes.draw(); });
$('#fTipo, #fFechaInicio, #fFechaFin').on('change', function () { dtSolicitudes.draw(); });

function abrirSolicitud() {
    $('#sId, #sSolicitante, #sArea, #sTipo, #sHoras, #sDescripcion, #sObservaciones').val('');
    var hoy = new Date().toISOString().slice(0, 10);
    $('#sFecha').val(hoy);
    $('#sResponsable').val(typeof usuarioActual !== 'undefined' ? usuarioActual : '');
    $('#sEstado').val('pendiente');
    $('#modalSolicitud').modal('show');
}

function editarSolicitud(solicitud) {
    $('#sId').val(solicitud.id || '');
    $('#sFecha').val(solicitud.fecha || '');
    $('#sSolicitante').val(solicitud.solicitante || '');
    $('#sArea').val(solicitud.area || '');
    $('#sTipo').val(solicitud.tipo || '');
    $('#sHoras').val(solicitud.tiempo_invertido_horas || '');
    $('#sDescripcion').val(solicitud.descripcion || '');
    $('#sResponsable').val(solicitud.responsable || '');
    $('#sEstado').val(solicitud.estado || 'pendiente');
    $('#sObservaciones').val(solicitud.observaciones || '');
    $('#modalSolicitud').modal('show');
}

function guardarSolicitud() {
    var descripcion = $('#sDescripcion').val().trim();
    if (!descripcion) {
        alert('La descripción es requerida');
        return;
    }

    var id = $('#sId').val();
    var payload = {
        fecha: $('#sFecha').val(),
        solicitante: $('#sSolicitante').val(),
        area: $('#sArea').val(),
        tipo: $('#sTipo').val(),
        tiempo_invertido_horas: $('#sHoras').val(),
        descripcion: descripcion,
        responsable: $('#sResponsable').val(),
        estado: $('#sEstado').val(),
        observaciones: $('#sObservaciones').val()
    };

    $.ajax({
        url: 'ajax/solicitudes_extra.php' + (id ? '?id=' + encodeURIComponent(id) : ''),
        method: id ? 'PUT' : 'POST',
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        data: JSON.stringify(payload)
    }).done(function (respuesta) {
        if (respuesta.success) {
            location.reload();
        } else {
            alert(respuesta.mensaje || 'No se pudo guardar la solicitud');
        }
    }).fail(function () {
        alert('Error de conexión al guardar la solicitud');
    });
}

function eliminarSolicitud(id) {
    if (!confirm('¿Eliminar solicitud operativa?')) {
        return;
    }
    $.ajax({ url: 'ajax/solicitudes_extra.php?id=' + encodeURIComponent(id), method: 'DELETE', dataType: 'json' })
        .done(function (respuesta) {
            if (respuesta.success) {
                location.reload();
            } else {
                alert(respuesta.mensaje || 'No se pudo eliminar la solicitud');
            }
        }).fail(function () {
            alert('Error de conexión al eliminar la solicitud');
        });
}

function convertirEnTarea(id) {
    var fechaVencimiento = prompt('Fecha de vencimiento (AAAA-MM-DD):');
    if (!fechaVencimiento) return;

    $.ajax({
        url: 'ajax/solicitudes_extra.php?id=' + encodeURIComponent(id) + '&accion=convertir',
        method: 'POST',
        dataType: 'json',
        data: { fecha_vencimiento: fechaVencimiento }
    }).done(function (respuesta) {
        if (respuesta.success) {
            alert(respuesta.mensaje);
            location.reload();
        } else {
            alert(respuesta.mensaje || 'No se pudo convertir la solicitud');
        }
    }).fail(function () {
        alert('Error de conexión al convertir la solicitud');
    });
}
