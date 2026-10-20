<?php
\App\Helpers\Auth::requireAdmin();
?>
<div class="container-fluid py-4" style="max-width: 1400px;">
    <!-- Encabezado de Página -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <div class="d-flex align-items-center mb-1">
                <span class="material-icons text-primary mr-2" style="font-size: 2rem; color: #00796B !important;">manage_history</span>
                <h2 class="h4 font-weight-bold text-dark mb-0">Bitácora de Accesos y Auditoría</h2>
            </div>
            <p class="text-muted small mb-0">Monitoreo en tiempo real de inicios de sesión, intentos fallidos y bloqueos de seguridad.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex align-items-center">
            <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center mr-2" id="btnRefreshLogs">
                <span class="material-icons mr-1" style="font-size: 1.1rem;">refresh</span> Actualizar
            </button>
            <span class="badge badge-pill badge-light border text-muted px-3 py-2" style="font-size: 0.75rem;">
                <span class="material-icons align-middle text-success mr-1" style="font-size: 0.9rem;">check_circle</span>
                Auditoría Activa
            </span>
        </div>
    </div>

    <!-- Tarjetas KPI Superiores -->
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #00796B !important; border-radius: 12px;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background-color: #E0F2F1;">
                        <span class="material-icons" style="font-size: 1.7rem; color: #00796B;">login</span>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="letter-spacing: 0.5px;">Inicios Hoy</div>
                        <div class="h3 font-weight-bold text-dark mb-0" id="kpiTotalHoy">--</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10B981 !important; border-radius: 12px;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background-color: #D1FAE5;">
                        <span class="material-icons text-success" style="font-size: 1.7rem;">verified_user</span>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="letter-spacing: 0.5px;">Exitosos Hoy</div>
                        <div class="h3 font-weight-bold text-success mb-0" id="kpiExitososHoy">--</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #EF4444 !important; border-radius: 12px;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background-color: #FEE2E2;">
                        <span class="material-icons text-danger" style="font-size: 1.7rem;">gpp_bad</span>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="letter-spacing: 0.5px;">Fallidos Hoy</div>
                        <div class="h3 font-weight-bold text-danger mb-0" id="kpiFallidosHoy">--</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3 mb-3 mb-xl-0">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #F59E0B !important; border-radius: 12px;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 50px; height: 50px; background-color: #FEF3C7;">
                        <span class="material-icons" style="font-size: 1.7rem; color: #D97706;">no_accounts</span>
                    </div>
                    <div>
                        <div class="text-muted small font-weight-bold text-uppercase" style="letter-spacing: 0.5px;">Bloqueados Hoy</div>
                        <div class="h3 font-weight-bold mb-0" style="color: #D97706;" id="kpiBloqueadosHoy">--</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form id="filterLogsForm" class="row align-items-end">
                <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold mb-1">Fecha Desde</label>
                    <input type="date" class="form-control form-control-sm" id="filtroDesde" value="<?php echo date('Y-m-d', strtotime('-7 days')); ?>">
                </div>
                <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold mb-1">Fecha Hasta</label>
                    <input type="date" class="form-control form-control-sm" id="filtroHasta" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-12 col-sm-6 col-md-3 mb-2 mb-md-0">
                    <label class="small text-muted font-weight-bold mb-1">Estado</label>
                    <select class="form-control form-control-sm" id="filtroStatus">
                        <option value="">-- Todos los estados --</option>
                        <option value="EXITOSO">Exitoso</option>
                        <option value="FALLIDO">Fallido</option>
                        <option value="BLOQUEADO">Bloqueado</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3 d-flex align-items-center">
                    <button type="submit" class="btn btn-sm btn-success flex-fill mr-2 d-flex align-items-center justify-content-center" style="background-color: #00796B; border-color: #00796B;">
                        <span class="material-icons mr-1" style="font-size: 1rem;">filter_alt</span> Filtrar
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnResetFilters" title="Restablecer filtros">
                        <span class="material-icons" style="font-size: 1rem;">restart_alt</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Registros -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 w-100" id="tablaLoginLogs">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 140px;">Fecha y Hora</th>
                            <th>Usuario / Login</th>
                            <th style="width: 130px;">Dirección IP</th>
                            <th style="width: 110px;" class="text-center">Estado</th>
                            <th>Detalles</th>
                            <th style="width: 220px;">Dispositivo / Navegador</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Carga dinámica vía AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var dt = $('#tablaLoginLogs').DataTable({
        pageLength: 25,
        order: [[0, 'desc']],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json",
            emptyTable: "No se encontraron registros de accesos con los filtros seleccionados."
        },
        columns: [
            { data: 'created_at', render: function(d) {
                if (!d) return '';
                return '<span class="text-dark font-weight-bold" style="font-size: 0.82rem;">' + d + '</span>';
            }},
            { data: 'username', render: function(d, t, r) {
                var icon = r.status === 'EXITOSO' ? 'account_circle' : 'person_outline';
                return '<span class="d-flex align-items-center font-weight-500">' +
                       '<span class="material-icons mr-1 text-muted" style="font-size: 1.1rem;">' + icon + '</span>' +
                       '<span>' + (d || '<em class="text-muted">Desconocido</em>') + '</span>' +
                       '</span>';
            }},
            { data: 'ip_address', render: function(d) {
                return '<code class="text-dark font-weight-normal px-2 py-1 bg-light border rounded" style="font-size: 0.78rem;">' + d + '</code>';
            }},
            { data: 'status', className: 'text-center', render: function(d) {
                if (d === 'EXITOSO') {
                    return '<span class="badge badge-success px-2 py-1" style="background-color: #10B981; font-weight: 500; font-size: 0.75rem;">EXITOSO</span>';
                } else if (d === 'FALLIDO') {
                    return '<span class="badge badge-danger px-2 py-1" style="background-color: #EF4444; font-weight: 500; font-size: 0.75rem;">FALLIDO</span>';
                } else {
                    return '<span class="badge badge-warning text-dark px-2 py-1" style="background-color: #F59E0B; font-weight: 500; font-size: 0.75rem;">BLOQUEADO</span>';
                }
            }},
            { data: 'details', render: function(d) {
                return '<span class="text-secondary small">' + (d || '-') + '</span>';
            }},
            { data: 'user_agent', render: function(d) {
                if (!d) return '<span class="text-muted small">-</span>';
                var clean = d;
                if (clean.length > 55) {
                    clean = clean.substring(0, 52) + '...';
                }
                return '<span class="text-muted small" title="' + d + '" style="font-size: 0.75rem;">' + clean + '</span>';
            }}
        ]
    });

    function cargarLogs() {
        var params = {
            desde: $('#filtroDesde').val(),
            hasta: $('#filtroHasta').val(),
            status: $('#filtroStatus').val()
        };

        $.getJSON('ajax/login_logs_list.php', params, function(resp) {
            if (resp && resp.stats) {
                $('#kpiTotalHoy').text(resp.stats.total_hoy);
                $('#kpiExitososHoy').text(resp.stats.exitosos_hoy);
                $('#kpiFallidosHoy').text(resp.stats.fallidos_hoy);
                $('#kpiBloqueadosHoy').text(resp.stats.bloqueados_hoy);
            }
            dt.clear();
            if (resp && resp.data) {
                dt.rows.add(resp.data);
            }
            dt.draw();
        }).fail(function() {
            console.error('Error al cargar la bitácora de inicios de sesión.');
        });
    }

    // Inicializar
    cargarLogs();

    $('#filterLogsForm').on('submit', function(e) {
        e.preventDefault();
        cargarLogs();
    });

    $('#btnRefreshLogs').on('click', function() {
        cargarLogs();
    });

    $('#btnResetFilters').on('click', function() {
        $('#filtroDesde').val('<?php echo date("Y-m-d", strtotime("-7 days")); ?>');
        $('#filtroHasta').val('<?php echo date("Y-m-d"); ?>');
        $('#filtroStatus').val('');
        cargarLogs();
    });
});
</script>
