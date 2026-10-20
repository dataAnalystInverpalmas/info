<?php
/**
 * Vista CRUD para Tika Calendario (semanas YYWW, año, modelo_tika_id)
 * Paleta corporativa Inverpalmas: Verde #00796B / #004D40
 */
?>
<link rel="stylesheet" href="scripts/crud_tables.css?v=2">
<style>
.tika-cal-hero {
    background: linear-gradient(135deg, #00796B 0%, #004D40 100%);
    color: #ffffff;
    border-radius: 14px;
    padding: 1.5rem 1.75rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 121, 107, 0.15);
}
.tika-cal-hero h2 {
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
    letter-spacing: -0.5px;
}
.tika-cal-hero p {
    color: rgba(255, 255, 255, 0.85);
    font-size: 0.875rem;
    margin-bottom: 0;
}
.kpi-pill {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 10px;
    padding: 0.6rem 1rem;
    color: #ffffff;
    min-width: 140px;
}
.kpi-pill .kpi-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.85;
    font-weight: 600;
    display: block;
}
.kpi-pill .kpi-val {
    font-size: 1.35rem;
    font-weight: 700;
    line-height: 1.2;
}
.badge-semana {
    font-family: monospace;
    font-size: 0.88rem;
    font-weight: 700;
    padding: 0.35rem 0.65rem;
    border-radius: 6px;
    background-color: #E0F2F1;
    color: #00796B;
    border: 1px solid #B2DFDB;
}
.badge-modelo {
    font-weight: 600;
    font-size: 0.82rem;
    padding: 0.35rem 0.65rem;
    border-radius: 6px;
    background-color: #E8F5E9;
    color: #2E7D32;
    border: 1px solid #C8E6C9;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.badge-sin-modelo {
    font-weight: 500;
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
    border-radius: 6px;
    background-color: #FFF3E0;
    color: #E65100;
    border: 1px solid #FFE0B2;
}
.table-hover tbody tr:hover {
    background-color: #F8FAFC !important;
}
.action-btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    font-size: 0.85rem;
    transition: all 0.15s ease;
}
.filter-card {
    border-radius: 12px;
    border: 1px solid #E2E8F0;
    background: #ffffff;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
</style>

<div class="container-fluid py-3 px-3 px-md-4">
    <!-- Hero Banner con KPIs -->
    <div class="tika-cal-hero d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center mb-1">
                <span class="material-icons mr-2" style="font-size: 2rem;">calendar_month</span>
                <h2 class="mb-0">Calendario Tika — Semanas y Modelos</h2>
            </div>
            <p>Gestión de semanas YYWW (ej. 2401 a 2952, 3001 a 3053), asignación de modelos Tika y generador de nuevos años en lote.</p>
        </div>

        <div class="d-flex flex-wrap gap-2 mt-3 mt-lg-0" id="kpisContainer">
            <div class="kpi-pill">
                <span class="kpi-label">Total Semanas</span>
                <div class="kpi-val" id="kpiTotalSemanas"><?php echo number_format((int)$kpis['total_semanas']); ?></div>
            </div>
            <div class="kpi-pill">
                <span class="kpi-label">Años Cubiertos</span>
                <div class="kpi-val" id="kpiTotalAnios"><?php echo (int)$kpis['total_anios']; ?></div>
            </div>
            <div class="kpi-pill">
                <span class="kpi-label">Con Modelo</span>
                <div class="kpi-val text-success" id="kpiConModelo" style="color: #A7F3D0 !important;"><?php echo number_format((int)$kpis['con_modelo']); ?></div>
            </div>
            <div class="kpi-pill">
                <span class="kpi-label">Sin Modelo</span>
                <div class="kpi-val text-warning" id="kpiSinModelo" style="color: #FDE68A !important;"><?php echo number_format((int)$kpis['sin_modelo']); ?></div>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros y Acciones -->
    <div class="filter-card">
        <div class="row align-items-center">
            <!-- Filtro Año -->
            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <label class="small font-weight-bold text-muted mb-1">Filtrar por Año</label>
                <select id="filtroAnio" class="form-control form-control-sm">
                    <option value="">Todos los años</option>
                    <?php foreach ($anios as $a): ?>
                        <option value="<?php echo $a; ?>"><?php echo $a; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Filtro Estado Modelo -->
            <div class="col-md-3 col-sm-6 mb-2 mb-md-0">
                <label class="small font-weight-bold text-muted mb-1">Estado de Asignación</label>
                <select id="filtroEstadoModelo" class="form-control form-control-sm">
                    <option value="">Todos los registros</option>
                    <option value="not_null">Con Modelo Asignado</option>
                    <option value="null">Sin Modelo Asignado</option>
                    <?php foreach ($modelos as $m): ?>
                        <option value="<?php echo $m; ?>">Modelo #<?php echo $m; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Acciones Principales -->
            <div class="col-md-6 text-md-right mt-2 mt-md-0 d-flex flex-wrap justify-content-md-end align-items-end" style="gap: 0.5rem;">
                <button type="button" class="btn btn-outline-info btn-sm d-inline-flex align-items-center" id="btnSyncDates" title="Importar semanas faltantes desde la tabla dates (2401 a 2952)">
                    <span class="material-icons mr-1" style="font-size: 1rem;">sync</span> Sincronizar dates
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center" id="btnAsignarAnio">
                    <span class="material-icons mr-1" style="font-size: 1rem;">tune</span> Asignar Modelo x Año
                </button>
                <button type="button" class="btn btn-primary btn-sm d-inline-flex align-items-center" id="btnNuevoAnio" style="background-color: #00796B; border-color: #00796B;">
                    <span class="material-icons mr-1" style="font-size: 1.1rem;">add_circle</span> Crear / Generar Año
                </button>
                <button type="button" class="btn btn-outline-success btn-sm d-inline-flex align-items-center" id="btnNuevaSemana">
                    <span class="material-icons mr-1" style="font-size: 1rem;">add</span> Nueva Semana
                </button>
            </div>
        </div>
    </div>

    <!-- Tabla Principal -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="tablaTikaCalendario">
                    <thead style="background-color: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                        <tr>
                            <th style="width: 120px;">Semana YYWW</th>
                            <th style="width: 90px;">Año</th>
                            <th style="width: 90px;"># Semana</th>
                            <th>Rango de Fechas (Lunes - Domingo)</th>
                            <th style="width: 170px;">Modelo Tika ID</th>
                            <th>Observaciones</th>
                            <th class="text-center" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables dynamic content -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: CREAR / GENERAR AÑO COMPLETO (ej. 2030)            -->
<!-- ======================================================== -->
<div class="modal fade" id="modalGenerarAnio" tabindex="-1" role="dialog" aria-labelledby="modalGenerarAnioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header text-white" style="background-color: #00796B; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title font-weight-bold d-flex align-items-center" id="modalGenerarAnioLabel">
                    <span class="material-icons mr-2">date_range</span> Generar Semanas para un Año
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formGenerarAnio">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Genera automáticamente todas las semanas YYWW del año indicado con sus fechas de inicio (lunes) y fin (domingo) calculadas según el estándar ISO-8601.
                    </p>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark mb-1">Año Calendario</label>
                        <input type="number" class="form-control" id="genAnio" name="anio" placeholder="Ej. 2030" min="2020" max="2050" required value="<?php echo date('Y') + 1; ?>">
                        <small class="form-text text-muted" id="genAnioHelp">Prefijo YY resultante: <strong>--</strong></small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark mb-1">Cantidad de Semanas a Generar</label>
                        <div class="d-flex" style="gap: 1.5rem;">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="rad52" name="semanas" class="custom-control-input" value="52" checked>
                                <label class="custom-control-label" for="rad52"><strong>52 Semanas</strong> <span class="text-muted small" id="lbl52Range">(ej. 3001 a 3052)</span></label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="rad53" name="semanas" class="custom-control-input" value="53">
                                <label class="custom-control-label" for="rad53"><strong>53 Semanas</strong> <span class="text-muted small" id="lbl53Range">(ej. 3001 a 3053)</span></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark mb-1">Modelo Tika Inicial (Opcional)</label>
                        <input type="number" class="form-control" id="genModeloId" name="modelo_tika_id" placeholder="Ej. 1 (o dejar vacío)">
                        <small class="form-text text-muted">Se asignará este modelo a todas las semanas generadas.</small>
                    </div>

                    <div class="custom-control custom-checkbox mt-3">
                        <input type="checkbox" class="custom-control-input" id="genSobrescribir" name="sobrescribir" value="1">
                        <label class="custom-control-label small text-muted" for="genSobrescribir">Sobrescribir modelo en semanas que ya existan para este año</label>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 font-weight-bold" id="btnSubmitGenerarAnio" style="background-color: #00796B; border-color: #00796B;">
                        <span class="material-icons mr-1 align-middle" style="font-size: 1.1rem;">save</span> Generar Semanas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: EDITAR / CREAR SEMANA INDIVIDUAL                   -->
<!-- ======================================================== -->
<div class="modal fade" id="modalSemana" tabindex="-1" role="dialog" aria-labelledby="modalSemanaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header text-white" style="background-color: #00796B; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title font-weight-bold d-flex align-items-center" id="modalSemanaLabel">
                    <span class="material-icons mr-2">edit_calendar</span> <span id="modalSemanaTitulo">Editar Semana</span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSemana">
                <input type="hidden" id="semId" name="id" value="">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-6 form-group mb-3">
                            <label class="font-weight-bold small text-dark mb-1">Semana YYWW *</label>
                            <input type="number" class="form-control" id="semSemana" name="semana" placeholder="Ej. 2601" required min="1001" max="9953">
                        </div>
                        <div class="col-6 form-group mb-3">
                            <label class="font-weight-bold small text-dark mb-1">Año</label>
                            <input type="number" class="form-control" id="semAnio" name="anio" placeholder="Ej. 2026" min="2000" max="2100">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark mb-1">Modelo Tika ID</label>
                        <input type="number" class="form-control" id="semModeloId" name="modelo_tika_id" placeholder="Ej. 1 (dejar vacío si no tiene modelo asignado)">
                        <small class="form-text text-muted">ID numérico del modelo de demanda Tika.</small>
                    </div>

                    <div class="row">
                        <div class="col-6 form-group mb-3">
                            <label class="font-weight-bold small text-dark mb-1">Fecha Inicio (Lunes)</label>
                            <input type="date" class="form-control" id="semFechaInicio" name="fecha_inicio">
                        </div>
                        <div class="col-6 form-group mb-3">
                            <label class="font-weight-bold small text-dark mb-1">Fecha Fin (Domingo)</label>
                            <input type="date" class="form-control" id="semFechaFin" name="fecha_fin">
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="font-weight-bold small text-dark mb-1">Observaciones / Descripción</label>
                        <textarea class="form-control" id="semObservaciones" name="observaciones" rows="2" placeholder="Notas adicionales de la semana o modelo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 font-weight-bold" style="background-color: #00796B; border-color: #00796B;">
                        Guardar Registro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL: ASIGNACIÓN DE MODELO POR AÑO                       -->
<!-- ======================================================== -->
<div class="modal fade" id="modalAsignarAnio" tabindex="-1" role="dialog" aria-labelledby="modalAsignarAnioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header text-white" style="background-color: #00796B; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h5 class="modal-title font-weight-bold d-flex align-items-center" id="modalAsignarAnioLabel">
                    <span class="material-icons mr-2">tune</span> Asignar Modelo a Todo un Año
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAsignarAnio">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">
                        Esta acción actualizará el <strong>modelo_tika_id</strong> de <em>todas</em> las semanas del año seleccionado.
                    </p>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark mb-1">Año a Modificar *</label>
                        <select class="form-control" id="asigAnio" name="anio" required>
                            <option value="">Seleccione un año...</option>
                            <?php foreach ($anios as $a): ?>
                                <option value="<?php echo $a; ?>">Año <?php echo $a; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark mb-1">Nuevo Modelo Tika ID</label>
                        <input type="number" class="form-control" id="asigModeloId" name="modelo_tika_id" placeholder="Ej. 2 (dejar vacío para desasignar / null)">
                        <small class="form-text text-muted">Deje este campo vacío si desea desvincular el modelo de todas las semanas de este año.</small>
                    </div>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-sm px-4 font-weight-bold" style="background-color: #00796B; border-color: #00796B;">
                        Aplicar a Todo el Año
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="scripts/tika_calendario.js?v=<?php echo time(); ?>"></script>
