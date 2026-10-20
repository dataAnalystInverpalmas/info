<link rel="stylesheet" href="scripts/crud_tables.css?v=2">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .pc-filters .select2-container { width: 100% !important; }
    .pc-filters .select2-container .select2-selection--single { height: calc(1.5em + .75rem + 2px); padding: .375rem .75rem; border: 1px solid #cbd5e1; border-radius: 8px; }
    #tablaPlanoConsulta td, #tablaPlanoConsulta th { vertical-align: middle; }
    #tablaPlanoConsulta th:last-child, #tablaPlanoConsulta td:last-child { white-space: nowrap; }
    .app-content.filters-hidden #filtersCol { display: none; }
    .app-content.filters-hidden #tableCol { -webkit-box-flex: 0; -ms-flex: 0 0 100%; flex: 0 0 100%; max-width: 100%; width: 100%; }
</style>

<div class="container-fluid app-content" style="padding-top:6px;">
    <!-- Page Header Banner -->
    <div class="crud-page-header">
        <div class="crud-header-left">
            <div class="crud-header-icon">
                <span class="material-icons">grid_view</span>
            </div>
            <div>
                <h4 class="crud-page-title">Plano Consulta</h4>
                <div class="crud-page-subtitle">
                    <span class="crud-page-badge">Siembras</span>
                    <span class="text-muted d-none d-sm-inline">• Consulta georreferenciada de camas, variedades y siembras por bloque</span>
                </div>
            </div>
        </div>
        <div class="crud-header-actions">
            <button id="btnToggleFilters" class="btn btn-toggle-filters">
                <span class="material-icons">filter_list</span>
                <span>Filtros</span>
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar Filtros -->
        <div id="filtersCol" class="col-md-4 col-lg-3 mb-3">
            <div class="crud-filtro-sidebar pc-filters">
                <div class="card mb-3">
                    <div class="card-header" id="crudFiltrosPcToggle" data-toggle="collapse" data-target="#crudFiltrosPcBody" aria-expanded="true">
                        <span>
                            <span class="material-icons" style="font-size:1.05rem;vertical-align:middle;margin-right:4px;">filter_alt</span>
                            Filtros de Búsqueda
                        </span>
                        <span class="material-icons toggle-icon">expand_less</span>
                    </div>
                    <div class="card-body collapse show" id="crudFiltrosPcBody">
                        <!-- Finca (Primary) -->
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="pc_filter_finca">Finca</label>
                            <select id="pc_filter_finca" class="form-control">
                                <option value="">Todas</option>
                                <?php if (!empty($fincas)): ?>
                                    <?php foreach ($fincas as $finca): ?>
                                        <option value="<?php echo htmlspecialchars($finca); ?>"><?php echo htmlspecialchars($finca); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Bloque (Dependent on Finca) -->
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="pc_filter_bloque">Bloque</label>
                            <select id="pc_filter_bloque" class="form-control" disabled>
                                <option value="">Todos</option>
                            </select>
                            <small class="form-text text-muted">Selecciona Finca primero</small>
                        </div>

                        <!-- Tabla (Dependent on Bloque) -->
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="pc_filter_tabla">Tabla</label>
                            <select id="pc_filter_tabla" class="form-control" disabled>
                                <option value="">Todas</option>
                            </select>
                            <small class="form-text text-muted">Selecciona Bloque primero</small>
                        </div>

                        <!-- Nave (Dependent on Tabla) -->
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="pc_filter_nave">Nave</label>
                            <select id="pc_filter_nave" class="form-control" disabled>
                                <option value="">Todas</option>
                            </select>
                            <small class="form-text text-muted">Selecciona Tabla primero</small>
                        </div>

                        <hr class="my-2" style="border-top: 1px solid #e2e8f0;">

                        <!-- Tipo Siembra -->
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="pc_filter_tipo_siembra">Tipo Siembra</label>
                            <select id="pc_filter_tipo_siembra" class="form-control">
                                <option value="">Todos</option>
                                <?php if (!empty($tiposSiembra)): ?>
                                    <?php foreach ($tiposSiembra as $tipoSiembra): ?>
                                        <option value="<?php echo htmlspecialchars($tipoSiembra); ?>"><?php echo htmlspecialchars($tipoSiembra); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Variedad -->
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="pc_filter_variedad">Variedad</label>
                            <select id="pc_filter_variedad" class="form-control">
                                <option value="">Todas</option>
                                <?php if (!empty($variedades)): ?>
                                    <?php foreach ($variedades as $variedad): ?>
                                        <option value="<?php echo htmlspecialchars($variedad); ?>"><?php echo htmlspecialchars($variedad); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Cosecha -->
                        <div class="filter-group mb-2">
                            <label class="filter-label" for="pc_filter_cosecha">Cosecha</label>
                            <select id="pc_filter_cosecha" class="form-control">
                                <option value="">Todas</option>
                                <?php if (!empty($cosechas)): ?>
                                    <?php foreach ($cosechas as $cosecha): ?>
                                        <option value="<?php echo htmlspecialchars($cosecha); ?>"><?php echo htmlspecialchars($cosecha); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Semana Siembra -->
                        <div class="filter-group mb-3">
                            <label class="filter-label" for="pc_filter_semana_siembra">Semana Siembra (YYWW)</label>
                            <select id="pc_filter_semana_siembra" class="form-control">
                                <option value="">Todas</option>
                                <?php if (!empty($semanasSiembra)): ?>
                                    <?php foreach ($semanasSiembra as $semanaSiembra): ?>
                                        <option value="<?php echo htmlspecialchars($semanaSiembra); ?>"><?php echo htmlspecialchars($semanaSiembra); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex" style="gap: 8px;">
                            <button id="pc_btn_filter" class="btn btn-brand-green btn-sm flex-fill d-flex align-items-center justify-content-center">
                                <span class="material-icons mr-1" style="font-size: 1rem;">search</span> Filtrar
                            </button>
                            <button id="pc_btn_clear_filters" class="btn btn-outline-secondary btn-sm flex-fill d-flex align-items-center justify-content-center">
                                <span class="material-icons mr-1" style="font-size: 1rem;">backspace</span> Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="tableCol" class="col-md-8 col-lg-9 mb-3">
            <div class="crud-table-card">
                <div class="table-responsive">
                    <table id="tablaPlanoConsulta" class="display table table-striped table-hover table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Finca</th>
                                <th>Bloque</th>
                                <th>Tabla</th>
                                <th>Nave</th>
                                <th>Cama</th>
                                <th>Fecha siembra</th>
                                <th>Semana siembra</th>
                                <th>Origen</th>
                                <th>Tipo siembra</th>
                                <th>Variedad original</th>
                                <th>Cosecha original</th>
                                <th>Variedad reem</th>
                                <th>Cosecha reem</th>
                                <th>Plantas</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="scripts/plano_consulta.js?v=2"></script>

