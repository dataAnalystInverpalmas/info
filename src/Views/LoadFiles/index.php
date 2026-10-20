<link rel="stylesheet" href="scripts/crud_tables.css?v=2">

<div id="loadFilesMainView" class="container-fluid" style="padding-top:6px;">
    <!-- Page Header Banner -->
    <div class="crud-page-header">
        <div class="crud-header-left">
            <div class="crud-header-icon">
                <span class="material-icons">cloud_upload</span>
            </div>
            <div>
                <h4 class="crud-page-title">Cargar Archivos</h4>
                <div class="crud-page-subtitle">
                    <span class="crud-page-badge">Siembras</span>
                    <span class="text-muted d-none d-sm-inline">• Actualización de matrices maestras de siembras y planos</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Subida de archivo -->
        <div class="col-lg-5 col-md-6 mb-4">
            <div class="crud-table-card h-100">
                <h5 class="font-weight-bold text-dark mb-3 d-flex align-items-center" style="font-size: 1.05rem;">
                    <span class="material-icons text-brand-green mr-2" style="font-size: 1.3rem;">file_upload</span>
                    Subir Nueva Matriz
                </h5>
                <p class="text-muted small mb-3">
                    Seleccione el archivo Excel (.xlsx) correspondiente para actualizar los datos en el sistema.
                </p>

                <form enctype="multipart/form-data" action="" method="post" id="formUploadFiles">
                    <div class="crud-upload-box">
                        <div class="crud-upload-icon">
                            <span class="material-icons">description</span>
                        </div>
                        <h6 class="font-weight-bold mb-1 text-dark">Seleccione un archivo Excel</h6>
                        <p class="text-muted small mb-3">Formatos admitidos: .xlsx, .xls</p>
                        
                        <div class="custom-file text-left mb-2">
                            <input type="file" class="form-control" id="archivoId" accept=".xlsx, .xls">
                        </div>

                        <!-- Preview Card -->
                        <div id="archivoPreviewBox" class="d-none mt-2 mb-3 p-3 rounded text-left" style="background:#f8fafc; border:1px solid #dce9e6;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center" style="min-width: 0;">
                                    <span class="material-icons text-success mr-2" style="font-size: 1.8rem; flex-shrink: 0;">table_view</span>
                                    <div style="min-width: 0;">
                                        <strong id="previewFileName" class="d-block text-dark small text-truncate"></strong>
                                        <span id="previewFileSize" class="text-muted" style="font-size:0.75rem;"></span>
                                    </div>
                                </div>
                                <span id="previewFileTag" class="badge badge-pill badge-light border px-2 py-1 font-weight-bold ml-2" style="color: #00796B; font-size: 0.72rem; white-space: nowrap;"></span>
                            </div>
                        </div>

                        <button type="button" class="btn btn-brand-green btn-block py-2 font-weight-bold d-inline-flex align-items-center justify-content-center" id="boton">
                            <span class="material-icons mr-1" style="font-size: 1.1rem;">cloud_upload</span>
                            <span>Subir Archivo al Servidor</span>
                        </button>

                        <div id="uploadAlertArea" class="mt-3 text-left"></div>
                    </div>
                </form>

                <div class="p-3 rounded" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                    <small class="text-muted font-weight-bold d-block mb-1">Archivos reconocidos automáticamente:</small>
                    <ul class="list-unstyled small text-muted mb-0" style="line-height: 1.7;">
                        <li><span class="material-icons text-success align-middle mr-1" style="font-size: 0.95rem;">check_circle</span> <code>Plantas Sembradas ld5000.xlsx</code></li>
                        <li><span class="material-icons text-success align-middle mr-1" style="font-size: 0.95rem;">check_circle</span> <code>tabla_variedades.xlsx</code></li>
                        <li><span class="material-icons text-success align-middle mr-1" style="font-size: 0.95rem;">check_circle</span> <code>tabla_hplano.xlsx</code></li>
                        <li><span class="material-icons text-success align-middle mr-1" style="font-size: 0.95rem;">check_circle</span> <code>tabla_arreglos.xlsx</code></li>
                        <li><span class="material-icons text-success align-middle mr-1" style="font-size: 0.95rem;">check_circle</span> <code>tabla_presupuesto.xlsx</code></li>
                        <li><span class="material-icons text-success align-middle mr-1" style="font-size: 0.95rem;">check_circle</span> <code>tabla_festivos.xlsx</code></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Lista de archivos en el servidor -->
        <div class="col-lg-7 col-md-6 mb-4">
            <div class="crud-table-card h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="font-weight-bold text-dark m-0 d-flex align-items-center" style="font-size: 1.05rem;">
                        <span class="material-icons text-brand-green mr-2" style="font-size: 1.3rem;">folder_open</span>
                        Archivos en el Servidor
                    </h5>
                    <span class="badge badge-pill badge-light border text-muted px-2 py-1"><?php echo count($files); ?> archivos</span>
                </div>

                <div class="table-responsive">
                    <table class="display table table-striped table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>Nombre del Archivo</th>
                                <th class="text-center" style="width: 110px;">Formato</th>
                                <th class="text-right" style="width: 180px;">Última Modificación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($files)): ?>
                                <?php foreach ($files as $filepath): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="material-icons text-success mr-2" style="font-size: 1.3rem;">table_view</span>
                                            <strong class="text-dark"><?php echo htmlspecialchars(pathinfo(basename($filepath), PATHINFO_FILENAME), ENT_QUOTES, 'UTF-8'); ?></strong>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light border text-uppercase font-weight-bold px-2 py-1" style="letter-spacing: 0.5px; color: #00796B;">
                                            <?php echo htmlspecialchars(pathinfo($filepath, PATHINFO_EXTENSION), ENT_QUOTES, 'UTF-8') ?: 'XLSX'; ?>
                                        </span>
                                    </td>
                                    <td class="text-right text-muted small">
                                        <span class="material-icons align-middle mr-1 text-muted" style="font-size: 0.95rem;">schedule</span>
                                        <?php echo date('d M Y, H:i', filemtime($filepath)); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">No hay archivos registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

