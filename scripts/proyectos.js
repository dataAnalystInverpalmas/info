var dtProyectos;

$(document).ready(function () {
    dtProyectos = $('#tablaProyectos').DataTable({ responsive: true });
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
