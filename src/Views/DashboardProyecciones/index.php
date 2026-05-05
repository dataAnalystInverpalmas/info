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
    <div class="row" id="dpFlorCards">
        <div class="col-12 text-center text-muted py-5">
            <span class="spinner-border spinner-border-sm me-2"></span> Cargando...
        </div>
    </div>
</div>

<script src="/scripts/dashboard_proyecciones.js?v=2"></script>
