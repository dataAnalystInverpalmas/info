<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .ce-bar { background:#fff; border-bottom:1px solid #eef2f5; box-shadow:0 2px 10px rgba(0,0,0,.05); margin:-.5rem -15px 1rem; padding:.9rem 15px; }
    .ce-bar h1 { font-size:1.05rem; font-weight:700; margin:0; color:#17312d; }
    .ce-bar p { margin:.15rem 0 0; color:#5f6f6b; font-size:.8rem; }

    .ce-sidebar { position:sticky; top:1rem; }
    .ce-sidebar .card { border:1px solid #dbe7e4; border-radius:14px; }
    .ce-sidebar .card-header { background:linear-gradient(135deg,#00796B 0%,#00695c 100%); color:#fff; border-radius:14px 14px 0 0 !important; font-weight:700; font-size:.85rem; padding:.6rem .9rem; display:flex; align-items:center; justify-content:space-between; cursor:pointer; }
    .ce-sidebar label { font-size:.78rem; font-weight:600; color:#1f2937; letter-spacing:.02em; margin-bottom:.2rem; }
    .ce-sidebar .select2-container { width:100% !important; }
    .ce-sidebar .select2-container .select2-selection--multiple { border:1px solid #ced4da; border-radius:.25rem; min-height:calc(1.5em + .75rem + 2px); }
    .ce-filtros-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.25rem .75rem; }
    .ce-full { grid-column:1 / -1; }

    .ce-card { border:1px solid #e1ebe9; border-radius:18px; background:#fff; box-shadow:0 10px 30px rgba(0,77,64,.05); padding:1rem; min-width:0; margin-bottom:1rem; }
    .ce-card-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; margin-bottom:.75rem; }
    .ce-card-title { font-size:.95rem; font-weight:700; margin:0; color:#17312d; }
    .ce-card-sub { font-size:.75rem; color:#6b7c78; }
    .ce-toolbar { background:#fff; border:1px solid #e1ebe9; border-radius:14px; padding:.6rem .8rem; margin-bottom:1rem; display:flex; flex-wrap:wrap; gap:.75rem 1.25rem; align-items:center; }
    .ce-toolbar .ce-tb-group { display:flex; align-items:center; gap:.4rem; }
    .ce-toolbar .ce-tb-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#6b7c78; }
    .ce-seg .btn.active { background-color:#00796B; border-color:#00796B; color:#fff; }

    .ce-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:.75rem; margin-bottom:1rem; }
    .ce-kpi { background:#fff; border:1px solid #e1ebe9; border-left:5px solid #00796B; border-radius:14px; padding:.7rem .9rem; transition:transform .15s ease, box-shadow .15s ease; }
    .ce-kpi.ce-clickable { cursor:pointer; }
    .ce-kpi.ce-clickable:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(0,77,64,.12); }
    .ce-kpi.ce-off { opacity:.45; }
    .ce-kpi-label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.04em; color:#6b7c78; }
    .ce-kpi-value { font-size:1.35rem; font-weight:800; color:#17312d; line-height:1.2; }
    .ce-kpi-help { font-size:.72rem; color:#6b7c78; }

    .ce-tbl-wrap { max-height:460px; overflow:auto; position:relative; }
    .ce-tbl { border-collapse:separate; border-spacing:0; white-space:nowrap; font-size:.8rem; }
    .ce-tbl thead th { position:sticky; top:0; background:#f8fafc; z-index:3; cursor:pointer; user-select:none; }
    .ce-tbl thead th.ce-sorted::after { content:' \25B2'; font-size:.6rem; }
    .ce-tbl thead th.ce-sorted.ce-desc::after { content:' \25BC'; }
    .ce-tbl td, .ce-tbl th { padding:.3rem .55rem; }
    .ce-tbl .ce-sticky { position:sticky; left:0; background:#f8fafc; z-index:2; min-width:150px; text-align:left; }
    .ce-tbl thead th.ce-sticky { z-index:4; }
    .ce-tbl tfoot td { position:sticky; bottom:0; background:#eef6f4; font-weight:700; }
    .ce-tbl tr.ce-row-click { cursor:pointer; }
    .ce-tbl tr.ce-row-click:hover td { background-color:#eef6f4 !important; }
    .ce-tbl tr.ce-row-hidden td { opacity:.4; text-decoration:line-through; }
    .ce-tbl td.ce-total, .ce-tbl th.ce-total { font-weight:700; background:#eef6f4; }
    .ce-tbl .ce-focus { box-shadow:inset 0 0 0 2px #f97316; }
    .ce-pos { color:#15803d; font-weight:600; }
    .ce-neg { color:#b91c1c; font-weight:600; }
    .ce-dot { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:.4rem; }

    .ce-chip { display:inline-flex; align-items:center; background:#fff3e0; color:#9a3412; border:1px solid #fdba74; border-radius:999px; padding:.15rem .6rem; font-size:.75rem; font-weight:600; cursor:pointer; }
    .ce-chip .material-icons { font-size:.95rem; margin-left:.25rem; }
    .ce-loading { position:relative; }
    .ce-loading::after { content:''; position:absolute; inset:0; background:rgba(255,255,255,.65); border-radius:18px; }

    @media (max-width:767.98px) { .ce-sidebar { position:static; margin-bottom:1rem; } }
    @media (max-width:575.98px) { .ce-filtros-grid { grid-template-columns:minmax(0,1fr); } }
</style>

<div class="ce-bar">
    <h1>Comparar Escenarios</h1>
    <p>Tallos por escenario (fuente) y tipo, cruzados con semana, variedad, flor y color. Filtros, agrupaciones, gráficos y comparaciones se recalculan al instante.</p>
</div>

<div class="container-fluid px-3 px-lg-4 pb-4">
    <div class="row">
        <!-- ===== Filtros ===== -->
        <div class="col-md-4 col-lg-3" id="ceSidebarCol">
            <div class="ce-sidebar mr-0 mr-lg-3">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header" id="ceFiltrosToggle">
                        <span><span class="material-icons" style="font-size:1rem;vertical-align:middle;">filter_list</span> Filtros</span>
                        <span class="material-icons">chevron_left</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="ce-filtros-grid">
                            <div class="mb-2 ce-full"><label for="ceFFuente">Fuente (escenario)</label>
                                <select id="ceFFuente" class="form-control ce-ms" multiple></select></div>
                            <div class="mb-2"><label for="ceFTipo">Tipo</label>
                                <select id="ceFTipo" class="form-control ce-ms" multiple></select></div>
                            <div class="mb-2"><label for="ceFAnio">Año</label>
                                <select id="ceFAnio" class="form-control ce-ms" multiple></select></div>
                            <div class="mb-2"><label for="ceFFlor">Flor</label>
                                <select id="ceFFlor" class="form-control ce-ms" multiple></select></div>
                            <div class="mb-2"><label for="ceFColor">Color</label>
                                <select id="ceFColor" class="form-control ce-ms" multiple></select></div>
                            <div class="mb-2 ce-full"><label for="ceFVariedad">Variedad</label>
                                <select id="ceFVariedad" class="form-control ce-ms" multiple></select></div>
                            <div class="mb-2"><label for="ceSemanaDesde">Semana desde</label>
                                <select id="ceSemanaDesde" class="form-control ce-single">
                                    <?php foreach ($semanas as $s): ?>
                                        <option value="<?php echo htmlspecialchars($s); ?>" <?php echo $s === $semanaDesdeDefault ? 'selected' : ''; ?>><?php echo htmlspecialchars($s); ?></option>
                                    <?php endforeach; ?>
                                </select></div>
                            <div class="mb-2"><label for="ceSemanaHasta">Semana hasta</label>
                                <select id="ceSemanaHasta" class="form-control ce-single">
                                    <?php foreach ($semanas as $s): ?>
                                        <option value="<?php echo htmlspecialchars($s); ?>" <?php echo $s === $semanaHastaDefault ? 'selected' : ''; ?>><?php echo htmlspecialchars($s); ?></option>
                                    <?php endforeach; ?>
                                </select></div>
                            <div class="mb-2 ce-full"><label for="ceGranularidad">Granularidad de carga</label>
                                <select id="ceGranularidad" class="form-control form-control-sm">
                                    <option value="semana" selected>Semana</option>
                                    <option value="dia">Día (más pesado)</option>
                                </select></div>
                        </div>
                        <small class="text-muted d-block mt-1">Fuente, tipo, año, flor, color y variedad filtran en cascada de inmediato. Semana y granularidad recargan los datos.</small>
                    </div>
                </div>
                <button class="btn btn-sm w-100 mb-2" style="background-color:#00796B;border-color:#00796B;color:#fff;" id="ceBtnCargar">
                    <span class="material-icons" style="font-size:1rem;vertical-align:middle;">sync</span> Cargar rango
                </button>
                <button class="btn btn-outline-secondary btn-sm w-100" id="ceBtnLimpiar">
                    <span class="material-icons" style="font-size:1rem;vertical-align:middle;">restart_alt</span> Limpiar filtros
                </button>
            </div>
        </div>

        <!-- ===== Contenido ===== -->
        <div class="col-md-8 col-lg-9" id="ceMainCol">
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="ceBtnMostrarFiltros" style="display:none;">
                <span class="material-icons" style="font-size:1rem;vertical-align:middle;">chevron_right</span> Mostrar filtros
            </button>

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
                <div id="ceEstado" class="small text-muted">Cargando información...</div>
                <div id="ceChips"></div>
            </div>

            <div class="ce-kpis" id="ceKpis"></div>

            <div class="ce-toolbar">
                <div class="ce-tb-group"><span class="ce-tb-label">Series por</span>
                    <div class="btn-group btn-group-sm ce-seg" id="ceSegSerie">
                        <button type="button" class="btn btn-outline-secondary active" data-v="fuente">Fuente</button>
                        <button type="button" class="btn btn-outline-secondary" data-v="tipo">Tipo</button>
                        <button type="button" class="btn btn-outline-secondary" data-v="flor">Flor</button>
                        <button type="button" class="btn btn-outline-secondary" data-v="color">Color</button>
                        <button type="button" class="btn btn-outline-secondary" data-v="variedad">Variedad</button>
                        <button type="button" class="btn btn-outline-secondary" data-v="anio">Año</button>
                    </div></div>
                <div class="ce-tb-group"><span class="ce-tb-label">Gráfico</span>
                    <div class="btn-group btn-group-sm ce-seg" id="ceSegTipo">
                        <button type="button" class="btn btn-outline-secondary active" data-v="line">Líneas</button>
                        <button type="button" class="btn btn-outline-secondary" data-v="area">Área</button>
                        <button type="button" class="btn btn-outline-secondary" data-v="bar">Barras</button>
                        <button type="button" class="btn btn-outline-secondary" data-v="stack">Apiladas</button>
                    </div></div>
                <div class="ce-tb-group"><span class="ce-tb-label">Top</span>
                    <select id="ceTopN" class="form-control form-control-sm" style="width:auto;">
                        <option value="5">5</option><option value="8">8</option>
                        <option value="12" selected>12</option><option value="20">20</option><option value="0">Todas</option>
                    </select></div>
                <div class="ce-tb-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="ceAcumulado">
                        <label class="custom-control-label small" for="ceAcumulado">Acumulado</label>
                    </div></div>
                <div class="ce-tb-group">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="ceBtnPng"><span class="material-icons" style="font-size:1rem;vertical-align:middle;">image</span> PNG</button>
                </div>
            </div>

            <div class="ce-card" id="ceCardChart">
                <div class="ce-card-head">
                    <h5 class="ce-card-title" id="ceChartTitle">Tallos por semana</h5>
                    <span class="ce-card-sub">Clic en la leyenda oculta/muestra una serie · clic en una semana la fija como foco</span>
                </div>
                <div style="position:relative;height:360px;"><canvas id="ceChart"></canvas></div>
            </div>

            <div class="ce-card" id="ceCardComp">
                <div class="ce-card-head">
                    <h5 class="ce-card-title">Comparación entre dos escenarios</h5>
                    <div class="d-flex align-items-center flex-wrap" style="gap:.5rem;">
                        <label class="small mb-0 font-weight-bold" for="ceBase">Base</label>
                        <select id="ceBase" class="form-control form-control-sm" style="width:auto;max-width:200px;"></select>
                        <span class="material-icons text-muted" style="font-size:1.1rem;">compare_arrows</span>
                        <label class="small mb-0 font-weight-bold" for="ceComp">Comparar</label>
                        <select id="ceComp" class="form-control form-control-sm" style="width:auto;max-width:200px;"></select>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="ceSwap" title="Invertir"><span class="material-icons" style="font-size:1rem;vertical-align:middle;">swap_horiz</span></button>
                    </div>
                </div>
                <div class="ce-kpis" id="ceCompKpis"></div>
                <div style="position:relative;height:280px;"><canvas id="ceChartDiff"></canvas></div>
                <div class="ce-tbl-wrap mt-3" style="max-height:320px;">
                    <table class="table table-sm table-striped ce-tbl mb-0" id="ceTblComp"></table>
                </div>
            </div>

            <div class="ce-card">
                <div class="ce-card-head">
                    <h5 class="ce-card-title" id="cePivotTitle">Pivote semanal</h5>
                    <div>
                        <span class="ce-card-sub mr-2">Clic en una fila oculta/muestra la serie en el gráfico</span>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="ceCsvPivot"><span class="material-icons" style="font-size:1rem;vertical-align:middle;">download</span> CSV</button>
                    </div>
                </div>
                <div class="ce-tbl-wrap"><table class="table table-sm ce-tbl mb-0" id="ceTblPivot"></table></div>
            </div>

            <div class="ce-card">
                <div class="ce-card-head">
                    <h5 class="ce-card-title">Resumen por dimensión y fuente</h5>
                    <div class="d-flex align-items-center" style="gap:.5rem;">
                        <label class="small mb-0 font-weight-bold" for="ceDimResumen">Filas</label>
                        <select id="ceDimResumen" class="form-control form-control-sm" style="width:auto;">
                            <option value="flor">Flor</option><option value="color">Color</option>
                            <option value="variedad" selected>Variedad</option><option value="tipo">Tipo</option>
                            <option value="anio">Año</option><option value="semana">Semana</option>
                        </select>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="ceCsvResumen"><span class="material-icons" style="font-size:1rem;vertical-align:middle;">download</span> CSV</button>
                    </div>
                </div>
                <div class="ce-tbl-wrap"><table class="table table-sm table-striped ce-tbl mb-0" id="ceTblResumen"></table></div>
            </div>

            <div class="ce-card">
                <div class="ce-card-head">
                    <h5 class="ce-card-title">Detalle</h5>
                    <div class="d-flex align-items-center" style="gap:.5rem;">
                        <input type="search" id="ceBuscar" class="form-control form-control-sm" placeholder="Buscar..." style="width:180px;">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="ceCsvDetalle"><span class="material-icons" style="font-size:1rem;vertical-align:middle;">download</span> CSV</button>
                    </div>
                </div>
                <div class="ce-tbl-wrap"><table class="table table-sm table-striped ce-tbl mb-0" id="ceTblDetalle"></table></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted" id="ceDetalleInfo"></small>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" id="cePrev">&laquo;</button>
                        <button type="button" class="btn btn-outline-secondary" id="ceNext">&raquo;</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.CE_CONFIG = { endpoint: '/ajax/compara_escenarios.php' };
</script>
<script src="/scripts/compara_escenarios.js?v=1"></script>
