<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Valores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>💰 Gestión de Valores</h1>
                <p>Administra tarifas y precios</p>
            </div>
            <a href="../" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <div id="alertas"></div>

        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title form-title">➕ Crear Nuevo Valor</h5>
                        <form id="formValor">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="refrigerio_id" class="form-label">Refrigerio</label>
                                    <select class="form-control" id="refrigerio_id" required>
                                        <option value="">Selecciona un refrigerio</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor</label>
                                    <select class="form-control" id="proveedor_id" required>
                                        <option value="">Selecciona un proveedor</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="valor" class="form-label">Valor ($)</label>
                                    <input type="number" class="form-control" id="valor" step="0.01" placeholder="Ej: 8500.00" required>
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary" id="btnCancelar" onclick="cancelarEdicion()" style="display: none;">
                                    ❌ Cancelar
                                </button>
                                <button type="submit" class="btn btn-crear">
                                    ➕ Crear Valor
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">📋 Valores Registrados</h5>
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
            cargarSelects();
            cargar();
            document.getElementById('formValor').addEventListener('submit', (e) => {
                e.preventDefault();
                edicionId ? actualizar() : crear();
            });
        });

        function cargarSelects() {
            fetch('../api/get_valores_data.php')
                .then(r => r.json())
                .then(d => {
                    const refSelect = document.getElementById('refrigerio_id');
                    const provSelect = document.getElementById('proveedor_id');

                    d.refrigerios.forEach(r => {
                        const opt = document.createElement('option');
                        opt.value = r.id;
                        opt.textContent = r.nombre;
                        refSelect.appendChild(opt);
                    });

                    d.proveedores.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.nombre;
                        provSelect.appendChild(opt);
                    });
                });
        }

        function crear() {
            const refrigerio_id = document.getElementById('refrigerio_id').value;
            const proveedor_id = document.getElementById('proveedor_id').value;
            const valor = document.getElementById('valor').value;

            if (!refrigerio_id || !proveedor_id || !valor) {
                mostrarAlerta('Completa todos los campos', 'warning');
                return;
            }

            const fd = new FormData();
            fd.append('refrigerio_id', refrigerio_id);
            fd.append('proveedor_id', proveedor_id);
            fd.append('valor', valor);

            fetch('../api/valores_crear.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Valor creado exitosamente', 'success');
                        document.getElementById('formValor').reset();
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function cargar() {
            fetch('../api/valores_leer.php')
                .then(r => r.json())
                .then(d => {
                    const lista = document.getElementById('lista');
                    if (d.success && d.data.length > 0) {
                        lista.innerHTML = d.data.map(item => `
                            <div class="task-item">
                                <div class="task-title">${escapeHtml(item.refrigerio)}</div>
                                <div class="task-description">
                                    🏢 Proveedor: ${escapeHtml(item.proveedor)}<br>
                                    💰 Valor: $${parseFloat(item.valor).toFixed(2)}
                                </div>
                                <div class="task-actions">
                                    <button class="btn btn-editar" onclick="prepararEdicion(${item.id}, ${item.refrigerio_id}, ${item.proveedor_id}, ${item.valor})">✏️ Editar</button>
                                    <button class="btn btn-eliminar" onclick="confirmarEliminar(${item.id}, '${escapeAttr(item.refrigerio)}')">🗑️ Eliminar</button>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        lista.innerHTML = '<div class="empty-state"><p class="no-tasks">No hay valores registrados</p></div>';
                    }
                });
        }

        function prepararEdicion(id, ref_id, prov_id, valor) {
            edicionId = id;
            document.getElementById('refrigerio_id').value = ref_id;
            document.getElementById('proveedor_id').value = prov_id;
            document.getElementById('valor').value = valor;
            document.querySelector('.form-title').textContent = '✏️ Editar Valor';
            document.querySelector('button[type="submit"]').textContent = '✏️ Actualizar Valor';
            document.getElementById('btnCancelar').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function actualizar() {
            const refrigerio_id = document.getElementById('refrigerio_id').value;
            const proveedor_id = document.getElementById('proveedor_id').value;
            const valor = document.getElementById('valor').value;

            if (!refrigerio_id || !proveedor_id || !valor) {
                mostrarAlerta('Completa todos los campos', 'warning');
                return;
            }

            const fd = new FormData();
            fd.append('id', edicionId);
            fd.append('refrigerio_id', refrigerio_id);
            fd.append('proveedor_id', proveedor_id);
            fd.append('valor', valor);

            fetch('../api/valores_actualizar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Valor actualizado exitosamente', 'success');
                        cancelarEdicion();
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function confirmarEliminar(id, nombre) {
            if (confirm(`¿Eliminar este valor?`)) {
                eliminar(id);
            }
        }

        function eliminar(id) {
            const fd = new FormData();
            fd.append('id', id);

            fetch('../api/valores_eliminar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Valor eliminado exitosamente', 'success');
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function cancelarEdicion() {
            edicionId = null;
            document.getElementById('formValor').reset();
            document.querySelector('.form-title').textContent = '➕ Crear Nuevo Valor';
            document.querySelector('button[type="submit"]').textContent = '➕ Crear Valor';
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
