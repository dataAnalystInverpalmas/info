<?php
/**
 * Vista de ejemplo: Gestión de Tareas con DataTables
 * Path: /views/Tareas/index.php
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="DataTables/datatables.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    
    <style>
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .badge-urgente { background-color: #dc3545; }
        .badge-alta { background-color: #fd7e14; }
        .badge-media { background-color: #ffc107; }
        .badge-baja { background-color: #28a745; }
        
        .estado-pendiente { color: #dc3545; }
        .estado-en_progreso { color: #0dcaf0; }
        .estado-completada { color: #198754; }
        .estado-cancelada { color: #999; }
        
        .nav-tabs .nav-link.active {
            border-bottom: 3px solid #667eea !important;
        }
    </style>
</head>
<body>

<div class="container-fluid mt-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-tasks"></i> Gestión de Tareas</h1>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTarea">
                <i class="fas fa-plus"></i> Nueva Tarea
            </button>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#todas" role="tab" data-bs-toggle="tab">Todas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#pendientes" role="tab" data-bs-toggle="tab">Pendientes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#imprevistas" role="tab" data-bs-toggle="tab">Imprevistas</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#proximas" role="tab" data-bs-toggle="tab">Próximas</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Tab: Todas las tareas -->
        <div class="tab-pane fade show active" id="todas" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list"></i> Todas las Tareas</h5>
                </div>
                <div class="card-body">
                    <table id="tablaTodas" class="table table-hover display nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Proyecto</th>
                                <th>Estado</th>
                                <th>Prioridad</th>
                                <th>Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab: Pendientes -->
        <div class="tab-pane fade" id="pendientes" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-clock"></i> Tareas Pendientes</h5>
                </div>
                <div class="card-body">
                    <table id="tablaPendientes" class="table table-hover display nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Proyecto</th>
                                <th>Prioridad</th>
                                <th>Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab: Imprevistas -->
        <div class="tab-pane fade" id="imprevistas" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-exclamation-triangle"></i> Tareas Imprevistas</h5>
                </div>
                <div class="card-body">
                    <table id="tablaImprevistas" class="table table-hover display nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Prioridad</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tab: Próximas -->
        <div class="tab-pane fade" id="proximas" role="tabpanel">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-calendar-alt"></i> Próximas a Vencer</h5>
                </div>
                <div class="card-body">
                    <table id="tablaProximas" class="table table-hover display nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Proyecto</th>
                                <th>Prioridad</th>
                                <th>Vencimiento</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Nueva/Editar Tarea -->
<div class="modal fade" id="modalTarea" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTarea">
                    <input type="hidden" id="tareaId">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombreTarea" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" id="descTarea" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Proyecto (opcional)</label>
                            <select class="form-select" id="proyectoTarea">
                                <option value="">-- Sin Proyecto (Imprevista) --</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prioridad</label>
                            <select class="form-select" id="prioridadTarea">
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="estadoTarea">
                                <option value="pendiente" selected>Pendiente</option>
                                <option value="en_progreso">En Progreso</option>
                                <option value="completada">Completada</option>
                                <option value="cancelada">Cancelada</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Vencimiento</label>
                            <input type="date" class="form-control" id="fechaVencimiento">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarTarea()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Bitácora -->
<div class="modal fade" id="modalBitacora" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bitácora de Tarea</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="bitacoraContenido"></div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="DataTables/datatables.min.js"></script>

<script>
let tablaTodas, tablaPendientes, tablaImprevistas, tablaProximas;

$(document).ready(function() {
    cargarProyectos();
    inicializarDataTables();
    
    $('#modalTarea').on('hide.bs.modal', function() {
        $('#formTarea')[0].reset();
        $('#tareaId').val('');
    });
});

function cargarProyectos() {
    fetch('ajax/proyectos.php')
        .then(r => r.json())
        .then(proyectos => {
            let select = '<option value="">-- Sin Proyecto (Imprevista) --</option>';
            proyectos.forEach(p => {
                select += `<option value="${p.id}">${p.nombre}</option>`;
            });
            $('#proyectoTarea').html(select);
        });
}

function inicializarDataTables() {
    tablaTodas = $('#tablaTodas').DataTable({
        ajax: { url: 'ajax/tareas.php', dataSrc: '' },
        columns: generarColumnasTodas(),
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' }
    });
    
    tablaPendientes = $('#tablaPendientes').DataTable({
        ajax: { url: 'ajax/tareas.php?accion=pendientes', dataSrc: '' },
        columns: generarColumnasSimplificadas(),
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' }
    });
    
    tablaImprevistas = $('#tablaImprevistas').DataTable({
        ajax: { url: 'ajax/tareas.php?accion=imprevistas', dataSrc: '' },
        columns: generarColumnasImprevistas(),
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' }
    });
    
    tablaProximas = $('#tablaProximas').DataTable({
        ajax: { url: 'ajax/tareas.php?accion=proximas&dias=7', dataSrc: '' },
        columns: generarColumnasSimplificadas(),
        responsive: true,
        language: { url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json' }
    });
}

function generarColumnasTodas() {
    return [
        { data: 'id' },
        { data: 'nombre' },
        { data: 'proyecto_id', render: d => d ? 'Proyecto #' + d : 'Imprevista' },
        { data: 'estado', render: d => `<span class="estado-${d}">${d}</span>` },
        { data: 'prioridad', render: d => `<span class="badge badge-${d}">${d}</span>` },
        { data: 'fecha_vencimiento', defaultContent: '-' },
        { data: 'id', render: renderAcciones }
    ];
}

function generarColumnasSimplificadas() {
    return [
        { data: 'id' },
        { data: 'nombre' },
        { data: 'proyecto_id', render: d => d ? 'Proyecto #' + d : 'Imprevista' },
        { data: 'prioridad', render: d => `<span class="badge badge-${d}">${d}</span>` },
        { data: 'fecha_vencimiento', defaultContent: '-' },
        { data: 'id', render: renderAcciones }
    ];
}

function generarColumnasImprevistas() {
    return [
        { data: 'id' },
        { data: 'nombre' },
        { data: 'estado', render: d => `<span class="estado-${d}">${d}</span>` },
        { data: 'prioridad', render: d => `<span class="badge badge-${d}">${d}</span>` },
        { data: 'id', render: renderAcciones }
    ];
}

function renderAcciones(data) {
    return `
        <button class="btn btn-xs btn-warning" onclick="editarTarea(${data})">
            <i class="fas fa-edit"></i>
        </button>
        <button class="btn btn-xs btn-info" onclick="verBitacora(${data})">
            <i class="fas fa-history"></i>
        </button>
        <button class="btn btn-xs btn-danger" onclick="eliminarTarea(${data})">
            <i class="fas fa-trash"></i>
        </button>
    `;
}

function editarTarea(id) {
    fetch(`ajax/tareas.php?id=${id}`)
        .then(r => r.json())
        .then(tarea => {
            $('#tareaId').val(tarea.id);
            $('#nombreTarea').val(tarea.nombre);
            $('#descTarea').val(tarea.descripcion || '');
            $('#proyectoTarea').val(tarea.proyecto_id || '');
            $('#prioridadTarea').val(tarea.prioridad);
            $('#estadoTarea').val(tarea.estado);
            $('#fechaVencimiento').val(tarea.fecha_vencimiento);
            
            const modal = new bootstrap.Modal(document.getElementById('modalTarea'));
            modal.show();
        });
}

function guardarTarea() {
    const id = $('#tareaId').val();
    const datos = {
        nombre: $('#nombreTarea').val(),
        descripcion: $('#descTarea').val(),
        proyecto_id: $('#proyectoTarea').val() || null,
        prioridad: $('#prioridadTarea').val(),
        estado: $('#estadoTarea').val(),
        fecha_vencimiento: $('#fechaVencimiento').val(),
        autor: 'Usuario'
    };
    
    if (!datos.nombre) {
        alert('El nombre es requerido');
        return;
    }
    
    const url = id ? `ajax/tareas.php?id=${id}` : 'ajax/tareas.php';
    const metodo = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: metodo,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(r => r.json())
    .then(data => {
        alert(data.mensaje);
        bootstrap.Modal.getInstance(document.getElementById('modalTarea')).hide();
        recargarTablasActivas();
    });
}

function eliminarTarea(id) {
    if (confirm('¿Eliminar tarea?')) {
        fetch(`ajax/tareas.php?id=${id}`, { method: 'DELETE' })
            .then(r => r.json())
            .then(data => {
                alert(data.mensaje);
                recargarTablasActivas();
            });
    }
}

function verBitacora(tareaId) {
    fetch(`ajax/bitacora.php?accion=historial&tarea_id=${tareaId}`)
        .then(r => r.json())
        .then(registros => {
            let html = '<div class="timeline">';
            registros.forEach(r => {
                html += `
                    <div class="mb-3 pb-3" style="border-bottom: 1px solid #eee;">
                        <strong>${r.tipo_registro}</strong>
                        <p class="text-muted small">${r.fecha_registro} - ${r.autor}</p>
                        <p>${r.descripcion}</p>
                    </div>
                `;
            });
            html += '</div>';
            $('#bitacoraContenido').html(html);
            const modal = new bootstrap.Modal(document.getElementById('modalBitacora'));
            modal.show();
        });
}

function recargarTablasActivas() {
    const activeTab = document.querySelector('.tab-pane.show.active');
    if (activeTab) {
        if (activeTab.id === 'todas') tablaTodas.ajax.reload();
        else if (activeTab.id === 'pendientes') tablaPendientes.ajax.reload();
        else if (activeTab.id === 'imprevistas') tablaImprevistas.ajax.reload();
        else if (activeTab.id === 'proximas') tablaProximas.ajax.reload();
    }
}
</script>

</body>
</html>
