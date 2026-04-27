<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Áreas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <!-- Header con botón volver -->
        <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>📍 Gestión de Áreas</h1>
                <p>Administra las áreas y fincas</p>
            </div>
            <a href="../" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <!-- Alertas -->
        <div id="alertas"></div>

        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title form-title">➕ Crear Nueva Área</h5>
                        <form id="formArea">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Área</label>
                                <input type="text" class="form-control" id="nombre" placeholder="Ej: Administrativa" required>
                            </div>
                            <div class="mb-3">
                                <label for="finca" class="form-label">Finca</label>
                                <input type="text" class="form-control" id="finca" placeholder="Ej: Finca Central" required>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary" id="btnCancelar" onclick="cancelarEdicion()" style="display: none;">
                                    ❌ Cancelar
                                </button>
                                <button type="submit" class="btn btn-crear">
                                    ➕ Crear Área
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">📋 Áreas Registradas</h5>
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
            document.getElementById('formArea').addEventListener('submit', (e) => {
                e.preventDefault();
                edicionId ? actualizar() : crear();
            });
        });

        function crear() {
            const nombre = document.getElementById('nombre').value.trim();
            const finca = document.getElementById('finca').value.trim();

            if (!nombre || !finca) {
                mostrarAlerta('Completa todos los campos', 'warning');
                return;
            }

            const fd = new FormData();
            fd.append('nombre', nombre);
            fd.append('finca', finca);

            fetch('../api/areas_crear.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Área creada exitosamente', 'success');
                        document.getElementById('formArea').reset();
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function cargar() {
            fetch('../api/areas_leer.php')
                .then(r => r.json())
                .then(d => {
                    const lista = document.getElementById('lista');
                    if (d.success && d.data.length > 0) {
                        lista.innerHTML = d.data.map(item => `
                            <div class="task-item">
                                <div class="task-title">${escapeHtml(item.nombre)}</div>
                                <div class="task-description">📍 Finca: ${escapeHtml(item.finca)}</div>
                                <div class="task-date">📅 ${new Date(item.fecha_creacion).toLocaleDateString('es-ES')}</div>
                                <div class="task-actions">
                                    <button class="btn btn-editar" onclick="prepararEdicion(${item.id}, '${escapeAttr(item.nombre)}', '${escapeAttr(item.finca)}')">✏️ Editar</button>
                                    <button class="btn btn-eliminar" onclick="confirmarEliminar(${item.id}, '${escapeAttr(item.nombre)}')">🗑️ Eliminar</button>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        lista.innerHTML = '<div class="empty-state"><p class="no-tasks">No hay áreas registradas</p></div>';
                    }
                });
        }

        function prepararEdicion(id, nombre, finca) {
            edicionId = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('finca').value = finca;
            document.querySelector('.form-title').textContent = '✏️ Editar Área';
            document.querySelector('button[type="submit"]').textContent = '✏️ Actualizar Área';
            document.getElementById('btnCancelar').style.display = 'block';
            document.getElementById('nombre').focus();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function actualizar() {
            const nombre = document.getElementById('nombre').value.trim();
            const finca = document.getElementById('finca').value.trim();

            if (!nombre || !finca) {
                mostrarAlerta('Completa todos los campos', 'warning');
                return;
            }

            const fd = new FormData();
            fd.append('id', edicionId);
            fd.append('nombre', nombre);
            fd.append('finca', finca);

            fetch('../api/areas_actualizar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Área actualizada exitosamente', 'success');
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

            fetch('../api/areas_eliminar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Área eliminada exitosamente', 'success');
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function cancelarEdicion() {
            edicionId = null;
            document.getElementById('formArea').reset();
            document.querySelector('.form-title').textContent = '➕ Crear Nueva Área';
            document.querySelector('button[type="submit"]').textContent = '➕ Crear Área';
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
