<style>
    .dp-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.85rem; }
    .dp-filter-card,
    .dp-section-card {
        border: 1px solid #e5e7eb;
        border-radius: 0.65rem;
        background: #fff;
        padding: 0.9rem;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
    }
    .dp-kpi-card {
        border: 1px solid #dbeafe;
        border-radius: 0.65rem;
        background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
        padding: 1rem;
    }
    .dp-kpi-label { color: #334155; font-size: 0.85rem; margin-bottom: 0.35rem; }
    .dp-kpi-value { color: #0f172a; font-size: 1.7rem; font-weight: 700; line-height: 1; }
    .dp-kpi-sub { color: #475569; font-size: 0.82rem; margin-top: 0.4rem; }
    .dp-section-title { font-size: 1rem; font-weight: 700; margin-bottom: 0.65rem; color: #1f2937; }
    .dp-table-wrapper { max-height: 360px; overflow: auto; }
    .dp-table thead th { position: sticky; top: 0; background: #f8fafc; z-index: 2; }
</style>

<div class="container-fluid">
    <h4 class="dp-title">Dashboard de Camas Sembradas</h4>

    <div class="row mb-3">
        <div class="col-12">
            <div class="dp-filter-card">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-2">
                        <label class="mb-1 form-label" for="dpFiltroFinca">Finca</label>
                        <input type="text" id="dpFiltroFinca" class="form-control form-control-sm" placeholder="Ej: INVERPALMAS">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="mb-1 form-label" for="dpFiltroProducto">Flor</label>
                        <input type="text" id="dpFiltroProducto" class="form-control form-control-sm" placeholder="Ej: CLAVEL">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="mb-1 form-label" for="dpFechaDesde">Fecha desde</label>
                        <input type="date" id="dpFechaDesde" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="mb-1 form-label" for="dpFechaHasta">Fecha hasta</label>
                        <input type="date" id="dpFechaHasta" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button id="dpBtnAplicarFiltros" class="btn btn-success btn-sm w-100">Aplicar filtros</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-lg-4 mb-3">
            <div class="dp-kpi-card">
                <div class="dp-kpi-label">Total de camas sembradas</div>
                <div class="dp-kpi-value" id="dpTotalCamas">0.0</div>
                <div class="dp-kpi-sub">Cálculo: plantas / 960 (redondeado a 1 decimal)</div>
            </div>
        </div>
        <div class="col-lg-8 mb-3" id="dpEstadoCarga">
            <div class="alert alert-info mb-0">Cargando información...</div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-3">
            <div class="dp-section-card h-100">
                <h5 class="dp-section-title">Camas por finca y flor</h5>
                <div class="dp-table-wrapper">
                    <table class="table table-sm table-striped dp-table mb-0">
                        <thead>
                            <tr>
                                <th>Finca</th>
                                <th>Flor</th>
                                <th class="text-end">Plantas</th>
                                <th class="text-end">Camas</th>
                            </tr>
                        </thead>
                        <tbody id="dpTablaFincaFlor">
                            <tr><td colspan="4" class="text-center text-muted">Sin datos</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-3">
            <div class="dp-section-card h-100">
                <h5 class="dp-section-title">Camas por flor (agregado)</h5>
                <div class="dp-table-wrapper">
                    <table class="table table-sm table-striped dp-table mb-0">
                        <thead>
                            <tr>
                                <th>Flor</th>
                                <th class="text-end">Plantas</th>
                                <th class="text-end">Camas</th>
                            </tr>
                        </thead>
                        <tbody id="dpTablaFlor">
                            <tr><td colspan="3" class="text-center text-muted">Sin datos</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="dp-section-card">
                <h5 class="dp-section-title">CLAVEL: camas por edad y finca</h5>
                <div style="position: relative; height: 380px;">
                    <canvas id="dpChartEdadClavel"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="dp-section-card">
                <h5 class="dp-section-title">MINICLAVEL: camas por edad y finca</h5>
                <div style="position: relative; height: 380px;">
                    <canvas id="dpChartEdadMiniclavel"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/scripts/dashboard_proyecciones.js?v=6"></script>
