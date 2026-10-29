<style>
    .proy-page-wrapper {
        padding: 0.5rem 0 2rem;
        color: #2D3748;
    }
    .proy-hero-header {
        background: linear-gradient(135deg, #00796B 0%, #004D40 100%);
        border-radius: 14px;
        padding: 1.25rem 1.6rem;
        color: #FFFFFF;
        box-shadow: 0 8px 24px -4px rgba(0, 121, 107, 0.25);
        margin-bottom: 1.25rem;
    }
    .proy-card {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        margin-bottom: 1.25rem;
    }
    .proy-filter-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #64748B;
        margin-bottom: 0.35rem;
        display: block;
    }
    .proy-table-wrapper {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    #table_proy_anual_wrapper .dataTables_length,
    #table_proy_anual_wrapper .dataTables_filter,
    #table_proy_anual_wrapper .dataTables_info,
    #table_proy_anual_wrapper .dataTables_paginate {
        font-size: 0.825rem;
        color: #64748B;
        margin-bottom: 0.75rem;
    }
    #table_proy_anual thead th {
        background-color: #F8FAFC !important;
        color: #475569 !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.04em !important;
        padding: 10px 14px !important;
        border-bottom: 2px solid #E2E8F0 !important;
    }
    #table_proy_anual tbody td {
        padding: 8px 14px !important;
        font-size: 0.84rem !important;
        color: #334155 !important;
        border-bottom: 1px solid #F1F5F9 !important;
        vertical-align: middle !important;
    }
    #table_proy_anual tfoot th {
        background-color: #F8FAFC !important;
        font-size: 0.85rem !important;
        font-weight: 700 !important;
        color: #00796B !important;
        border-top: 2px solid #E2E8F0 !important;
        padding: 10px 14px !important;
    }
</style>

<div class="proy-page-wrapper container-fluid">
    <!-- Header Banner -->
    <div class="proy-hero-header">
        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 1rem;">
            <div>
                <div class="d-flex align-items-center mb-1" style="gap: 0.6rem;">
                    <span class="badge badge-light px-2 py-1 font-weight-bold" style="color: #00796B; font-size: 0.75rem;">CULTIVO &bull; ÁREAS</span>
                    <span class="text-white-50 small">&bull; Asignación Finca y Bloque</span>
                </div>
                <h2 class="font-weight-bold mb-1 d-flex align-items-center" style="font-size: 1.45rem; letter-spacing: -0.01em;">
                    <span class="material-icons mr-2" style="font-size: 1.6rem;">map</span>
                    Presupuesto con Asignación de Área
                </h2>
                <p class="text-white-50 small mb-0">
                    Proyección espacial y geográfica de siembras por bloque, variedad, temporada y ciclo.
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-light font-weight-bold d-inline-flex align-items-center px-3" onclick="window.print();" style="border-radius: 8px; color: #004D40;">
                    <span class="material-icons mr-1" style="font-size: 1.1rem;">print</span> Imprimir
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ===== Panel de Filtros ===== -->
        <div class="col-xl-3 col-lg-4 col-md-12 mb-4">
            <div class="proy-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <span class="font-weight-bold text-dark d-flex align-items-center" style="font-size: 0.95rem;">
                        <span class="material-icons mr-2 text-muted" style="font-size: 1.2rem;">filter_alt</span>
                        Filtros de Área
                    </span>
                    <span class="badge badge-light border text-muted">Parámetros</span>
                </div>

                <form onsubmit="event.preventDefault(); listarProy();">
                    <div class="form-group mb-3">
                        <label class="proy-filter-label" for="finicialProy">Fecha Inicial</label>
                        <input class="form-control" type="date" id="finicialProy" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="proy-filter-label" for="ffinalProy">Fecha Final</label>
                        <input class="form-control" type="date" id="ffinalProy" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="proy-filter-label" for="proy_year">Tipo de Siembra</label>
                        <select id="proy_year" class="form-control"></select>
                    </div>

                    <button type="button" onclick="listarProy()" class="btn btn-success btn-block py-2 font-weight-bold d-inline-flex align-items-center justify-content-center" style="background-color: #00796B; border: none; border-radius: 8px;">
                        <span class="material-icons mr-1" style="font-size: 1.1rem;">search</span>
                        Consultar Asignación
                    </button>
                </form>
            </div>
        </div>

        <!-- ===== Tabla de Datos ===== -->
        <div class="col-xl-9 col-lg-8 col-md-12 mb-4">
            <div class="proy-table-wrapper">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <div class="d-flex align-items-center">
                        <span class="material-icons mr-2 text-muted" style="font-size: 1.25rem;">grid_on</span>
                        <h5 class="font-weight-bold text-dark mb-0" style="font-size: 1.05rem;">Detalle de Bloques y Variedades</h5>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped display" id="table_proy_anual" style="width:100%">
                        <thead>
                            <tr>
                                <th>Finca</th>
                                <th>Bloque</th>
                                <th>Flor</th>
                                <th width="30px">Variedad</th>
                                <th>Temporada</th>
                                <th>Ciclo</th>
                                <th>Fecha Siembra</th>
                                <th>Tipo</th>
                                <th>Plantas</th>
                                <th>Cód. Variedad</th>
                                <th>Cód. Temporada</th>
                                <th>Siembra</th>
                                <th>Color</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="8" style="text-align:right">Camas Consolidadas:</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/scriptsProy.js"></script>
