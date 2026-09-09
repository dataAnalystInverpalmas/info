var dtTareas;

$(document).ready(function () {
    dtTareas = $('#tablaTareas').DataTable({
        responsive: true,
        // columnas 1=ID, 2=Tipo, 10=Orden ocultas por defecto; visibles con el botón de columnas si se necesitan
        columnDefs: [{ visible: false, targets: [1, 2, 10] }]
    });

    var params = new URLSearchParams(window.location.search);
    var proyectoInicial = typeof window.gestionProyectoInicial !== 'undefined' ? window.gestionProyectoInicial : params.get('proyecto');
    var proyectoIdInicial = typeof window.gestionProyectoIdInicial !== 'undefined' ? window.gestionProyectoIdInicial : parseInt(params.get('proyecto_id') || '0', 10);
    if (proyectoInicial) {
        $('#filtroProyectoTarea').val(proyectoInicial);
    }

    filtrarTareas();

    if (params.get('nueva') === '1') {
        abrirModalTarea();
        if (proyectoIdInicial) {
            $('#tProyecto').val(String(proyectoIdInicial));
        }
    }
});

function filtrarTareas() {
    var estado = $('#filtroEstado').val();
    var prioridad = $('#filtroPrioridad').val();
    var proy = $('#filtroProyectoTarea').val();
    // Ajustar índices de columna debido a la nueva columna handle (índice 0)
    dtTareas.column(8).search(estado).column(9).search(prioridad).column(4).search(proy).draw();
}

function abrirModalTarea() {
    $('#tId').val('');
    $('#tTipo').val('prevista');
    $('#tNombre, #tDesc, #tEtapa, #tEntregable, #tEvidencia, #tObservaciones, #tDependencia').val('');
    $('#tProyecto').val('');
    $('#tResponsable').val(typeof usuarioActual !== 'undefined' ? usuarioActual : '');
    $('#tSolicita').val('');
    $('#tEstado').val('pendiente');
    $('#tAvance').val(0);
    $('#tPrioridad').val('media');
    $('#tOrden, #tInicio, #tVencimiento, #tFinReal').val('');
    $('#modalTarea').modal('show');
}

function editarTarea(id) {
    $.getJSON('ajax/tareas.php', { id: id }, function (d) {
        if (!d || !d.id) {
            alert('Tarea no encontrada');
            return;
        }
        $('#tId').val(d.id);
        $('#tTipo').val(d.tipo || 'prevista');
        $('#tNombre').val(d.nombre);
        $('#tDesc').val(d.descripcion);
        $('#tEtapa').val(d.etapa_fase || '');
        $('#tProyecto').val(d.proyecto_id || '');
        $('#tResponsable').val(d.responsable);
        $('#tSolicita').val(d.quien_solicita);
        $('#tEstado').val(d.estado);
        $('#tAvance').val(d.porcentaje_avance || 0);
        $('#tPrioridad').val(d.prioridad);
        $('#tOrden').val(d.orden_ejecucion || '');
        $('#tInicio').val(d.fecha_inicio);
        $('#tVencimiento').val(d.fecha_vencimiento);
        $('#tFinReal').val(d.fecha_fin_real || '');
        $('#tEntregable').val(d.entregable_concreto || '');
        $('#tEvidencia').val(d.evidencia_soporte || '');
        $('#tObservaciones').val(d.observaciones || '');
        $('#tDependencia').val(d.dependencia || '');
        $('#modalTarea').modal('show');
    }).fail(function () {
        alert('Error al cargar la tarea');
    });
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
        orden_ejecucion: $('#tOrden').val(),
        fecha_inicio: $('#tInicio').val(),
        fecha_vencimiento: $('#tVencimiento').val(),
        etapa_fase: $('#tEtapa').val(),
        fecha_fin_real: $('#tFinReal').val(),
        entregable_concreto: $('#tEntregable').val(),
        evidencia_soporte: $('#tEvidencia').val(),
        observaciones: $('#tObservaciones').val(),
        dependencia: $('#tDependencia').val()
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
