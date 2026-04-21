<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Jornadas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>⏰ Gestión de Jornadas</h1>
                <p>Administra los turnos de trabajo</p>
            </div>
            <a href="../" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <div id="alertas"></div>

        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title form-title">➕ Crear Nueva Jornada</h5>
                        <form id="formJornada">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre de la Jornada</label>
                                <input type="text" class="form-control" id="nombre" placeholder="Ej: Mañana" required>
                            </div>
                            <div class="mb-3">
                                <label for="hora" class="form-label">Hora</label>
                                <input type="time" class="form-control" id="hora" required>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary" id="btnCancelar" onclick="cancelarEdicion()" style="display: none;">
                                    ❌ Cancelar
                                </button>
                                <button type="submit" class="btn btn-crear">
                                    ➕ Crear Jornada
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">📋 Jornadas Registradas</h5>
                        <div id="lista"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let edicionId = null;

        document.addEventListener('DOMContentLoaded', () => {
            cargar();
            document.getElementById('formJornada').addEventListener('submit', (e) => {
                e.preventDefault();
                edicionId ? actualizar() : crear();
            });
        });

        function crear() {
            const nombre = document.getElementById('nombre').value.trim();
            const hora = document.getElementById('hora').value;

            if (!nombre) {
                mostrarAlerta('Completa el nombre de la jornada', 'warning');
                return;
            }
            if (!hora) {
                mostrarAlerta('Selecciona la hora', 'warning');
                return;
            }

            const fd = new FormData();
            fd.append('nombre', nombre);
            fd.append('hora', hora);

            fetch('../api/jornadas_crear.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Jornada creada exitosamente', 'success');
                        document.getElementById('formJornada').reset();
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function cargar() {
            fetch('../api/jornadas_leer.php')
                .then(r => r.json())
                .then(d => {
                    const lista = document.getElementById('lista');
                    if (d.success && d.data.length > 0) {
                        lista.innerHTML = d.data.map(item => `
                            <div class="task-item">
                                <div class="task-title">${escapeHtml(item.nombre)}</div>
                                <div class="task-description">
                                    ⏰ ${item.hora ? item.hora : 'Sin hora'}
                                </div>
                                <div class="task-date">📅 ${new Date(item.fecha_creacion).toLocaleDateString('es-ES')}</div>
                                <div class="task-actions">
                                    <button class="btn btn-editar" onclick="prepararEdicion(${item.id}, '${escapeAttr(item.nombre)}', '${item.hora || ''}')">✏️ Editar</button>
                                    <button class="btn btn-eliminar" onclick="confirmarEliminar(${item.id}, '${escapeAttr(item.nombre)}')">🗑️ Eliminar</button>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        lista.innerHTML = '<div class="empty-state"><p class="no-tasks">No hay jornadas registradas</p></div>';
                    }
                });
        }

        function prepararEdicion(id, nombre, hora_inicio, hora_fin) {
            edicionId = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('hora').value = hora_inicio;
            document.querySelector('.form-title').textContent = '✏️ Editar Jornada';
            document.querySelector('button[type="submit"]').textContent = '✏️ Actualizar Jornada';
            document.getElementById('btnCancelar').style.display = 'block';
            document.getElementById('nombre').focus();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function actualizar() {
            const nombre = document.getElementById('nombre').value.trim();
            const hora = document.getElementById('hora').value;

            if (!nombre) {
                mostrarAlerta('Completa el nombre de la jornada', 'warning');
                return;
            }
            if (!hora) {
                mostrarAlerta('Selecciona la hora', 'warning');
                return;
            }

            const fd = new FormData();
            fd.append('id', edicionId);
            fd.append('nombre', nombre);
            fd.append('hora', hora);

            fetch('../api/jornadas_actualizar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Jornada actualizada exitosamente', 'success');
                        cancelarEdicion();
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function confirmarEliminar(id, nombre) {
            if (confirm(`¿Eliminar "${nombre}"?`)) {
                eliminar(id);
            }
        }

        function eliminar(id) {
            const fd = new FormData();
            fd.append('id', id);

            fetch('../api/jornadas_eliminar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Jornada eliminada exitosamente', 'success');
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function cancelarEdicion() {
            edicionId = null;
            document.getElementById('formJornada').reset();
            document.querySelector('.form-title').textContent = '➕ Crear Nueva Jornada';
            document.querySelector('button[type="submit"]').textContent = '➕ Crear Jornada';
            document.getElementById('btnCancelar').style.display = 'none';
        }

        function mostrarAlerta(msg, tipo) {
            const contenedor = document.getElementById('alertas');
            const alerta = document.createElement('div');
            alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
            alerta.innerHTML = `${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
            contenedor.appendChild(alerta);
            setTimeout(() => alerta.remove(), 5000);
        }

        function escapeHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        function escapeAttr(text) {
            return text.replace(/'/g, "\\'").replace(/"/g, '\\"');
        }
    </script>
</body>
</html>
