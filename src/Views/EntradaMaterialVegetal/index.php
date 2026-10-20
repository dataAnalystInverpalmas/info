<link rel="stylesheet" href="scripts/crud_tables.css?v=2">
<style>
    .emv-nav {
        border-radius: 8px !important;
        font-weight: 600 !important;
        padding: 0.45rem 1rem !important;
        font-size: 0.85rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: all 0.2s ease !important;
    }

    .emv-nav.btn-success,
    .emv-nav.active {
        background-color: #00796B !important;
        border-color: #00796B !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(0, 121, 107, 0.25) !important;
    }

    .emv-nav.btn-outline-success {
        color: #00796B !important;
        border-color: #cbd5e1 !important;
        background-color: #ffffff !important;
    }

    .emv-nav.btn-outline-success:hover,
    .emv-nav.btn-outline-success:focus {
        background-color: #e6f4f1 !important;
        border-color: #00796B !important;
        color: #00796B !important;
    }

    .emv-filter-card {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        background: #ffffff;
        overflow: hidden;
        margin-bottom: 1.25rem;
    }

    .emv-filter-card .card-header {
        background: linear-gradient(135deg, #00796B 0%, #005a50 100%);
        color: #ffffff;
        padding: 0.75rem 1.25rem;
        font-size: 0.88rem;
        font-weight: 600;
        border: none;
    }
</style>

<div class="container-fluid" style="padding-top:6px;">
    <!-- Page Header Banner -->
    <div class="crud-page-header">
        <div class="crud-header-left">
            <div class="crud-header-icon">
                <span class="material-icons">inventory_2</span>
            </div>
            <div>
                <h4 class="crud-page-title">Entrada de Material Vegetal</h4>
                <div class="crud-page-subtitle">
                    <span class="crud-page-badge">Siembras</span>
                    <span class="text-muted d-none d-sm-inline">• Recepción de esquejes, planta madre y control de remisiones</span>
                </div>
            </div>
        </div>
        <div class="crud-header-actions">
            <div class="btn-group" role="group" aria-label="Menu de navegación EMV">
                <button type="button" class="btn btn-sm btn-success emv-nav active" data-emv-view="entrada">
                    <span class="material-icons" style="font-size: 1.05rem;">assignment</span> Entradas
                </button>
                <button type="button" class="btn btn-sm btn-outline-success emv-nav" data-emv-view="reporte">
                    <span class="material-icons" style="font-size: 1.05rem;">query_stats</span> Reporte / Detalle
                </button>
            </div>
        </div>
    </div>

    <!-- ===== Vista 1: Entradas ===== -->
    <div id="emv_view_entrada">
        <div class="row">
            <!-- Panel de filtros lateral -->
            <div class="col-md-4 col-lg-3 mb-3">
                <div class="crud-filtro-sidebar">
                    <div class="card mb-3">
                        <div class="card-header" id="crudFiltrosEmvToggle" data-toggle="collapse" data-target="#crudFiltrosEmvBody" aria-expanded="true">
                            <span>
                                <span class="material-icons" style="font-size:1.05rem;vertical-align:middle;margin-right:4px;">filter_alt</span>
                                Filtros de Búsqueda
                            </span>
                            <span class="material-icons toggle-icon">expand_less</span>
                        </div>
                        <div class="card-body collapse show" id="crudFiltrosEmvBody">
                            <div class="filter-group mb-2">
                                <label class="filter-label" for="emv_fecha_ini">Fecha Inicial</label>
                                <input class="form-control" type="date" id="emv_fecha_ini">
                            </div>

                            <div class="filter-group mb-2">
                                <label class="filter-label" for="emv_fecha_fin">Fecha Final</label>
                                <input class="form-control" type="date" id="emv_fecha_fin">
                            </div>

                            <div class="filter-group mb-2">
                                <label class="filter-label" for="emv_filtro_proveedor">Proveedor</label>
                                <select class="form-control" id="emv_filtro_proveedor">
                                    <option value="">— todos —</option>
                                </select>
                            </div>

                            <div class="filter-group mb-3">
                                <label class="filter-label" for="emv_filtro_material">Material</label>
                                <select class="form-control" id="emv_filtro_material">
                                    <option value="">— todos —</option>
                                    <option value="Esqueje">Esqueje</option>
                                    <option value="Planta madre">Planta madre</option>
                                </select>
                            </div>

                            <button type="button" onclick="emvListar()" class="btn btn-brand-green btn-block d-flex align-items-center justify-content-center py-2">
                                <span class="material-icons mr-1" style="font-size: 1.1rem;">search</span> Consultar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido principal: Tabla -->
            <div class="col-md-8 col-lg-9 mb-3">
                <div class="crud-table-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="font-weight-bold text-dark m-0 d-flex align-items-center" style="font-size: 1.05rem;">
                            <span class="material-icons text-brand-green mr-2" style="font-size: 1.3rem;">receipt_long</span>
                            Entradas Registradas
                        </h5>
                        <button id="btnEmvNuevo" type="button" class="btn btn-crud-new">
                            <span class="material-icons">add</span> Nueva Entrada
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="tableEmv" class="display table table-striped table-hover" style="width:100%">
                            <thead class="text-center">
                                <tr>
                                    <th>Id</th>
                                    <th>Fecha</th>
                                    <th>Maquila</th>
                                    <th>Proveedor</th>
                                    <th>Remisión</th>
                                    <th>Destino</th>
                                    <th>Material</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Vista 2: Reporte / Detalle ===== -->
    <div id="emv_view_reporte" style="display:none;">
        <div class="emv-filter-card">
            <div class="card-header">
                <strong><span class="material-icons align-middle mr-1" style="font-size: 1.1rem;">tune</span> Filtros del Reporte Detallado</strong>
            </div>
            <div class="card-body p-3">
                <div class="form-row">
                    <div class="form-group col-md-2 mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Fecha inicial</label>
                        <input type="date" class="form-control form-control-sm" id="emv_rep_fecha_ini">
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Fecha final</label>
                        <input type="date" class="form-control form-control-sm" id="emv_rep_fecha_fin">
                    </div>
                    <div class="form-group col-md-2 mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Maquila</label>
                        <input type="text" class="form-control form-control-sm" id="emv_rep_maquila" maxlength="4" placeholder="0000">
                    </div>
                    <div class="form-group col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Proveedor</label>
                        <select class="form-control form-control-sm" id="emv_rep_proveedor">
                            <option value="">— todos —</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Destino</label>
                        <select class="form-control form-control-sm" id="emv_rep_destino">
                            <option value="">— todos —</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Material</label>
                        <select class="form-control form-control-sm" id="emv_rep_material">
                            <option value="">— todos —</option>
                            <option value="Esqueje">Esqueje</option>
                            <option value="Planta madre">Planta madre</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Variedad</label>
                        <select class="form-control form-control-sm" id="emv_rep_variedad">
                            <option value="">— todas —</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 mb-2">
                        <label class="small font-weight-bold text-muted mb-1">Remisión</label>
                        <input type="text" class="form-control form-control-sm" id="emv_rep_remision" placeholder="Número de remisión">
                    </div>
                    <div class="form-group col-md-3 mb-2 d-flex align-items-end">
                        <button type="button" id="btnEmvReporteConsultar" class="btn btn-brand-green btn-sm btn-block d-flex align-items-center justify-content-center" style="height: 31px;">
                            <span class="material-icons mr-1" style="font-size: 1rem;">search</span> Consultar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="crud-table-card">
            <div class="table-responsive">
                <table id="tableEmvReporte" class="display table table-striped table-hover" style="width:100%">
                    <thead class="text-center">
                        <tr>
                            <th>Id</th>
                            <th>Fecha</th>
                            <th>Maquila</th>
                            <th>Proveedor</th>
                            <th>Remisión</th>
                            <th>Destino</th>
                            <th>Material</th>
                            <th>Variedad</th>
                            <th>Recibida</th>
                            <th>Facturado</th>
                            <th>Reposición</th>
                            <th>Excedente</th>
                            <th>Obsequio</th>
                            <th>Adicional</th>
                            <th>Raíz</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     Modal cabecera (Nuevo / Editar)
     ============================================================ -->
<div class="modal fade crud-modal" id="modalEmvCRUD" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="crud-modal-header-icon mr-3">
                    <span class="material-icons text-brand-green">inventory_2</span>
                </div>
                <div>
                    <h5 class="modal-title font-weight-bold" id="modalEmvCRUDTitle">Nueva Entrada</h5>
                    <p class="modal-subtitle text-muted">Cabecera de recepción de material</p>
                </div>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEmv">
                <div class="modal-body">
                    <input type="hidden" id="emv_id">
                    <div class="form-row">
                        <div class="form-group col-sm-6">
                            <label class="form-label font-weight-bold small text-dark" for="emv_fecha">Fecha</label>
                            <input type="date" class="form-control" id="emv_fecha" required>
                        </div>
                        <div class="form-group col-sm-6">
                            <label class="form-label font-weight-bold small text-dark" for="emv_maquila">Maquila <small class="text-muted">(4 dígitos)</small></label>
                            <input type="text" class="form-control" id="emv_maquila" maxlength="4" pattern="\d{1,4}" placeholder="0000" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label font-weight-bold small text-dark" for="emv_proveedor">Proveedor</label>
                        <select id="emv_proveedor" class="form-control" required>
                            <option value="">— seleccione —</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-sm-6">
                            <label class="form-label font-weight-bold small text-dark" for="emv_remision">Remisión</label>
                            <input type="text" class="form-control" id="emv_remision" placeholder="Número de remisión">
                        </div>
                        <div class="form-group col-sm-6">
                            <label class="form-label font-weight-bold small text-dark" for="emv_destino">Destino</label>
                            <select id="emv_destino" class="form-control" required>
                                <option value="">— seleccione —</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label font-weight-bold small text-dark" for="emv_material">Material</label>
                        <select id="emv_material" class="form-control" required>
                            <option value="">— seleccione —</option>
                            <option value="Esqueje">Esqueje</option>
                            <option value="Planta madre">Planta madre</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal">
                        <span class="material-icons align-middle mr-1" style="font-size: 1rem;">close</span> Cancelar
                    </button>
                    <button type="button" id="btnEmvVerDetalles" class="btn btn-outline-brand-green btn-sm px-3" style="display:none;">
                        <span class="material-icons align-middle mr-1" style="font-size: 1rem;">list_alt</span> Ir a Detalles
                    </button>
                    <button type="submit" class="btn btn-brand-green btn-sm px-4">
                        <span class="material-icons align-middle mr-1" style="font-size: 1rem;">save</span> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================
     Modal detalles
     ============================================================ -->
<div class="modal fade crud-modal bs-example-modal-lg" id="modalEmvDetalles" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="crud-modal-header-icon mr-3">
                    <span class="material-icons text-brand-green">list_alt</span>
                </div>
                <div>
                    <h5 class="modal-title font-weight-bold" id="modalEmvDetallesTitle">Detalles de Entrada</h5>
                    <p class="modal-subtitle text-muted">Carga de variedades y desglose de esquejes recibidos</p>
                </div>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <!-- Formulario para agregar un nuevo detalle -->
                <form id="formEmvDetalle">
                    <input type="hidden" id="det_entrada_id">

                    <div class="card mb-4" style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                        <div class="card-header py-2" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <strong class="text-dark small"><span class="material-icons align-middle mr-1 text-brand-green" style="font-size: 1rem;">add_circle</span> Agregar Detalle de Variedad</strong>
                        </div>
                        <div class="card-body p-3">
                            <div class="form-row">
                                <div class="form-group col-sm-3 mb-2">
                                    <label class="small font-weight-bold text-muted mb-1" for="det_filtro_flor">Flor (filtro)</label>
                                    <select class="form-control form-control-sm" id="det_filtro_flor">
                                        <option value="">— todas —</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-5 mb-2">
                                    <label class="small font-weight-bold text-muted mb-1" for="det_variedad">Variedad</label>
                                    <input type="text" class="form-control form-control-sm mb-1" id="det_variedad_busca" placeholder="Buscar variedad..." autocomplete="off">
                                    <select class="form-control form-control-sm" id="det_variedad" required size="1">
                                        <option value="">— seleccione —</option>
                                    </select>
                                </div>
                                <div class="form-group col-sm-2 mb-2">
                                    <label class="small font-weight-bold text-muted mb-1" for="det_cantidad_recibida">Cant. Recibida</label>
                                    <input type="number" class="form-control form-control-sm font-weight-bold text-brand-green" id="det_cantidad_recibida" readonly tabindex="-1" placeholder="0" style="background:#e6f4f1;">
                                </div>
                                <div class="form-group col-sm-2 mb-2">
                                    <label class="small font-weight-bold text-muted mb-1">Tipo de entrega</label>
                                    <div class="custom-control custom-checkbox mt-1">
                                        <input class="custom-control-input crud-checkbox" type="checkbox" id="det_raiz">
                                        <label class="custom-control-label small font-weight-bold text-dark" for="det_raiz">Con raíz</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col mb-2">
                                    <label class="small font-weight-bold text-muted mb-1" for="det_facturado">Facturado</label>
                                    <input type="number" class="form-control form-control-sm emv-suma" id="det_facturado" min="0" value="0">
                                </div>
                                <div class="form-group col mb-2">
                                    <label class="small font-weight-bold text-muted mb-1" for="det_reposicion">Reposición</label>
                                    <input type="number" class="form-control form-control-sm emv-suma" id="det_reposicion" min="0" value="0">
                                </div>
                                <div class="form-group col mb-2">
                                    <label class="small font-weight-bold text-muted mb-1" for="det_excedente">Excedente</label>
                                    <input type="number" class="form-control form-control-sm emv-suma" id="det_excedente" min="0" value="0">
                                </div>
                                <div class="form-group col mb-2">
                                    <label class="small font-weight-bold text-muted mb-1" for="det_obsequio">Obsequio</label>
                                    <input type="number" class="form-control form-control-sm emv-suma" id="det_obsequio" min="0" value="0">
                                </div>
                                <div class="form-group col mb-2">
                                    <label class="small font-weight-bold text-muted mb-1" for="det_adicional">Adicional</label>
                                    <input type="number" class="form-control form-control-sm emv-suma" id="det_adicional" min="0" value="0">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="small font-weight-bold text-muted mb-1" for="det_observacion">Observación</label>
                                <input type="text" class="form-control form-control-sm" id="det_observacion" placeholder="Observación (opcional)">
                            </div>
                            <button type="submit" class="btn btn-brand-green btn-block py-2 font-weight-bold">
                                <span class="material-icons align-middle mr-1" style="font-size: 1.1rem;">add_circle</span>
                                Agregar Detalle
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Tabla de detalles existentes -->
                <div class="table-responsive">
                    <table id="tableEmvDetalles" class="display table table-striped table-hover" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th>Id</th>
                                <th>Variedad</th>
                                <th>Cant. Recibida</th>
                                <th>Facturado</th>
                                <th>Reposición</th>
                                <th>Excedente</th>
                                <th>Obsequio</th>
                                <th>Adicional</th>
                                <th>Raíz</th>
                                <th>Observación</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-dismiss="modal">
                    <span class="material-icons align-middle mr-1" style="font-size: 1rem;">close</span> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="scripts/entrada_material_vegetal.js?v=<?= time() ?>"></script>