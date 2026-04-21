<?php
/**
 * Vista de ejemplo: Gestión de Proyectos con DataTables
 * Path: /views/Proyectos/index.php
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proyectos</title>
    
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
        .badge-activo { background-color: #28a745; }
        .badge-pausado { background-color: #ffc107; }
        .badge-completado { background-color: #17a2b8; }
        .badge-cancelado { background-color: #dc3545; }
        
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }
        
        .btn-xs {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    </style>
</head>
<body>

<div class="container-fluid mt-5">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1><i class="fas fa-project-diagram"></i> Gestión de Proyectos</h1>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProyecto">
                <i class="fas fa-plus"></i> Nuevo Proyecto
            </button>
        </div>
    </div>

    <!-- Tabla de Proyectos -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-list"></i> Proyectos</h5>
        </div>
        <div class="card-body">
            <table id="tablaPro" class="table table-hover display nowrap w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Tareas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Nuevo/Editar Proyecto -->
<div class="modal fade" id="modalProyecto" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formProyecto">
                    <input type="hidden" id="proyectoId">
                    
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" class="form-control" id="nombreProyecto" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea class="form-control" id="descProyecto" rows="3"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado</label>
                            <select class="form-select" id="estadoProyecto">
                                <option value="activo">Activo</option>
                                <option value="pausado">Pausado</option>
                                <option value="completado">Completado</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" id="fechaInicio">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" id="fechaFin">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarProyecto()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Ver Detalles -->
<div class="modal fade" id="modalDetalles" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalles del Proyecto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="detallesContenido"></div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="DataTables/datatables.min.js"></script>

<script>
let tabla;

$(document).ready(function() {
    // Inicializar DataTable
    tabla = $('#tablaPro').DataTable({
        ajax: {
            url: 'ajax/proyectos.php',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'descripcion',
              render: function(data) {
                return data ? data.substring(0, 50) + '...' : '-';
              }
            },
            { data: 'estado',
              render: function(data) {
                return `<span class="badge badge-${data}">${data}</span>`;
              }
            },
            { data: 'fecha_inicio', defaultContent: '-' },
            { data: 'fecha_fin', defaultContent: '-' },
            { data: 'id',
              render: function(data) {
                return `<button class="btn btn-sm btn-info" onclick="verTareas(${data})">
                          <i class="fas fa-tasks"></i> Ver
                        </button>`;
              }
            },
            { data: 'id',
              render: function(data) {
                return `
                  <button class="btn btn-xs btn-warning" onclick="editarProyecto(${data})">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-xs btn-info" onclick="verDetalles(${data})">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="btn btn-xs btn-danger" onclick="eliminarProyecto(${data})">
                    <i class="fas fa-trash"></i>
                  </button>
                `;
              }
            }
        ],
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
        }
    });
    
    // Limpiar modal al cerrar
    $('#modalProyecto').on('hide.bs.modal', function() {
        $('#formProyecto')[0].reset();
        $('#proyectoId').val('');
    });
});

function limpiarForm() {
    $('#formProyecto')[0].reset();
    $('#proyectoId').val('');
}

function editarProyecto(id) {
    fetch(`ajax/proyectos.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
            $('#proyectoId').val(data.id);
            $('#nombreProyecto').val(data.nombre);
            $('#descProyecto').val(data.descripcion || '');
            $('#estadoProyecto').val(data.estado);
            $('#fechaInicio').val(data.fecha_inicio);
            $('#fechaFin').val(data.fecha_fin);
            
            const modal = new bootstrap.Modal(document.getElementById('modalProyecto'));
            modal.show();
        })
        .catch(err => alert('Error: ' + err));
}

function guardarProyecto() {
    const id = $('#proyectoId').val();
    const datos = {
        nombre: $('#nombreProyecto').val(),
        descripcion: $('#descProyecto').val(),
        estado: $('#estadoProyecto').val(),
        fecha_inicio: $('#fechaInicio').val(),
        fecha_fin: $('#fechaFin').val()
    };
    
    if (!datos.nombre) {
        alert('El nombre es requerido');
        return;
    }
    
    const url = id ? `ajax/proyectos.php?id=${id}` : 'ajax/proyectos.php';
    const metodo = id ? 'PUT' : 'POST';
    
    fetch(url, {
        method: metodo,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.mensaje);
            bootstrap.Modal.getInstance(document.getElementById('modalProyecto')).hide();
            tabla.ajax.reload();
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(err => alert('Error: ' + err));
}

function eliminarProyecto(id) {
    if (confirm('¿Está seguro de que desea eliminar este proyecto?')) {
        fetch(`ajax/proyectos.php?id=${id}`, { method: 'DELETE' })
            .then(r => r.json())
            .then(data => {
                alert(data.mensaje);
                tabla.ajax.reload();
            })
            .catch(err => alert('Error: ' + err));
    }
}

function verDetalles(id) {
    fetch(`ajax/proyectos.php?id=${id}&accion=estadisticas`)
        .then(r => r.json())
        .then(data => {
            let html = `
                <h5>${data.nombre}</h5>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Total Tareas:</strong> ${data.total_tareas || 0}</p>
                        <p><strong>Completadas:</strong> ${data.tareas_completadas || 0}</p>
                        <p><strong>En Progreso:</strong> ${data.tareas_en_progreso || 0}</p>
                        <p><strong>Pendientes:</strong> ${data.tareas_pendientes || 0}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Registros Bitácora:</strong> ${data.total_registros_bitacora || 0}</p>
                    </div>
                </div>
            `;
            $('#detallesContenido').html(html);
            const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
            modal.show();
        });
}

function verTareas(proyectoId) {
    fetch(`ajax/tareas.php?accion=por_proyecto&proyecto_id=${proyectoId}`)
        .then(r => r.json())
        .then(tareas => {
            let html = '<table class="table table-sm"><thead><tr><th>Nombre</th><th>Estado</th><th>Prioridad</th></tr></thead><tbody>';
            tareas.forEach(t => {
                html += `<tr><td>${t.nombre}</td><td>${t.estado}</td><td>${t.prioridad}</td></tr>`;
            });
            html += '</tbody></table>';
            $('#detallesContenido').html(html);
            const modal = new bootstrap.Modal(document.getElementById('modalDetalles'));
            modal.show();
        });
}
</script>

</body>
</html>
