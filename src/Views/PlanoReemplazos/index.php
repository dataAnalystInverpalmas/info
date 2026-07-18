<style>
    .pr-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        padding: 16px;
    }

    .pr-toolbar {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: end;
        margin-bottom: 12px;
    }

    .pr-toolbar .form-group {
        min-width: 220px;
        margin-bottom: 0;
    }

    .pr-filters {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 12px;
        margin-bottom: 12px;
    }

    .pr-filters .form-group {
        min-width: 200px;
        margin-bottom: 0;
    }

    #tablaPlanoReemplazos td,
    #tablaPlanoReemplazos th {
        vertical-align: middle;
    }
</style>

<div class="container-fluid" style="padding-top:20px;">
    <div class="pr-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Plano Consulta</h4>
            <small class="text-muted">Siembras</small>
        </div>

        <div class="pr-filters">
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: end;">
                <div class="form-group">
                    <label class="small text-muted mb-1" for="pr_filter_finca">Filtrar por Finca</label>
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
                    <label class="small text-muted mb-1" for="pr_filter_bloque">Filtrar por Bloque</label>
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
                    <label class="small text-muted mb-1" for="pr_filter_tabla">Filtrar por Tabla</label>
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
                    <label class="small text-muted mb-1" for="pr_filter_nave">Filtrar por Nave</label>
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
                    <button id="pr_btn_clear_filters" class="btn btn-outline-secondary btn-sm" title="Limpiar filtros">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>

                <div class="form-group">
                    <label class="small text-muted mb-1" for="pr_filter_tipo_siembra">Filtrar por Tipo Siembra</label>
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
                    <label class="small text-muted mb-1" for="pr_filter_variedad">Filtrar por Variedad</label>
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
                    <label class="small text-muted mb-1" for="pr_filter_cosecha">Filtrar por Cosecha</label>
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
                    <label class="small text-muted mb-1" for="pr_filter_semana_siembra">Filtrar por Semana Siembra</label>
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
            </div>
        </div>

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
                            <tr
                            >
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
