<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Estilo para el layout con filtros en sidebar */
    .card-panel { background:#fff; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.06); padding:16px; }
    
    .pc-filters .select2-container { width: 100% !important; }
    .pc-filters .select2-container .select2-selection--single { height: calc(1.5em + .75rem + 2px); padding: .375rem .75rem; border: 1px solid #ced4da; border-radius: .25rem; }
    
    .pc-filters h5 { margin-top:0; }
    .pc-filters .form-section { margin-bottom:16px; }
    .pc-filters .form-label { font-weight:500; font-size:0.92rem; }
    
    #tablaPlanoConsulta td,
    #tablaPlanoConsulta th {
        vertical-align: middle;
    }
    
    #tablaPlanoConsulta th:last-child,
    #tablaPlanoConsulta td:last-child {
        white-space: nowrap;
    }
    
    .app-content { padding-top: 20px; }
    .app-content.filters-hidden #filtersCol { display: none; }
    .app-content.filters-hidden #tableCol { -webkit-box-flex: 0; -ms-flex: 0 0 100%; flex: 0 0 100%; max-width: 100%; width: 100%; }
    
    #btnToggleFilters { font-size: 0.85rem; }
</style>

<div class="container-fluid app-content">
    <div class="row">
        <div id="filtersCol" class="col-sm-3">
            <div class="card-panel pc-filters">
                <h5>Filtros</h5>

                <!-- Finca (Primary) -->
                <div class="form-group form-section">
                    <label class="form-label" for="pc_filter_finca">Finca</label>
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
                <div class="form-group form-section">
                    <label class="form-label" for="pc_filter_bloque">Bloque</label>
                    <select id="pc_filter_bloque" class="form-control" disabled>
                        <option value="">Todos</option>
                    </select>
                    <small class="form-text text-muted">Selecciona Finca primero</small>
                </div>

                <!-- Tabla (Dependent on Bloque) -->
                <div class="form-group form-section">
                    <label class="form-label" for="pc_filter_tabla">Tabla</label>
                    <select id="pc_filter_tabla" class="form-control" disabled>
                        <option value="">Todas</option>
                    </select>
                    <small class="form-text text-muted">Selecciona Bloque primero</small>
                </div>

                <!-- Nave (Dependent on Tabla) -->
                <div class="form-group form-section">
                    <label class="form-label" for="pc_filter_nave">Nave</label>
                    <select id="pc_filter_nave" class="form-control" disabled>
                        <option value="">Todas</option>
                    </select>
                    <small class="form-text text-muted">Selecciona Tabla primero</small>
                </div>

                <!-- Divider -->
                <hr style="margin: 20px 0;">

                <!-- Tipo Siembra -->
                <div class="form-group form-section">
                    <label class="form-label" for="pc_filter_tipo_siembra">Tipo Siembra</label>
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
                <div class="form-group form-section">
                    <label class="form-label" for="pc_filter_variedad">Variedad</label>
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
                <div class="form-group form-section">
                    <label class="form-label" for="pc_filter_cosecha">Cosecha</label>
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
                <div class="form-group form-section">
                    <label class="form-label" for="pc_filter_semana_siembra">Semana Siembra (YYWW)</label>
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
                <div class="d-flex gap-2">
                    <button id="pc_btn_filter" class="btn btn-sm btn-success">Filtrar</button>
                    <button id="pc_btn_clear_filters" class="btn btn-sm btn-secondary">Limpiar</button>
                </div>
            </div>
        </div>

        <div id="tableCol" class="col-sm-9">
            <div class="card-panel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="m-0">Plano Consulta</h4>
                    <div>
                        <button id="btnToggleFilters" class="btn btn-outline-secondary mr-2">Filtros</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tablaPlanoConsulta" class="display table table-striped table-bordered" style="width:100%">
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
