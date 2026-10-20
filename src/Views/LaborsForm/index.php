<link rel="stylesheet" href="scripts/crud_tables.css?v=2">

<div class="container-fluid" style="padding-top:6px;">
    <!-- Page Header Banner -->
    <div class="crud-page-header">
        <div class="crud-header-left">
            <div class="crud-header-icon">
                <span class="material-icons">spa</span>
            </div>
            <div>
                <h4 class="crud-page-title">Labores de Siembra</h4>
                <div class="crud-page-subtitle">
                    <span class="crud-page-badge">Siembras</span>
                    <span class="text-muted d-none d-sm-inline">• Registro y control de ensartes, cosechas de esquejes y bancos</span>
                </div>
            </div>
        </div>
        <div class="crud-header-actions">
            <div id="nuevo">
                <button id="btnNuevo" type="button" class="btn btn-crud-new" data-toggle="modal">
                    <span class="material-icons">add</span> Nueva Labor
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filtros -->
        <div class="col-md-4 col-lg-3 mb-3">
            <div class="crud-filtro-sidebar">
                <div class="card mb-3">
                    <div class="card-header" id="crudFiltrosToggle" data-toggle="collapse" data-target="#crudFiltrosBody" aria-expanded="true">
                        <span>
                            <span class="material-icons" style="font-size:1.05rem;vertical-align:middle;margin-right:4px;">filter_alt</span>
                            Filtros de Búsqueda
                        </span>
                        <span class="material-icons toggle-icon">expand_less</span>
                    </div>
                    <div class="card-body collapse show" id="crudFiltrosBody">
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="fecha_ini">Fecha Inicial</label>
                            <input class="form-control" type="date" id="fecha_ini">
                        </div>

                        <div class="filter-group mb-2">
                            <label class="filter-label" for="fecha_fin">Fecha Final</label>
                            <input class="form-control" type="date" id="fecha_fin">
                        </div>

                        <div class="filter-group mb-3">
                            <label class="filter-label" for="tipo">Tipo de Labor</label>
                            <select id="tipo" class="form-control">
                                <option value="0">Programa</option>
                                <option value="1">Reemplazo</option>
                            </select>
                        </div>

                        <button id="listar" type="submit" onclick="listar()" class="btn btn-brand-green btn-block d-flex align-items-center justify-content-center py-2">
                            <span class="material-icons mr-1" style="font-size: 1.1rem;">search</span> Consultar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Table -->
        <div class="col-md-8 col-lg-9 mb-3">
            <div class="crud-table-card">
                <div class="table-responsive">
                    <table id="tablaLabores" class="display table table-striped table-hover" style="width:100%">
                        <thead class="text-center">
                            <tr>
                                <th>Id</th>
                                <th>Variedad</th>
                                <th>Temporada</th>
                                <th>Fecha Ensarte</th>
                                <th>Esquejes Ensartados</th>
                                <th>Fecha Cosecha</th>
                                <th>Esquejes Cosechados</th>
                                <th>Banco</th>
                                <th>Esquejes</th>
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

<!-- Modal para CRUD -->
<div class="modal fade crud-modal" id="modalCRUD" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="crud-modal-header-icon mr-3">
                    <span class="material-icons text-brand-green">spa</span>
                </div>
                <div>
                    <h5 class="modal-title font-weight-bold" id="exampleModalLabel">Labor</h5>
                    <p class="modal-subtitle text-muted">Gestión de labor de siembra</p>
                </div>
                <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formLabors">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark" for="variedad">Variedad</label>
                                <input type="text" class="form-control" id="variedad" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark" for="temporada_obj">Temporada</label>
                                <input type="text" class="form-control" id="temporada_obj" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark" for="fecha_ensarte_r">Fecha Ensarte</label>
                                <input type="date" class="form-control" id="fecha_ensarte_r">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark" for="esquejes_ensarte">Esquejes Ensartados</label>
                                <input type="number" class="form-control" id="esquejes_ensarte" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark" for="fecha_cosecha_r">Fecha Cosecha</label>
                                <input type="date" class="form-control" id="fecha_cosecha_r">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark" for="esquejes_cosecha">Esquejes Cosechados</label>
                                <input type="number" class="form-control" id="esquejes_cosecha" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="form-label font-weight-bold small text-dark" for="banco">Banco</label>
                                <input type="text" class="form-control" id="banco" placeholder="Número o nombre de banco">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-dismiss="modal">
                        <span class="material-icons align-middle mr-1" style="font-size: 1rem;">close</span> Cancelar
                    </button>
                    <button type="submit" id="btnGuardar" class="btn btn-brand-green btn-sm px-4">
                        <span class="material-icons align-middle mr-1" style="font-size: 1rem;">save</span> Guardar Datos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="scripts/main_ls.js"></script>