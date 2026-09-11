<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .ccp-bar {
        background: #ffffff;
        border-bottom: 1px solid #eef2f5;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin: -0.5rem -15px 1rem;
        padding: 0.9rem 15px;
    }
    .ccp-bar h1 { font-size: 1.05rem; font-weight: 700; margin: 0; color: #17312d; }
    .ccp-bar p { margin: 0.15rem 0 0; color: #5f6f6b; font-size: 0.8rem; }

    .ccp-filtro-sidebar { position: sticky; top: 1rem; }
    .ccp-filtro-sidebar .card { border: 1px solid #dbe7e4; border-radius: 14px; }
    .ccp-filtro-sidebar .card-header {
        background: linear-gradient(135deg, #00796B 0%, #00695c 100%);
        color: #fff;
        border-radius: 14px 14px 0 0 !important;
        font-weight: 700;
        font-size: 0.85rem;
        padding: 0.6rem 0.9rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
    }
    .ccp-filtro-sidebar .card-header .material-icons.toggle-icon { font-size: 1.1rem; transition: transform 0.2s ease; }
    .ccp-filtro-sidebar .card-header.collapsed .toggle-icon { transform: rotate(-90deg); }
    .ccp-filtro-sidebar label { font-size: 0.78rem; font-weight: 600; color: #1f2937; letter-spacing: 0.02em; }
    .ccp-filtro-sidebar .select2-container { width: 100% !important; }
    .ccp-filtro-sidebar .select2-container .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: calc(1.5em + .75rem + 2px);
    }
    .ccp-filtros-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.25rem 0.75rem; }

    .ccp-section-card {
        border: 1px solid #e1ebe9;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 77, 64, 0.05);
        padding: 1rem;
        min-width: 0;
    }
    .ccp-section-title { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem; color: #17312d; }
    .ccp-table-wrapper { max-height: 340px; overflow: auto; }
    .ccp-table thead th { position: sticky; top: 0; background: #f8fafc; z-index: 2; }
    .ccp-modo-group .btn.active { background-color: #00796B; border-color: #00796B; color: #fff; }

    @media (max-width: 767.98px) {
        .ccp-filtro-sidebar { position: static; margin-bottom: 1rem; }
    }
    @media (max-width: 575.98px) {
        .ccp-filtros-grid { grid-template-columns: minmax(0, 1fr); }
    }
</style>

<div class="ccp-bar">
    <h1>Curvas de Producción Clavel</h1>
    <p>Tendencia de F/M y corte por edad o por año-semana, con filtros combinables por finca, bloque, flor, variedad, cosecha y ciclo.</p>
</div>

<div class="container-fluid px-3 px-lg-4 pb-4">
    <div class="row" id="ccpRow">
        <div class="col-md-4 col-lg-3" id="ccpSidebarCol">
            <div class="ccp-filtro-sidebar mr-0 mr-lg-3">
                <div class="card mb-3 shadow-sm">
                    <div class="card-header" id="ccpFiltrosToggle">
                        <span>
                            <span class="material-icons" style="font-size:1rem;vertical-align:middle;">filter_list</span>
                            Filtros
                        </span>
                        <span class="material-icons toggle-icon">chevron_left</span>
                    </div>
                    <div class="card-body p-3" id="ccpFiltrosBody">
                        <div class="ccp-filtros-grid">
                            <div class="mb-2">
                                <label for="ccpFiltroFinca">Finca</label>
                                <select id="ccpFiltroFinca" class="form-control select2" multiple>
                                    <?php foreach ($fincas as $finca): ?>
                                        <option value="<?php echo htmlspecialchars($finca); ?>"><?php echo htmlspecialchars($finca); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="ccpFiltroBloque">Bloque</label>
                                <select id="ccpFiltroBloque" class="form-control select2" multiple>
                                    <?php foreach ($bloques as $bloque): ?>
                                        <option value="<?php echo htmlspecialchars($bloque); ?>"><?php echo htmlspecialchars($bloque); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="ccpFiltroFlor">Flor</label>
                                <select id="ccpFiltroFlor" class="form-control select2" multiple>
                                    <?php foreach ($flores as $flor): ?>
                                        <option value="<?php echo htmlspecialchars($flor); ?>"><?php echo htmlspecialchars($flor); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="ccpFiltroVariedad">Variedad</label>
                                <select id="ccpFiltroVariedad" class="form-control select2" multiple>
                                    <?php foreach ($variedades as $variedad): ?>
                                        <option value="<?php echo htmlspecialchars($variedad); ?>"><?php echo htmlspecialchars($variedad); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="ccpFiltroCosecha">Cosecha</label>
                                <select id="ccpFiltroCosecha" class="form-control select2" multiple>
                                    <?php foreach ($cosechas as $cosecha): ?>
                                        <option value="<?php echo htmlspecialchars($cosecha); ?>"><?php echo htmlspecialchars($cosecha); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="ccpFiltroCiclo">Ciclo</label>
                                <select id="ccpFiltroCiclo" class="form-control select2" multiple>
                                    <option value="1">Ciclo 1 (edad ≤ 35)</option>
                                    <option value="2">Ciclo 2 (edad &gt; 35)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-sm w-100 mb-2" style="background-color:#00796B;border-color:#00796B;color:#fff;" onclick="CurvasClavelProduccion.aplicarFiltros()">
                    <span class="material-icons" style="font-size:1rem;">search</span> Aplicar
                </button>
                <button class="btn btn-outline-secondary btn-sm w-100" onclick="CurvasClavelProduccion.limpiarFiltros()">
                    <span class="material-icons" style="font-size:1rem;">restart_alt</span> Limpiar
                </button>
            </div>
        </div>

        <div class="col-md-8 col-lg-9" id="ccpMainCol">
            <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="ccpBtnMostrarFiltros" style="display:none;" onclick="CurvasClavelProduccion.toggleSidebar()">
                <span class="material-icons" style="font-size:1rem;vertical-align:middle;">chevron_right</span> Mostrar filtros
            </button>

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <div id="ccpEstadoCarga" class="text-muted small">Cargando información...</div>
                <div class="btn-group ccp-modo-group btn-group-sm mt-2 mt-md-0" role="group" aria-label="Agrupar por">
                    <button type="button" class="btn btn-outline-secondary active" data-modo="edad">Por Edad</button>
                    <button type="button" class="btn btn-outline-secondary" data-modo="aass">Por Año-Semana</button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-3">
                    <div class="ccp-section-card">
                        <h5 class="ccp-section-title">Promedio F/M (flores por mata)</h5>
                        <div style="position: relative; height: 340px;">
                            <canvas id="ccpChartFM"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <div class="ccp-section-card">
                        <h5 class="ccp-section-title">Total Corte (tallos)</h5>
                        <div style="position: relative; height: 340px;">
                            <canvas id="ccpChartCorte"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="ccp-section-card">
                        <h5 class="ccp-section-title">Detalle numérico</h5>
                        <div class="ccp-table-wrapper">
                            <table class="table table-sm table-striped ccp-table mb-0">
                                <thead>
                                    <tr>
                                        <th id="ccpColX">Edad</th>
                                        <th class="text-end">Promedio F/M</th>
                                        <th class="text-end">Corte (suma)</th>
                                        <th class="text-end">Matas</th>
                                        <th class="text-end">Registros</th>
                                    </tr>
                                </thead>
                                <tbody id="ccpTablaDetalle">
                                    <tr><td colspan="5" class="text-center text-muted">Sin datos</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="ccp-section-card">
                        <h5 class="ccp-section-title">Acumulado por Ciclo</h5>
                        <div class="ccp-table-wrapper">
                            <table class="table table-sm table-striped ccp-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Ciclo</th>
                                        <th class="text-end">F/M acumulado</th>
                                        <th class="text-end">Corte (suma)</th>
                                        <th class="text-end">Matas (promedio)</th>
                                        <th class="text-end">Registros (suma)</th>
                                    </tr>
                                </thead>
                                <tbody id="ccpTablaAcumuladoCiclo">
                                    <tr><td colspan="5" class="text-center text-muted">Sin datos</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/scripts/curvas_clavel_produccion.js?v=4"></script>
