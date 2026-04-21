<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Consumos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 10px; }
            .table { font-size: 11px; }
            .resumen-table { page-break-inside: avoid; }
        }
        .resumen-table {
            margin-top: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
        }
        .resumen-titulo {
            font-weight: 600;
            margin-top: 15px;
            margin-bottom: 10px;
            color: #333;
        }
        .filtros-seccion {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="header no-print" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1>📊 Registrar Consumos</h1>
                <p>Registra consumos de refrigerios</p>
            </div>
            <a href="../" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
        </div>

        <div id="alertas"></div>

        <!-- Formulario de registro -->
        <div class="row mb-4 no-print">
            <div class="col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title form-title">➕ Crear Nuevo Registro</h5>
                        <form id="formHecho">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha" class="form-label">Fecha</label>
                                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="proveedor_id" class="form-label">Proveedor</label>
                                    <select class="form-control" id="proveedor_id" required>
                                        <option value="">Selecciona un proveedor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="seccion_id" class="form-label">Sección</label>
                                    <select class="form-control" id="seccion_id" required>
                                        <option value="">Selecciona una sección</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="jornada_id" class="form-label">Jornada</label>
                                    <select class="form-control" id="jornada_id" required>
                                        <option value="">Selecciona una jornada</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="refrigerio_id" class="form-label">Refrigerio/Comida</label>
                                    <select class="form-control" id="refrigerio_id" required>
                                        <option value="">Selecciona un refrigerio</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="cantidad" class="form-label">Cantidad</label>
                                    <input type="number" class="form-control" id="cantidad" placeholder="Ej: 10" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="valor_unitario" class="form-label">Valor Unitario ($)</label>
                                    <input type="number" class="form-control" id="valor_unitario" step="0.01" placeholder="Ej: 8500.00" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="valor_total" class="form-label">Valor Total ($)</label>
                                    <input type="number" class="form-control" id="valor_total" step="0.01" readonly>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="cuenta_cobro" class="form-label">Cuenta de Cobro</label>
                                    <input type="text" class="form-control" id="cuenta_cobro" placeholder="Ej: CC-001">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <input type="text" class="form-control" id="observaciones" placeholder="Notas">
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="button" class="btn btn-secondary" id="btnCancelar" onclick="cancelarEdicion()" style="display: none;">
                                    ❌ Cancelar
                                </button>
                                <button type="submit" class="btn btn-crear">
                                    ➕ Registrar Consumo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de búsqueda -->
        <div class="row mb-4 no-print">
            <div class="col-lg-10 mx-auto">
                <div class="filtros-seccion">
                    <h6 class="mb-3">🔍 Filtrar Registros</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="filtro_año" class="form-label">Año</label>
                            <select class="form-control" id="filtro_año">
                                <option value="">Todos los años</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="filtro_mes" class="form-label">Mes</label>
                            <select class="form-control" id="filtro_mes">
                                <option value="">Todos los meses</option>
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="filtro_quincena" class="form-label">Quincena</label>
                            <select class="form-control" id="filtro_quincena">
                                <option value="">Ambas quincenas</option>
                                <option value="1">Primera (1-15)</option>
                                <option value="2">Segunda (16-31)</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" onclick="filtrarRegistros()">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-info btn-sm" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="descargarExcel()">
                                <i class="fas fa-file-excel"></i> Descargar Excel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de registros -->
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" id="titulo_detallado">📋 Registros Detallados</h5>
                        <div id="lista"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resúmenes -->
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="resumen-table">
                    <h6 class="resumen-titulo">💰 Resumen por Proveedor - Cuánto a Pagar</h6>
                    <div id="resumen_proveedor"></div>
                </div>
            </div>
            <div class="col-lg-6 mx-auto">
                <div class="resumen-table">
                    <h6 class="resumen-titulo">📊 Resumen por Área - Valor Gastado</h6>
                    <div id="resumen_area"></div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="resumen-table">
                    <h6 class="resumen-titulo">💵 Total General: $<span id="total_general">0.00</span></h6>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let edicionId = null;
        let registrosActuales = [];

        document.addEventListener('DOMContentLoaded', () => {
            cargarSelects();
            cargar();
            document.getElementById('formHecho').addEventListener('submit', (e) => {
                e.preventDefault();
                edicionId ? actualizar() : crear();
            });

            document.getElementById('cantidad').addEventListener('input', calcularTotal);
            document.getElementById('valor_unitario').addEventListener('input', calcularTotal);

            // Cargar años disponibles
            cargarAños();

            // Filtro por mes/quincena
            document.getElementById('filtro_mes').addEventListener('change', () => {
                if (document.getElementById('filtro_año').value && document.getElementById('filtro_mes').value) {
                    filtrarRegistros();
                }
                actualizarTituloDetallado();
            });

            document.getElementById('filtro_quincena').addEventListener('change', () => {
                if (document.getElementById('filtro_año').value && document.getElementById('filtro_mes').value) {
                    filtrarRegistros();
                }
                actualizarTituloDetallado();
            });

            document.getElementById('filtro_año').addEventListener('change', () => {
                actualizarTituloDetallado();
            });

            actualizarTituloDetallado();
        });
        function actualizarTituloDetallado() {
            const año = document.getElementById('filtro_año').value;
            const mes = document.getElementById('filtro_mes').value;
            const quincena = document.getElementById('filtro_quincena').value;
            let mesTexto = '';
            switch (mes) {
                case '1': mesTexto = 'Enero'; break;
                case '2': mesTexto = 'Febrero'; break;
                case '3': mesTexto = 'Marzo'; break;
                case '4': mesTexto = 'Abril'; break;
                case '5': mesTexto = 'Mayo'; break;
                case '6': mesTexto = 'Junio'; break;
                case '7': mesTexto = 'Julio'; break;
                case '8': mesTexto = 'Agosto'; break;
                case '9': mesTexto = 'Septiembre'; break;
                case '10': mesTexto = 'Octubre'; break;
                case '11': mesTexto = 'Noviembre'; break;
                case '12': mesTexto = 'Diciembre'; break;
                default: mesTexto = '';
            }
            let quincenaTexto = '';
            if (quincena === '1') quincenaTexto = 'Primera';
            else if (quincena === '2') quincenaTexto = 'Segunda';
            else quincenaTexto = '';
            let titulo = '📋 Registros Detallados';
            if (año || mesTexto || quincenaTexto) {
                titulo += ' - ';
                if (año) titulo += `Año ${año} `;
                if (mesTexto) titulo += `Mes ${mesTexto} `;
                if (quincenaTexto) titulo += `Quincena ${quincenaTexto}`;
            }
            document.getElementById('titulo_detallado').textContent = titulo.trim();
        }

        function cargarAños() {
            fetch('../api/hechos_leer.php')
                .then(r => r.json())
                .then(d => {
                    if (d.success && d.data.length > 0) {
                        const años = [...new Set(d.data.map(h => h.año))].sort().reverse();
                        const selectAño = document.getElementById('filtro_año');
                        años.forEach(año => {
                            const opt = document.createElement('option');
                            opt.value = año;
                            opt.textContent = año;
                            selectAño.appendChild(opt);
                        });
                        if (años.length > 0) {
                            document.getElementById('filtro_año').value = años[0];
                            document.getElementById('filtro_mes').value = new Date().getMonth() + 1;
                            setTimeout(filtrarRegistros, 500);
                        }
                    }
                });
        }

        function cargarSelects() {
            fetch('../api/get_hechos_data.php')
                .then(r => r.json())
                .then(d => {
                    const provSelect = document.getElementById('proveedor_id');
                    const seccSelect = document.getElementById('seccion_id');
                    const refSelect = document.getElementById('refrigerio_id');
                    const jorSelect = document.getElementById('jornada_id');

                    d.proveedores.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.textContent = p.nombre;
                        provSelect.appendChild(opt);
                    });

                    d.secciones.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.nombre;
                        seccSelect.appendChild(opt);
                    });

                    d.refrigerios.forEach(r => {
                        const opt = document.createElement('option');
                        opt.value = r.id;
                        opt.textContent = r.nombre;
                        refSelect.appendChild(opt);
                    });

                    d.jornadas.forEach(j => {
                        const opt = document.createElement('option');
                        opt.value = j.id;
                        opt.textContent = j.nombre;
                        jorSelect.appendChild(opt);
                    });
                });
        }

        function calcularTotal() {
            const cantidad = parseFloat(document.getElementById('cantidad').value) || 0;
            const unitario = parseFloat(document.getElementById('valor_unitario').value) || 0;
            const total = cantidad * unitario;
            document.getElementById('valor_total').value = total.toFixed(2);
        }

        function crear() {
            const fecha = document.getElementById('fecha').value;
            const proveedor_id = document.getElementById('proveedor_id').value;
            const seccion_id = document.getElementById('seccion_id').value;
            const refrigerio_id = document.getElementById('refrigerio_id').value;
            const jornada_id = document.getElementById('jornada_id').value;
            const cantidad = document.getElementById('cantidad').value;
            const valor_unitario = document.getElementById('valor_unitario').value;
            const cuenta_cobro = document.getElementById('cuenta_cobro').value;
            const observaciones = document.getElementById('observaciones').value;

            if (!fecha || !proveedor_id || !seccion_id || !refrigerio_id || !jornada_id || !cantidad) {
                mostrarAlerta('Completa todos los campos requeridos', 'warning');
                return;
            }

            const fd = new FormData();
            fd.append('fecha', fecha);
            fd.append('proveedor_id', proveedor_id);
            fd.append('seccion_id', seccion_id);
            fd.append('refrigerio_id', refrigerio_id);
            fd.append('jornada_id', jornada_id);
            fd.append('cantidad', cantidad);
            fd.append('valor_unitario', valor_unitario);
            fd.append('cuenta_cobro', cuenta_cobro);
            fd.append('observaciones', observaciones);

            fetch('../api/hechos_crear.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Registro creado exitosamente', 'success');
                        document.getElementById('formHecho').reset();
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function filtrarRegistros() {
            const año = document.getElementById('filtro_año').value;
            const mes = document.getElementById('filtro_mes').value;
            const quincena = document.getElementById('filtro_quincena').value;

            const fd = new FormData();
            fd.append('año', año);
            fd.append('mes', mes);
            if (quincena) fd.append('quincena', quincena);

            fetch('../api/hechos_filtrar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => mostrarTabla(d));
        }

        function cargar() {
            fetch('../api/hechos_leer.php')
                .then(r => r.json())
                .then(d => {
                    if (d.success && d.data.length > 0) {
                        registrosActuales = d.data;
                        mostrarTabla(d);
                    }
                });
        }

        function mostrarTabla(data) {
            if (!data.success || !data.data || data.data.length === 0) {
                document.getElementById('lista').innerHTML = '<div class="empty-state"><p class="no-tasks">No hay registros</p></div>';
                document.getElementById('resumen_proveedor').innerHTML = '';
                document.getElementById('resumen_area').innerHTML = '';
                document.getElementById('total_general').textContent = '0.00';
                return;
            }

            const tabla = `
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Proveedor</th>
                                <th>Sección</th>
                                <th>Refrigerio</th>
                                <th>Jornada</th>
                                <th>Cantidad</th>
                                <th>Valor Unit.</th>
                                <th>Valor Total</th>
                                <th>Cuenta Cobro</th>
                                <th class="no-print">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.data.map(h => `
                                <tr>
                                    <td>${h.fecha}</td>
                                    <td>${escapeHtml(h.proveedor)}</td>
                                    <td>${escapeHtml(h.seccion)}</td>
                                    <td>${escapeHtml(h.refrigerio)}</td>
                                    <td>${escapeHtml(h.jornada)}</td>
                                    <td class="text-center">${h.cantidad}</td>
                                    <td class="text-right">$${parseFloat(h.valor_unitario).toFixed(2)}</td>
                                    <td class="text-right font-weight-bold">$${parseFloat(h.valor_total).toFixed(2)}</td>
                                    <td>${escapeHtml(h.cuenta_cobro || '-')}</td>
                                    <td class="no-print">
                                        <button class="btn btn-sm btn-editar" onclick="prepararEdicion(${h.id}, '${h.fecha}', ${h.proveedor_id}, ${h.seccion_id}, ${h.refrigerio_id}, ${h.jornada_id}, ${h.cantidad}, ${h.valor_unitario}, '${escapeAttr(h.cuenta_cobro || '')}')">✏️</button>
                                        <button class="btn btn-sm btn-eliminar" onclick="confirmarEliminar(${h.id})">🗑️</button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
            document.getElementById('lista').innerHTML = tabla;

            // Resumen proveedor
            const resProvTable = `
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr><th>Proveedor</th><th class="text-right">Total a Pagar</th></tr>
                    </thead>
                    <tbody>
                        ${Object.entries(data.resumen_proveedor).map(([prov, total]) => `
                            <tr>
                                <td>${escapeHtml(prov)}</td>
                                <td class="text-right font-weight-bold">$${parseFloat(total).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            document.getElementById('resumen_proveedor').innerHTML = resProvTable;

            // Resumen área
            const resAreaTable = `
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr><th>Área</th><th class="text-right">Valor Gastado</th></tr>
                    </thead>
                    <tbody>
                        ${Object.entries(data.resumen_area).map(([area, total]) => `
                            <tr>
                                <td>${escapeHtml(area)}</td>
                                <td class="text-right font-weight-bold">$${parseFloat(total).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            document.getElementById('resumen_area').innerHTML = resAreaTable;

            document.getElementById('total_general').textContent = parseFloat(data.total_general).toFixed(2);
        }

        function prepararEdicion(id, fecha_id, prov_id, secc_id, ref_id, jor_id, cant, valor, cuenta) {
            edicionId = id;
            document.getElementById('fecha').value = fecha_id; // fecha_id ahora es la fecha en formato YYYY-MM-DD
            document.getElementById('proveedor_id').value = prov_id;
            document.getElementById('seccion_id').value = secc_id;
            document.getElementById('refrigerio_id').value = ref_id;
            document.getElementById('jornada_id').value = jor_id;
            document.getElementById('cantidad').value = cant;
            document.getElementById('valor_unitario').value = valor;
            document.getElementById('cuenta_cobro').value = cuenta;
            calcularTotal();
            document.querySelector('.form-title').textContent = '✏️ Editar Registro';
            document.querySelector('button[type="submit"]').textContent = '✏️ Actualizar Registro';
            document.getElementById('btnCancelar').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function actualizar() {
            const fecha = document.getElementById('fecha').value;
            const proveedor_id = document.getElementById('proveedor_id').value;
            const seccion_id = document.getElementById('seccion_id').value;
            const refrigerio_id = document.getElementById('refrigerio_id').value;
            const jornada_id = document.getElementById('jornada_id').value;
            const cantidad = document.getElementById('cantidad').value;
            const valor_unitario = document.getElementById('valor_unitario').value;
            const cuenta_cobro = document.getElementById('cuenta_cobro').value;
            const observaciones = document.getElementById('observaciones').value;

            if (!fecha || !proveedor_id || !seccion_id || !refrigerio_id || !jornada_id || !cantidad) {
                mostrarAlerta('Completa todos los campos requeridos', 'warning');
                return;
            }

            const fd = new FormData();
            fd.append('id', edicionId);
            fd.append('fecha', fecha);
            fd.append('proveedor_id', proveedor_id);
            fd.append('seccion_id', seccion_id);
            fd.append('refrigerio_id', refrigerio_id);
            fd.append('jornada_id', jornada_id);
            fd.append('cantidad', cantidad);
            fd.append('valor_unitario', valor_unitario);
            fd.append('cuenta_cobro', cuenta_cobro);
            fd.append('observaciones', observaciones);

            fetch('../api/hechos_actualizar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Registro actualizado exitosamente', 'success');
                        cancelarEdicion();
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function confirmarEliminar(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
                eliminar(id);
            }
        }

        function eliminar(id) {
            const fd = new FormData();
            fd.append('id', id);

            fetch('../api/hechos_eliminar.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(d => {
                    if (d.success) {
                        mostrarAlerta('Registro eliminado exitosamente', 'success');
                        cargar();
                    } else {
                        mostrarAlerta(d.message, 'danger');
                    }
                });
        }

        function cancelarEdicion() {
            edicionId = null;
            document.getElementById('formHecho').reset();
            document.querySelector('.form-title').textContent = '➕ Crear Nuevo Registro';
            document.querySelector('button[type="submit"]').textContent = '➕ Registrar Consumo';
            document.getElementById('btnCancelar').style.display = 'none';
        }

        function descargarExcel() {
            const año = document.getElementById('filtro_año').value;
            const mes = document.getElementById('filtro_mes').value;
            const quincena = document.getElementById('filtro_quincena').value;
            
            let url = `../api/hechos_excel.php?año=${año}&mes=${mes}`;
            if (quincena) url += `&quincena=${quincena}`;
            
            window.location.href = url;
        }

        function mostrarAlerta(msg, tipo) {
            const contenedor = document.getElementById('alertas');
            const alerta = document.createElement('div');
            alerta.className = `alert alert-${tipo} alert-dismissible fade show no-print`;
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
