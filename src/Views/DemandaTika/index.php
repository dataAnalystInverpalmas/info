<link rel="stylesheet" href="scripts/crud_tables.css?v=2">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .crud-page-header .crud-header-actions.report-actions-bar {
        margin-bottom: 0 !important;
        flex: 1 1 auto;
        justify-content: flex-end;
    }

    .crud-page-header .crud-page-subtitle {
        font-size: 0.85rem;
        margin-top: 0;
    }

    .dtk-bar {
        background: #ffffff;
        border-bottom: 1px solid #eef2f5;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin: -0.5rem -15px 1rem;
        padding: 0.9rem 15px;
    }
    .dtk-bar h1 { font-size: 1.05rem; font-weight: 700; margin: 0; color: #17312d; }
    .dtk-bar p { margin: 0.15rem 0 0; color: #5f6f6b; font-size: 0.8rem; }

    .dtk-filtro-sidebar { position: sticky; top: 1rem; }
    .dtk-filtro-sidebar .card { border: 1px solid #dbe7e4; border-radius: 14px; }
    .dtk-filtro-sidebar .card-header {
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
    .dtk-filtro-sidebar .card-header .material-icons.toggle-icon { font-size: 1.1rem; transition: transform 0.2s ease; }
    .dtk-filtro-sidebar .card-header.collapsed .toggle-icon { transform: rotate(-90deg); }
    .dtk-filtro-sidebar label { font-size: 0.78rem; font-weight: 600; color: #1f2937; letter-spacing: 0.02em; }
    .dtk-filtro-sidebar .select2-container { width: 100% !important; }
    .dtk-filtro-sidebar .select2-container .select2-selection--multiple {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        min-height: calc(1.5em + .75rem + 2px);
    }
    .dtk-filtros-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.25rem 0.75rem; }
    .dtk-filtros-grid-full { grid-column: 1 / -1; }

    @media (max-width: 991.98px) {
        .dtk-filtro-sidebar { position: static !important; margin-bottom: 1.25rem; }
    }
    @media (max-width: 575.98px) {
        .dtk-filtros-grid { grid-template-columns: 1fr; }
    }

    .dtk-section-card {
        border: 1px solid #e1ebe9;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 77, 64, 0.05);
        padding: 1rem;
        min-width: 0;
    }
    .dtk-section-title { font-size: 0.95rem; font-weight: 700; margin: 0 0 0.75rem; color: #17312d; }
    .dtk-agrupar-group .btn.active { background-color: #00796B; border-color: #00796B; color: #fff; }

    .dtk-pivot-wrapper { max-height: 480px; overflow: auto; position: relative; }
    .dtk-pivot { border-collapse: separate; border-spacing: 0; white-space: nowrap; }
    .dtk-pivot thead th { position: sticky; top: 0; background: #f8fafc; z-index: 3; }
    .dtk-pivot th.dtk-col-flor, .dtk-pivot td.dtk-col-flor {
        position: sticky; left: 0; z-index: 2; background: #f8fafc; min-width: 60px;
    }
    .dtk-pivot th.dtk-col-color, .dtk-pivot td.dtk-col-color {
        position: sticky; left: 60px; z-index: 2; background: #f8fafc; min-width: 110px; text-align: left;
    }
    .dtk-pivot th.dtk-col-item, .dtk-pivot td.dtk-col-item {
        position: sticky; left: 170px; z-index: 2; background: #f8fafc; min-width: 180px; text-align: left;
    }
    .dtk-pivot thead th.dtk-col-flor, .dtk-pivot thead th.dtk-col-color, .dtk-pivot thead th.dtk-col-item { z-index: 4; }
    .dtk-pivot td.dtk-col-total, .dtk-pivot th.dtk-col-total { font-weight: 700; background: #eef6f4; }
    .dtk-pivot tfoot td { font-weight: 700; background: #eef6f4; position: sticky; bottom: 0; }
    .dtk-pivot tfoot td.dtk-col-flor, .dtk-pivot tfoot td.dtk-col-color, .dtk-pivot tfoot td.dtk-col-item { z-index: 2; }

    @media (max-width: 767.98px) {
        .dtk-filtro-sidebar { position: static; margin-bottom: 1rem; }
    }
    @media (max-width: 575.98px) {
        .dtk-filtros-grid { grid-template-columns: minmax(0, 1fr); }
    }
</style>

<!-- Page Header Banner / Navbar estilo CRUD EMV -->
<div class="crud-page-header d-print-none no-print">
    <div class="crud-header-left">
        <div class="crud-header-icon">
            <span class="material-icons">difference</span>
        </div>
        <div>
            <!-- Título omitido según indicación del usuario (ya presente en el navbar/breadcrumb superior) -->
            <div class="crud-page-subtitle">
                <span class="crud-page-badge">Producción</span>
                <span class="text-muted d-none d-sm-inline">• Comparativo y balance de demanda semanal Tika</span>
            </div>
        </div>
    </div>
    <div class="crud-header-actions report-actions-bar mb-0">
        <!-- Espacio para los chips de filtros activos a medida que se agregan -->
        <div class="filtros-aplicados"></div>
        <button type="button" class="btn btn-sm btn-teal d-flex align-items-center" data-bs-toggle="offcanvas"
            data-bs-target="#dtkFiltros" aria-controls="dtkFiltros" title="Mostrar filtros">
            <span class="material-icons me-1" style="font-size:1rem;">filter_list</span>
            Filtros
            <span class="badge rounded-pill bg-light text-dark ms-1 d-none filtros-badge">0</span>
        </button>
    </div>
</div>

<!-- Barra lateral vertical de filtros (mismo patrón que Reporte Lonas) -->
<div class="offcanvas offcanvas-end filtros-sidebar no-print" tabindex="-1" id="dtkFiltros"
    data-bs-scroll="true" data-bs-backdrop="false" aria-labelledby="dtkFiltrosLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="dtkFiltrosLabel">
            <span class="material-icons" style="font-size: 1.2rem; color: #00796B;">filter_list</span>
            Filtros
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            <label class="filter-label" for="dtkFiltroFlor">Flor</label>
            <select id="dtkFiltroFlor" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach ($flores as $flor): ?>
                    <option value="<?php echo htmlspecialchars($flor); ?>"><?php echo htmlspecialchars($flor); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="dtkFiltroMercado">Mercado</label>
            <select id="dtkFiltroMercado" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach ($mercados as $mercado): ?>
                    <option value="<?php echo htmlspecialchars($mercado); ?>"><?php echo htmlspecialchars($mercado); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="dtkFiltroSubmercado">Submercado</label>
            <select id="dtkFiltroSubmercado" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach ($submercados as $submercado): ?>
                    <option value="<?php echo htmlspecialchars($submercado); ?>"><?php echo htmlspecialchars($submercado); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="dtkFiltroGrupo">Grupo demanda</label>
            <select id="dtkFiltroGrupo" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach ($gruposDemanda as $grupo): ?>
                    <option value="<?php echo htmlspecialchars($grupo); ?>"><?php echo htmlspecialchars($grupo); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="dtkFiltroItem">Ítem</label>
            <select id="dtkFiltroItem" class="form-control select2" multiple data-cuenta-filtro>
                <?php foreach ($items as $item): ?>
                    <option value="<?php echo htmlspecialchars($item); ?>"><?php echo htmlspecialchars($item); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="dtkFiltroSemanaDesde">Semana desde</label>
            <select id="dtkFiltroSemanaDesde" class="form-control select2-single">
                <?php foreach ($semanas as $semana): ?>
                    <option value="<?php echo htmlspecialchars($semana); ?>" <?php echo $semana === $semanaDesdeDefault ? 'selected' : ''; ?>><?php echo htmlspecialchars($semana); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="filter-label" for="dtkFiltroSemanaHasta">Semana hasta</label>
            <select id="dtkFiltroSemanaHasta" class="form-control select2-single">
                <?php foreach ($semanas as $semana): ?>
                    <option value="<?php echo htmlspecialchars($semana); ?>" <?php echo $semana === $semanaHastaDefault ? 'selected' : ''; ?>><?php echo htmlspecialchars($semana); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="filtros-footer d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="DemandaTika.limpiarFiltros()" title="Limpiar todos los filtros">
            <span class="material-icons align-middle" style="font-size: 1rem;">restart_alt</span>
            Limpiar
        </button>
        <button type="button" class="btn btn-sm btn-teal flex-fill" data-filtros-aplicar onclick="DemandaTika.aplicarFiltros()" title="Consultar con los filtros seleccionados">
            <span class="material-icons align-middle" style="font-size: 1rem;">search</span>
            Consultar
        </button>
    </div>
</div>

<div class="container-fluid px-0 pb-4">
    <div class="row g-3" id="dtkRow">
        <div class="col-12" id="dtkMainCol">

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                <div id="dtkEstadoCarga" class="text-muted small">Cargando información...</div>
                <div class="btn-group dtk-agrupar-group btn-group-sm mt-2 mt-md-0" role="group" aria-label="Agrupar gráfico por" id="dtkAgruparGroup">
                    <?php foreach ($agruparOpciones as $clave => $etiqueta): ?>
                        <button type="button" class="btn btn-outline-secondary <?php echo $clave === $agruparPorDefecto ? 'active' : ''; ?>" data-agrupar="<?php echo htmlspecialchars($clave); ?>"><?php echo htmlspecialchars($etiqueta); ?></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="dtk-section-card">
                        <h5 class="dtk-section-title">Demanda total por semana</h5>
                        <div style="position: relative; height: 340px;">
                            <canvas id="dtkChartResumen"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="dtk-section-card">
                        <h5 class="dtk-section-title">Resumen por Flor y Color</h5>
                        <div class="dtk-pivot-wrapper">
                            <table class="table table-sm table-striped dtk-pivot mb-0" id="dtkTablaResumenFC">
                                <thead>
                                    <tr id="dtkResumenFCHead">
                                        <th class="dtk-col-flor">Flor</th>
                                        <th class="dtk-col-color">Color</th>
                                        <th class="dtk-col-total text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="dtkResumenFCBody">
                                    <tr><td colspan="3" class="text-center text-muted">Sin datos</td></tr>
                                </tbody>
                                <tfoot>
                                    <tr id="dtkResumenFCFoot">
                                        <td class="dtk-col-flor">Total</td>
                                        <td class="dtk-col-color"></td>
                                        <td class="dtk-col-total text-end">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-3">
                    <div class="dtk-section-card">
                        <h5 class="dtk-section-title">Pivote de demanda por ítem</h5>
                        <div class="dtk-pivot-wrapper">
                            <table class="table table-sm table-striped dtk-pivot mb-0" id="dtkTablaPivote">
                                <thead>
                                    <tr id="dtkPivoteHead">
                                        <th class="dtk-col-flor">Flor</th>
                                        <th class="dtk-col-color">Color</th>
                                        <th class="dtk-col-item">Ítem</th>
                                        <th class="dtk-col-total text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="dtkPivoteBody">
                                    <tr><td colspan="4" class="text-center text-muted">Sin datos</td></tr>
                                </tbody>
                                <tfoot>
                                    <tr id="dtkPivoteFoot">
                                        <td class="dtk-col-flor">Total</td>
                                        <td class="dtk-col-color"></td>
                                        <td class="dtk-col-item"></td>
                                        <td class="dtk-col-total text-end">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="scripts/demanda_tika.js?v=3"></script>
