<link rel="stylesheet" href="scripts/crud_tables.css?v=2">
<style>
    #tablaPlanoReemplazos td,
    #tablaPlanoReemplazos th {
        vertical-align: middle;
    }
    .pr-filter-grid {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: flex-end;
    }
    .pr-filter-grid .form-group {
        min-width: 180px;
        flex: 1 1 180px;
        margin-bottom: 0;
    }
</style>

<div class="container-fluid" style="padding-top:6px;">
    <!-- Page Header Banner -->
    <div class="crud-page-header">
        <div class="crud-header-left">
            <div class="crud-header-icon">
                <span class="material-icons">find_replace</span>
            </div>
            <div>
                <h4 class="crud-page-title">Plano de Reemplazos</h4>
                <div class="crud-page-subtitle">
                    <span class="crud-page-badge">Siembras</span>
                    <span class="text-muted d-none d-sm-inline">• Consulta georreferenciada de reemplazos y variedades sustitutas</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros Card -->
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="pr-filter-grid">
                <div class="form-group">
                    <label class="small font-weight-bold text-muted mb-1" for="pr_filter_finca">Finca</label>
                    <select id="pr_filter_finca" class="form-control form-control-sm">
                        <option value="">-- Todas --</option>
                        <?php if (!empty($fincas)): ?>
                            <?php foreach ($fincas as $finca): ?>
                                <option value="<?php echo htmlspecialchars($finca); ?>">
                                    <?php echo htmlspecialchars($finca); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold text-muted mb-1" for="pr_filter_bloque">Bloque</label>
                    <select id="pr_filter_bloque" class="form-control form-control-sm">
                        <option value="">-- Todos --</option>
                        <?php if (!empty($bloques)): ?>
                            <?php foreach ($bloques as $bloque): ?>
                                <option value="<?php echo htmlspecialchars($bloque); ?>">
                                    <?php echo htmlspecialchars($bloque); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold text-muted mb-1" for="pr_filter_tabla">Tabla</label>
                    <select id="pr_filter_tabla" class="form-control form-control-sm">
                        <option value="">-- Todas --</option>
                        <?php if (!empty($tablas)): ?>
                            <?php foreach ($tablas as $tabla): ?>
                                <option value="<?php echo htmlspecialchars($tabla); ?>">
                                    <?php echo htmlspecialchars($tabla); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold text-muted mb-1" for="pr_filter_nave">Nave</label>
                    <select id="pr_filter_nave" class="form-control form-control-sm">
                        <option value="">-- Todas --</option>
                        <?php if (!empty($naves)): ?>
                            <?php foreach ($naves as $nave): ?>
                                <option value="<?php echo htmlspecialchars($nave); ?>">
                                    <?php echo htmlspecialchars($nave); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold text-muted mb-1" for="pr_filter_tipo_siembra">Tipo Siembra</label>
                    <select id="pr_filter_tipo_siembra" class="form-control form-control-sm">
                        <option value="">-- Todos --</option>
                        <?php if (!empty($tiposSiembra)): ?>
                            <?php foreach ($tiposSiembra as $tipoSiembra): ?>
                                <option value="<?php echo htmlspecialchars($tipoSiembra); ?>">
                                    <?php echo htmlspecialchars($tipoSiembra); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold text-muted mb-1" for="pr_filter_variedad">Variedad</label>
                    <select id="pr_filter_variedad" class="form-control form-control-sm">
                        <option value="">-- Todas --</option>
                        <?php if (!empty($variedades)): ?>
                            <?php foreach ($variedades as $variedad): ?>
                                <option value="<?php echo htmlspecialchars($variedad); ?>">
                                    <?php echo htmlspecialchars($variedad); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold text-muted mb-1" for="pr_filter_cosecha">Cosecha</label>
                    <select id="pr_filter_cosecha" class="form-control form-control-sm">
                        <option value="">-- Todas --</option>
                        <?php if (!empty($cosechas)): ?>
                            <?php foreach ($cosechas as $cosecha): ?>
                                <option value="<?php echo htmlspecialchars($cosecha); ?>">
                                    <?php echo htmlspecialchars($cosecha); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold text-muted mb-1" for="pr_filter_semana_siembra">Semana Siembra</label>
                    <select id="pr_filter_semana_siembra" class="form-control form-control-sm">
                        <option value="">-- Todas --</option>
                        <?php if (!empty($semanasSiembra)): ?>
                            <?php foreach ($semanasSiembra as $semanaSiembra): ?>
                                <option value="<?php echo htmlspecialchars($semanaSiembra); ?>">
                                    <?php echo htmlspecialchars($semanaSiembra); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group" style="flex: 0 0 auto; min-width: auto;">
                    <button id="pr_btn_clear_filters" class="btn btn-outline-brand-green btn-sm d-flex align-items-center justify-content-center" style="height: 31px;" title="Limpiar filtros">
                        <span class="material-icons mr-1" style="font-size: 1rem;">backspace</span> Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Card -->
    <div class="crud-table-card">
        <div class="table-responsive">
            <table id="tablaPlanoReemplazos" class="table table-striped table-bordered display compact" style="width:100%">
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
                <tbody>
                    <?php if (!empty($rows)): ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string)($row['finca'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['bloque'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['tabla'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['nave'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['cama'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['fecha_siembra'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['semana_siembra'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['origen'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['tipo_siembra'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['variedad_original'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['cosecha_original'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['variedad_reem'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['cosecha_reem'] ?? '')); ?></td>
                                <td><?php echo htmlspecialchars((string)($row['plantas'] ?? '')); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="scripts/plano_reemplazos.js?v=3"></script>
