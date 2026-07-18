<style>
    .dp-title { font-size: 1.15rem; font-weight: 600; margin-bottom: 0.75rem; }
    .dp-filter-card { border: 1px solid #e9ecef; border-radius: 0.5rem; background: #fff; padding: 0.75rem; }
    .dp-flor-card { border-radius: 0.5rem; background: #fff; height: 100%; box-shadow: 0 1px 4px rgba(0,0,0,.08); overflow: hidden; }
    .dp-flor-card-header { color: #fff; padding: 0.55rem 0.75rem; font-weight: 700; font-size: 1.15rem; text-align: center; letter-spacing: 1px; }
    .dp-flor-card-body { padding: 0.65rem; }
    .dp-sub-card { border: 1px solid #e9ecef; border-radius: 0.4rem; padding: 0.45rem 0.6rem; margin-bottom: 0.45rem; background: #f8f9fa; }
    .dp-sub-label { font-size: 0.73rem; color: #6c757d; margin-bottom: 0.1rem; }
    .dp-sub-value { font-size: 1.05rem; font-weight: 700; }
    .dp-sub-value.dp-neutral  { color: #0d6efd; }
    .dp-sub-value.dp-positive { color: #198754; }
    .dp-sub-value.dp-negative { color: #dc3545; }
    .dp-divider { border-top: 1px dashed #dee2e6; margin: 0.5rem 0; }
    
    .chart-container-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        border: 1px solid #E2E8F0;
        margin-bottom: 1.5rem;
        min-height: 380px;
    }
    .chart-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #2D3748;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #EDF2F7;
        padding-bottom: 0.5rem;
    }
</style>

<div class="container-fluid">
    <h4 class="dp-title">Dashboard de Proyecciones</h4>

    <!-- Filtros -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="dp-filter-card">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-2">
                        <label class="mb-1 form-label" for="dpFiltroFinca">Finca</label>
                        <input type="text" id="dpFiltroFinca" class="form-control form-control-sm" placeholder="Ej: 002">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="mb-1 form-label" for="dpFechaDesde">Fecha desde</label>
                        <input type="date" id="dpFechaDesde" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="mb-1 form-label" for="dpFechaHasta">Fecha hasta</label>
                        <input type="date" id="dpFechaHasta" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 mb-2">
                        <button id="dpBtnAplicarFiltros" class="btn btn-success btn-sm w-100">Aplicar filtros</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas por tipo de flor -->
    <div class="row mb-4" id="dpFlorCards">
        <div class="col-12 text-center text-muted py-5">
            <span class="spinner-border spinner-border-sm me-2"></span> Cargando...
        </div>
    </div>

    <!-- Sección de Gráficos de Alto Nivel -->
    <div class="row">
        <!-- Gráfico 1: Plantas sembradas por flor -->
        <div class="col-md-4">
            <div class="chart-container-card">
                <h5 class="chart-title"><span class="material-icons align-middle mr-1" style="font-size: 1.15rem; color: #00796B;">yard</span> Plantas Sembradas por Flor</h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="chartPlantasFlor"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 2: Relación de Edad de Siembras (Semanas transcurridas) -->
        <div class="col-md-4">
            <div class="chart-container-card">
                <h5 class="chart-title"><span class="material-icons align-middle mr-1" style="font-size: 1.15rem; color: #4a5568;">query_builder</span> Edades y Densidad de Plantas</h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="chartEdades"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico 3: Distribución Porcentual por Color -->
        <div class="col-md-4">
            <div class="chart-container-card">
                <h5 class="chart-title"><span class="material-icons align-middle mr-1" style="font-size: 1.15rem; color: #e53e3e;">pie_chart</span> Distribución por Color (%)</h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="chartVariedades"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/scripts/dashboard_proyecciones.js?v=4"></script>
