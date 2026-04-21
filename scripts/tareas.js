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
    $('#tResponsable').val('');
    $('#tEstado').val('pendiente');
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
    $('#tEstado').val(d.estado);
    $('#tPrioridad').val(d.prioridad);
    $('#tInicio').val(d.fecha_inicio);
    $('#tVencimiento').val(d.fecha_vencimiento);
    $('#modalTarea').modal('show');
}

function guardarTarea() {
    if (!$('#tNombre').val()) {
        alert('El nombre es requerido');
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
        estado: $('#tEstado').val(),
        prioridad: $('#tPrioridad').val(),
        fecha_inicio: $('#tInicio').val(),
        fecha_vencimiento: $('#tVencimiento').val()
    }, function (resp) {
        if (resp.success) {
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
