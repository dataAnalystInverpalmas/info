<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Secciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>🏗️ Gestión de Secciones</h1>
                <p>Administra las secciones por área</p>
            </div>
            <a href="../" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <div id="alertas"></div>

        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title form-title">➕ Crear Nueva Sección</h5>
                        <form id="formSeccion">
                            <div class="mb-3">
                                <label for="id_area" class="form-label">Área</label>
                                <select class="form-control" id="id_area" required>
                                    <option value="">Selecciona un área</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre de Sección</label>
                                <input type="text" class="form-control" id="nombre" placeholder="Ej: Cosecha" required>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary" id="btnCancelar" onclick="cancelarEdicion()" style="display: none;">
                                    ❌ Cancelar
                                </button>
                                <button type="submit" class="btn btn-crear">
                                    ➕ Crear Sección
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">📋 Secciones Registradas</h5>
                        <div id="lista"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let edicionId = null;
        let areaIdEdicion = null;

        document.addEventListener('DOMContentLoaded', () => {
            cargarAreas();
            cargar();
            document.getElementById('formSeccion').addEventListener('submit', (e) => {
                e.preventDefault();
                edicionId ? actualizar() : crear();
            });
        });

        function cargarAreas() {
            fetch('../api/get_areas.php')
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        const select = document.getElementById('id_area');
                        d.data.forEach(area => {
                            const option = document.createElement('option');
                            option.value = area.id;
                            option.textContent = area.nombre;
                            select.appendChild(option);
                        });
                    }
                });
        }

        function crear() {
            const id_area = document.getElementById('id_area').value;
            const nombre = document.getElementById('nombre').value.trim();

            if (!id_area || !nombre) {
                mostrarAlerta('Completa todos los campos', 'warning');
                return;
            }

            const fd = new FormData();
            fd.append('id_area', id_area);
            fd.append('nombre', nombre);

            fetch('../api/secciones_crear.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Sección creada exitosamente', 'success');
                        document.getElementById('formSeccion').reset();
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function cargar() {
            fetch('../api/secciones_leer.php')
                .then(r => r.json())
                .then(d => {
                    const lista = document.getElementById('lista');
                    if (d.success && d.data.length > 0) {
                        lista.innerHTML = d.data.map(item => `
                            <div class="task-item">
                                <div class="task-title">${escapeHtml(item.nombre)}</div>
                                <div class="task-description">📍 Área: ${escapeHtml(item.area_nombre)}</div>
                                <div class="task-date">📅 ${new Date(item.fecha_creacion).toLocaleDateString('es-ES')}</div>
                                <div class="task-actions">
                                    <button class="btn btn-editar" onclick="prepararEdicion(${item.id}, ${item.id_area}, '${escapeAttr(item.nombre)}')">✏️ Editar</button>
                                    <button class="btn btn-eliminar" onclick="confirmarEliminar(${item.id}, '${escapeAttr(item.nombre)}')">🗑️ Eliminar</button>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        lista.innerHTML = '<div class="empty-state"><p class="no-tasks">No hay secciones registradas</p></div>';
                    }
                });
        }

        function prepararEdicion(id, id_area, nombre) {
            edicionId = id;
            areaIdEdicion = id_area;
            document.getElementById('id_area').value = id_area;
            document.getElementById('nombre').value = nombre;
            document.querySelector('.form-title').textContent = '✏️ Editar Sección';
            document.querySelector('button[type="submit"]').textContent = '✏️ Actualizar Sección';
            document.getElementById('btnCancelar').style.display = 'block';
            document.getElementById('nombre').focus();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function actualizar() {
            const id_area = document.getElementById('id_area').value;
            const nombre = document.getElementById('nombre').value.trim();

            if (!id_area || !nombre) {
                mostrarAlerta('Completa todos los campos', 'warning');
                return;
            }

            const fd = new FormData();
            fd.append('id', edicionId);
            fd.append('id_area', id_area);
            fd.append('nombre', nombre);

            fetch('../api/secciones_actualizar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Sección actualizada exitosamente', 'success');
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

            fetch('../api/secciones_eliminar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Sección eliminada exitosamente', 'success');
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function cancelarEdicion() {
            edicionId = null;
            areaIdEdicion = null;
            document.getElementById('formSeccion').reset();
            document.querySelector('.form-title').textContent = '➕ Crear Nueva Sección';
            document.querySelector('button[type="submit"]').textContent = '➕ Crear Sección';
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
