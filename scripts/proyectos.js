var dtProyectos;
var proyectoNotasActual = null;
var proyectoLogrosActual = null;
var proyectoRiesgosActual = null;
var logrosCache = [];
var riesgosCache = [];

$(document).ready(function () {
    dtProyectos = $('#tablaProyectos').DataTable({ responsive: true });

    // Evita que quede foco dentro de un modal oculto (warning aria-hidden en navegadores modernos).
    $('#modalNotasProyecto, #modalLogrosProyecto, #modalRiesgosProyecto').on('hide.bs.modal', function () {
        if (this.contains(document.activeElement)) {
            document.activeElement.blur();
        }
    });

    $('#modalNotasProyecto, #modalLogrosProyecto, #modalRiesgosProyecto').on('hidden.bs.modal', function () {
        var tabla = document.getElementById('tablaProyectos');
        if (tabla && typeof tabla.focus === 'function') {
            tabla.setAttribute('tabindex', '-1');
            tabla.focus();
        } else if (document.body && typeof document.body.focus === 'function') {
            document.body.focus();
        }
    });
});

function filtrarProyectos() {
    var cat = $('#filtroCategoria').val();
    dtProyectos.column(1).search(cat).draw();
}

function abrirModalProyecto() {
    $('#pId').val('');
    $('#pCategoria').val('');
    $('#pNombre').val('');
    $('#pDesc').val('');
    $('#pEstado').val('activo');
    $('#pInicio').val('');
    $('#pFin').val('');
    $('#modalProyecto').modal('show');
}

function editarProyecto(id, categoria, nombre, descripcion, estado, inicio, fin) {
    $('#pId').val(id);
    $('#pCategoria').val(categoria);
    $('#pNombre').val(nombre);
    $('#pDesc').val(descripcion);
    $('#pEstado').val(estado);
    $('#pInicio').val(inicio);
    $('#pFin').val(fin);
    $('#modalProyecto').modal('show');
}

function guardarProyecto() {
    if (!$('#pNombre').val()) {
        alert('El nombre es requerido');
        return;
    }

    $.post('ajax/proyecto_crud.php', {
        accion: $('#pId').val() ? 'update' : 'create',
        id: $('#pId').val(),
        categoria: $('#pCategoria').val(),
        nombre: $('#pNombre').val(),
        descripcion: $('#pDesc').val(),
        estado: $('#pEstado').val(),
        fecha_inicio: $('#pInicio').val(),
        fecha_fin: $('#pFin').val()
    }, function (resp) {
        if (resp.success) {
            location.reload();
        } else {
            alert(resp.mensaje || 'Error al guardar');
        }
    }, 'json');
}

function eliminarProyecto(id) {
    if (!confirm('¿Eliminar proyecto?')) {
        return;
    }

    $.post('ajax/proyecto_crud.php', {
        accion: 'delete',
        id: id
    }, function (resp) {
        if (resp.success) {
            location.reload();
        } else {
            alert(resp.mensaje || 'Error al eliminar');
        }
    }, 'json');
}

function abrirNotasProyecto(id, nombre) {
    proyectoNotasActual = id;
    $('#notaProyectoId').val(id);
    $('#notasProyectoTitulo').text('Notas del proyecto: ' + nombre);
    $('#notaDescripcion').val('');
    $('#listaNotasProyecto').empty();
    $('#sinNotasProyecto').show();
    $('#modalNotasProyecto').modal('show');
    cargarNotasProyecto(id);
}

function cargarNotasProyecto(id) {
    if (!id) return;
    $.getJSON('ajax/bitacora.php?accion=notas_proyecto&proyecto_id=' + encodeURIComponent(id), function (resp) {
        var lista = $('#listaNotasProyecto').empty();
        var registros = Array.isArray(resp) ? resp : (resp.data || []);

        if (registros.length === 0) {
            $('#sinNotasProyecto').show();
            return;
        }

        $('#sinNotasProyecto').hide();
        registros.forEach(function (r) {
            var item = $('<div>').addClass('border-bottom pb-2 mb-2');
            item.append(
                $('<div>').addClass('d-flex justify-content-between align-items-start').append(
                    $('<strong>').text('📝 Nota'),
                    $('<small>').addClass('text-muted').text((r.autor || 'Sistema') + ' · ' + (r.fecha_registro || ''))
                )
            );
            item.append($('<div>').addClass('mt-1').text(r.descripcion || ''));
            lista.append(item);
        });
    });
}

function guardarNotaProyecto() {
    var proyectoId = $('#notaProyectoId').val();
    var descripcion = $('#notaDescripcion').val().trim();

    if (!proyectoId) {
        alert('No se encontró el proyecto seleccionado');
        return;
    }
    if (!descripcion) {
        alert('La nota no puede estar vacía');
        return;
    }

    $.ajax({
        url: 'ajax/bitacora.php',
        method: 'POST',
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        data: JSON.stringify({
            proyecto_id: proyectoId,
            tipo_registro: 'nota',
            descripcion: descripcion,
            autor: typeof usuarioActual !== 'undefined' && usuarioActual ? usuarioActual : 'Sistema'
        }),
        success: function (resp) {
            if (resp.success) {
                $('#notaDescripcion').val('');
                cargarNotasProyecto(proyectoId);
            } else {
                alert(resp.mensaje || 'Error al guardar la nota');
            }
        },
        error: function () {
            alert('Error de conexión al guardar la nota');
        }
    });
}

function abrirLogrosProyecto(id, nombre) {
    proyectoLogrosActual = id;
    logrosCache = [];

    $('#logroProyectoId').val(id);
    $('#logroId').val('');
    $('#logroDescripcion').val('');
    $('#logroImpacto').val('');
    $('#logroFecha').val('');
    $('#logroEstado').val('registrado');
    $('#logrosProyectoTitulo').text('Logros del proyecto: ' + nombre);
    $('#listaLogrosProyecto').empty();
    $('#sinLogrosProyecto').show();

    $('#modalLogrosProyecto').modal('show');
    cargarLogrosProyecto(id);
}

function cargarLogrosProyecto(id) {
    if (!id) return;

    $.getJSON('ajax/proyecto_logros.php?proyecto_id=' + encodeURIComponent(id), function (resp) {
        var lista = $('#listaLogrosProyecto').empty();
        var registros = Array.isArray(resp) ? resp : (resp.data || []);
        logrosCache = registros;

        if (registros.length === 0) {
            $('#sinLogrosProyecto').show();
            return;
        }

        $('#sinLogrosProyecto').hide();
        registros.forEach(function (r) {
            var item = $('<div>').addClass('border rounded p-2 mb-2');

            var cabecera = $('<div>').addClass('d-flex justify-content-between align-items-start');
            cabecera.append($('<strong>').text('🏁 Logro'));
            cabecera.append($('<small>').addClass('text-muted').text((r.autor || 'Sistema') + ' · ' + (r.fecha_creacion || '')));
            item.append(cabecera);

            item.append($('<div>').addClass('mt-1').text(r.descripcion || ''));

            var meta = $('<div>').addClass('small text-muted mt-1');
            meta.text('Impacto: ' + (r.impacto || '-') + ' | Fecha: ' + (r.fecha_logro || '-') + ' | Estado: ' + (r.estado || '-'));
            item.append(meta);

            var acciones = $('<div>').addClass('text-right mt-2');
            acciones.append($('<button>').addClass('btn btn-xs btn-warning mr-1').text('Editar').attr('type', 'button').on('click', function () {
                cargarLogroEnFormulario(r.id);
            }));
            acciones.append($('<button>').addClass('btn btn-xs btn-danger').text('Eliminar').attr('type', 'button').on('click', function () {
                eliminarLogroProyecto(r.id);
            }));
            item.append(acciones);

            lista.append(item);
        });
    }).fail(function () {
        alert('Error de conexión al cargar logros');
    });
}

function cargarLogroEnFormulario(id) {
    var logro = (logrosCache || []).find(function (x) { return String(x.id) === String(id); });
    if (!logro) return;

    $('#logroId').val(logro.id || '');
    $('#logroDescripcion').val(logro.descripcion || '');
    $('#logroImpacto').val(logro.impacto || '');
    $('#logroFecha').val(logro.fecha_logro || '');
    $('#logroEstado').val(logro.estado || 'registrado');
}

function guardarLogroProyecto() {
    var proyectoId = $('#logroProyectoId').val();
    var id = $('#logroId').val();
    var payload = {
        proyecto_id: proyectoId,
        descripcion: ($('#logroDescripcion').val() || '').trim(),
        impacto: ($('#logroImpacto').val() || '').trim(),
        fecha_logro: $('#logroFecha').val(),
        estado: $('#logroEstado').val(),
        autor: typeof usuarioActual !== 'undefined' && usuarioActual ? usuarioActual : 'Sistema'
    };

    if (!proyectoId) {
        alert('No se encontró el proyecto seleccionado');
        return;
    }
    if (!payload.descripcion) {
        alert('La descripción del logro es requerida');
        return;
    }

    var method = id ? 'PUT' : 'POST';
    var url = 'ajax/proyecto_logros.php' + (id ? ('?id=' + encodeURIComponent(id)) : '');

    $.ajax({
        url: url,
        method: method,
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        data: JSON.stringify(payload),
        success: function (resp) {
            if (resp.success) {
                $('#logroId').val('');
                $('#logroDescripcion').val('');
                $('#logroImpacto').val('');
                $('#logroFecha').val('');
                $('#logroEstado').val('registrado');
                cargarLogrosProyecto(proyectoId);
            } else {
                alert(resp.mensaje || 'Error al guardar el logro');
            }
        },
        error: function () {
            alert('Error de conexión al guardar el logro');
        }
    });
}

function eliminarLogroProyecto(id) {
    if (!confirm('¿Eliminar logro?')) {
        return;
    }

    var proyectoId = $('#logroProyectoId').val();
    $.ajax({
        url: 'ajax/proyecto_logros.php?id=' + encodeURIComponent(id),
        method: 'DELETE',
        dataType: 'json',
        success: function (resp) {
            if (resp.success) {
                cargarLogrosProyecto(proyectoId);
            } else {
                alert(resp.mensaje || 'Error al eliminar logro');
            }
        },
        error: function () {
            alert('Error de conexión al eliminar logro');
        }
    });
}

function abrirRiesgosProyecto(id, nombre) {
    proyectoRiesgosActual = id;
    riesgosCache = [];

    $('#riesgoProyectoId').val(id);
    $('#riesgoId').val('');
    $('#riesgoDescripcion').val('');
    $('#riesgoProbabilidad').val('media');
    $('#riesgoImpacto').val('medio');
    $('#riesgoEstado').val('abierto');
    $('#riesgoResponsable').val('');
    $('#riesgoPlan').val('');
    $('#riesgoFechaCompromiso').val('');
    $('#riesgosProyectoTitulo').text('Riesgos del proyecto: ' + nombre);
    $('#listaRiesgosProyecto').empty();
    $('#sinRiesgosProyecto').show();

    $('#modalRiesgosProyecto').modal('show');
    cargarRiesgosProyecto(id);
}

function cargarRiesgosProyecto(id) {
    if (!id) return;

    $.getJSON('ajax/proyecto_riesgos.php?proyecto_id=' + encodeURIComponent(id), function (resp) {
        var lista = $('#listaRiesgosProyecto').empty();
        var registros = Array.isArray(resp) ? resp : (resp.data || []);
        riesgosCache = registros;

        if (registros.length === 0) {
            $('#sinRiesgosProyecto').show();
            return;
        }

        $('#sinRiesgosProyecto').hide();
        registros.forEach(function (r) {
            var item = $('<div>').addClass('border rounded p-2 mb-2');

            var cabecera = $('<div>').addClass('d-flex justify-content-between align-items-start');
            cabecera.append($('<strong>').text('⚠ Riesgo'));
            cabecera.append($('<small>').addClass('text-muted').text(r.fecha_creacion || ''));
            item.append(cabecera);

            item.append($('<div>').addClass('mt-1').text(r.descripcion || ''));

            var meta = $('<div>').addClass('small text-muted mt-1');
            meta.text('Probabilidad: ' + (r.probabilidad || '-') + ' | Impacto: ' + (r.impacto || '-') + ' | Estado: ' + (r.estado || '-'));
            item.append(meta);

            var meta2 = $('<div>').addClass('small text-muted mt-1');
            meta2.text('Responsable: ' + (r.responsable || '-') + ' | Compromiso: ' + (r.fecha_compromiso || '-'));
            item.append(meta2);

            if (r.plan_mitigacion) {
                item.append($('<div>').addClass('small mt-1').text('Plan: ' + r.plan_mitigacion));
            }

            var acciones = $('<div>').addClass('text-right mt-2');
            acciones.append($('<button>').addClass('btn btn-xs btn-warning mr-1').text('Editar').attr('type', 'button').on('click', function () {
                cargarRiesgoEnFormulario(r.id);
            }));
            acciones.append($('<button>').addClass('btn btn-xs btn-danger').text('Eliminar').attr('type', 'button').on('click', function () {
                eliminarRiesgoProyecto(r.id);
            }));
            item.append(acciones);

            lista.append(item);
        });
    }).fail(function () {
        alert('Error de conexión al cargar riesgos');
    });
}

function cargarRiesgoEnFormulario(id) {
    var riesgo = (riesgosCache || []).find(function (x) { return String(x.id) === String(id); });
    if (!riesgo) return;

    $('#riesgoId').val(riesgo.id || '');
    $('#riesgoDescripcion').val(riesgo.descripcion || '');
    $('#riesgoProbabilidad').val(riesgo.probabilidad || 'media');
    $('#riesgoImpacto').val(riesgo.impacto || 'medio');
    $('#riesgoEstado').val(riesgo.estado || 'abierto');
    $('#riesgoResponsable').val(riesgo.responsable || '');
    $('#riesgoPlan').val(riesgo.plan_mitigacion || '');
    $('#riesgoFechaCompromiso').val(riesgo.fecha_compromiso || '');
}

function guardarRiesgoProyecto() {
    var proyectoId = $('#riesgoProyectoId').val();
    var id = $('#riesgoId').val();
    var payload = {
        proyecto_id: proyectoId,
        descripcion: ($('#riesgoDescripcion').val() || '').trim(),
        probabilidad: $('#riesgoProbabilidad').val(),
        impacto: $('#riesgoImpacto').val(),
        estado: $('#riesgoEstado').val(),
        responsable: ($('#riesgoResponsable').val() || '').trim(),
        plan_mitigacion: ($('#riesgoPlan').val() || '').trim(),
        fecha_compromiso: $('#riesgoFechaCompromiso').val()
    };

    if (!proyectoId) {
        alert('No se encontró el proyecto seleccionado');
        return;
    }
    if (!payload.descripcion) {
        alert('La descripción del riesgo es requerida');
        return;
    }

    var method = id ? 'PUT' : 'POST';
    var url = 'ajax/proyecto_riesgos.php' + (id ? ('?id=' + encodeURIComponent(id)) : '');

    $.ajax({
        url: url,
        method: method,
        contentType: 'application/json; charset=utf-8',
        dataType: 'json',
        data: JSON.stringify(payload),
        success: function (resp) {
            if (resp.success) {
                $('#riesgoId').val('');
                $('#riesgoDescripcion').val('');
                $('#riesgoProbabilidad').val('media');
                $('#riesgoImpacto').val('medio');
                $('#riesgoEstado').val('abierto');
                $('#riesgoResponsable').val('');
                $('#riesgoPlan').val('');
                $('#riesgoFechaCompromiso').val('');
                cargarRiesgosProyecto(proyectoId);
            } else {
                alert(resp.mensaje || 'Error al guardar el riesgo');
            }
        },
        error: function () {
            alert('Error de conexión al guardar el riesgo');
        }
    });
}

function eliminarRiesgoProyecto(id) {
    if (!confirm('¿Eliminar riesgo?')) {
        return;
    }

    var proyectoId = $('#riesgoProyectoId').val();
    $.ajax({
        url: 'ajax/proyecto_riesgos.php?id=' + encodeURIComponent(id),
        method: 'DELETE',
        dataType: 'json',
        success: function (resp) {
            if (resp.success) {
                cargarRiesgosProyecto(proyectoId);
            } else {
                alert(resp.mensaje || 'Error al eliminar riesgo');
            }
        },
        error: function () {
            alert('Error de conexión al eliminar riesgo');
        }
    });
}
