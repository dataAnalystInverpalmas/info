var dtTareas;

$(document).ready(function () {
    dtTareas = $('#tablaTareas').DataTable({ responsive: true });
});

function filtrarTareas() {
    var tipo = $('#filtroTipo').val();
    var proy = $('#filtroProyectoTarea').val();
    dtTareas.column(1).search(tipo).column(3).search(proy).draw();
}

function abrirModalTarea() {
    $('#tId').val('');
    $('#tTipo').val('prevista');
    $('#tNombre').val('');
    $('#tDesc').val('');
    $('#tProyecto').val('');
    $('#tResponsable').val(typeof usuarioActual !== 'undefined' ? usuarioActual : '');
    $('#tSolicita').val('');
    $('#tEstado').val('pendiente');
    $('#tAvance').val(0);
    $('#tPrioridad').val('media');
    $('#tInicio').val('');
    $('#tVencimiento').val('');
    $('#modalTarea').modal('show');
}

function editarTarea(d) {
    $('#tId').val(d.id);
    $('#tTipo').val(d.tipo);
    $('#tNombre').val(d.nombre);
    $('#tDesc').val(d.descripcion);
    $('#tProyecto').val(d.proyecto_id);
    $('#tResponsable').val(d.responsable);
    $('#tSolicita').val(d.quien_solicita);
    $('#tEstado').val(d.estado);
    $('#tAvance').val(d.porcentaje_avance || 0);
    $('#tPrioridad').val(d.prioridad);
    $('#tInicio').val(d.fecha_inicio);
    $('#tVencimiento').val(d.fecha_vencimiento);
    $('#modalTarea').modal('show');
}

$('#tEstado').on('change', function () {
    var estado = $(this).val();
    if (estado === 'pendiente') {
        $('#tAvance').val(0);
    } else if (estado === 'en_progreso') {
        $('#tAvance').val(25);
    } else if (estado === 'completada') {
        $('#tAvance').val(100);
    }
});

function guardarTarea() {
    if (!$('#tNombre').val()) {
        alert('El nombre es requerido');
        return;
    }

    var avance = parseInt($('#tAvance').val(), 10);
    if (isNaN(avance) || avance < 0 || avance > 100) {
        alert('El % de avance debe estar entre 0 y 100');
        return;
    }

    $.post('ajax/tarea_crud.php', {
        accion: $('#tId').val() ? 'update' : 'create',
        id: $('#tId').val(),
        tipo: $('#tTipo').val(),
        nombre: $('#tNombre').val(),
        descripcion: $('#tDesc').val(),
        proyecto_id: $('#tProyecto').val(),
        responsable: $('#tResponsable').val(),
        quien_solicita: $('#tSolicita').val(),
        estado: $('#tEstado').val(),
        porcentaje_avance: avance,
        prioridad: $('#tPrioridad').val(),
        fecha_inicio: $('#tInicio').val(),
        fecha_vencimiento: $('#tVencimiento').val()
    }, function (resp) {
        if (resp.success) {
            if (typeof resp.porcentaje_avance !== 'undefined') {
                console.log('porcentaje_avance guardado:', resp.porcentaje_avance, 'filas_afectadas:', resp.filas_afectadas);
            }
            location.reload();
        } else {
            alert(resp.mensaje || 'Error al guardar');
        }
    }, 'json');
}

function eliminarTarea(id) {
    if (!confirm('¿Eliminar tarea?')) {
        return;
    }

    $.post('ajax/tarea_crud.php', {
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
