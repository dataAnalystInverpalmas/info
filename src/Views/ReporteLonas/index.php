<?php
/**
 * Vista del Reporte Semanal de Cosecha (Lonas).
 * Formato fiel a la PLANILLA PARA REGISTRO CORTE (I CYM SIE PRC 028).
 * Diseñado para ocupar la mitad del ancho de una hoja carta y caber exactamente en una página.
 */
?>
<!-- Dependencias de Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    /* ========================================================
       ESTILOS EN PANTALLA (SCREEN)
       ======================================================== */
    .reporte-lonas-container {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
        color: #1a202c;
        padding-bottom: 2.5rem;
    }

    .reporte-lonas-bar {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        margin: -0.5rem -15px 1rem;
        padding: 0.75rem 15px;
    }

    .reporte-lonas-bar h1 {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
        color: #004D40;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .reporte-lonas-bar p {
        margin: 0.15rem 0 0;
        color: #718096;
        font-size: 0.8rem;
    }

    /* Tarjeta de Filtros */
    .card-filtros-lonas {
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 3px 12px rgba(0, 0, 0, 0.03);
        border: 1px solid #e2e8f0;
        padding: 0.85rem 1rem;
        margin-bottom: 1rem;
    }

    .card-filtros-lonas .filter-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #4A5568;
        margin-bottom: 0.25rem;
    }

    .select2-container .select2-selection--single {
        height: 34px !important;
        border: 1px solid #cbd5e0 !important;
        border-radius: 6px !important;
        padding: 2px 6px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px !important;
        color: #2D3748 !important;
        font-size: 0.82rem !important;
    }

    /* Botones */
    .btn-teal {
        background-color: #00796B !important;
        border-color: #00796B !important;
        color: #ffffff !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
        padding: 0.4rem 0.85rem !important;
        font-size: 0.82rem !important;
    }

    .btn-teal:hover {
        background-color: #004D40 !important;
        color: #ffffff !important;
    }

    /* Wrapper de media página en pantalla (50% de ancho) */
    .planilla-wrapper {
        width: 100%;
        max-width: 520px;
        margin: 0 auto;
        background: #ffffff;
        border: 1px solid #cbd5e0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        padding: 14px 18px;
        border-radius: 4px;
        position: relative;
    }

    /* ENCABEZADO DE LA PLANILLA (Fiel a la imagen) */
    .planilla-header {
        text-align: center;
        margin-top: 12mm;
        margin-bottom: 5px;
    }

    .planilla-header .finca-name {
        font-size: 0.92rem;
        font-weight: 800;
        color: #000000;
        letter-spacing: 0.05em;
        margin: 0;
        line-height: 1.2;
    }

    .planilla-header .form-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: #000000;
        letter-spacing: 0.03em;
        margin: 1px 0;
        line-height: 1.2;
    }

    .planilla-header .doc-code {
        font-size: 0.7rem;
        font-weight: 700;
        color: #000000;
        letter-spacing: 0.06em;
        margin: 0;
        line-height: 1.2;
    }

    /* Campos de información tipo formulario con línea subrayada */
    .planilla-fields {
        margin-top: 7px;
        margin-bottom: 7px;
        font-size: 0.74rem;
        font-weight: 700;
        color: #000000;
    }

    .planilla-field-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 4px;
        gap: 8px;
    }

    .planilla-field {
        display: flex;
        align-items: flex-end;
        flex: 1;
        white-space: nowrap;
    }

    .planilla-field .field-label {
        font-weight: 800;
        font-size: 0.7rem;
        margin-right: 4px;
    }

    .planilla-field .field-val {
        flex: 1;
        border-bottom: 1px solid #000000;
        padding: 0 4px;
        min-height: 16px;
        font-weight: 700;
        font-size: 0.74rem;
        color: #000000;
        text-align: center;
    }

    /* TABLA TIPO CUADRÍCULA EXCEL / PLANILLA */
    .table-planilla {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.72rem;
        table-layout: fixed;
        color: #000000;
    }

    .table-planilla thead th {
        background-color: #ffffff;
        color: #000000;
        border: 1px solid #000000;
        font-weight: 800;
        text-align: center;
        padding: 3px 2px;
        font-size: 0.7rem;
        vertical-align: middle;
        line-height: 1.1;
    }

    /* Anchos relativos de las 11 columnas */
    .table-planilla th.col-sem {
        width: 6.5%;
    }

    .table-planilla th.col-dia {
        width: 8.3%;
    }

    .table-planilla th.col-tot {
        width: 11.8%;
    }

    .table-planilla th.col-fm {
        width: 11.8%;
    }

    .table-planilla th.col-acm {
        width: 11.8%;
    }

    .table-planilla tbody tr {
        height: 30px;
    }

    .table-planilla tbody td {
        border: 1px solid #000000;
        padding: 3.5px 3px;
        vertical-align: middle;
        line-height: 1.2;
        height: 30px;
        font-variant-numeric: tabular-nums;
        font-size: 0.74rem;
    }

    .table-planilla tbody td.cell-sem {
        text-align: center;
        font-weight: 800;
    }

    .table-planilla tbody td.cell-num {
        text-align: right;
    }

    .table-planilla tbody td.cell-tot {
        text-align: right;
        font-weight: 800;
    }

    .table-planilla tbody td.cell-fm,
    .table-planilla tbody td.cell-acm {
        text-align: center;
        font-weight: 700;
    }

    .table-planilla tbody td.cell-empty {
        color: transparent;
    }

    /* Estado de Carga */
    .loader-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.85);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 20;
    }

    /* ========================================================
       ESTILOS DE IMPRESIÓN (PRINT)
       - Ocupa el largo completo de la página carta sin saltar de hoja
       - Cuadrícula más ancha y tipos de letra más nítidos
       ======================================================== */
    @media print {
        @page {
            size: letter portrait;
            margin: 1.5mm 3.5mm 1mm 3.5mm;
        }

        html,
        body {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            height: 100% !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Ocultar elementos de UI */
        .fixed-top,
        .navbar,
        .navbar-custom,
        .reporte-lonas-bar,
        .card-filtros-lonas,
        .btn-toolbar-actions,
        .no-print,
        footer,
        #sidebar,
        #mostrar_loading,
        #mostrar_listo,
        #mostrar_error {
            display: none !important;
        }

        .reporte-lonas-container {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        /* EXACTAMENTE LA MITAD DE LA HOJA CARTA (50%) */
        .planilla-wrapper {
            width: 50% !important;
            max-width: 50% !important;
            float: left !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            page-break-inside: avoid !important;
            page-break-after: avoid !important;
        }

        .planilla-header {
            margin-top: 5mm !important;
            margin-bottom: 0.8mm !important;
        }

        .planilla-header .finca-name {
            font-size: 8.0pt !important;
            line-height: 1.1 !important;
        }

        .planilla-header .form-title {
            font-size: 7.4pt !important;
            line-height: 1.1 !important;
        }

        .planilla-header .doc-code {
            font-size: 6.0pt !important;
            line-height: 1 !important;
        }

        .planilla-fields {
            margin-top: 0.8mm !important;
            margin-bottom: 1.2mm !important;
            font-size: 6.2pt !important;
        }

        .planilla-field-row {
            margin-bottom: 0.5mm !important;
        }

        .planilla-field .field-label {
            font-size: 6.0pt !important;
            font-weight: 800 !important;
        }

        .planilla-field .field-val {
            font-size: 6.2pt !important;
            border-bottom: 0.5pt solid #000000 !important;
            min-height: 2.6mm !important;
            padding: 0 2px !important;
        }

        .table-planilla {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 6.5pt !important;
            line-height: 1 !important;
            table-layout: fixed !important;
        }

        .table-planilla thead th {
            border: 0.5pt solid #000000 !important;
            padding: 1.2px 1px !important;
            font-size: 6.6pt !important;
            font-weight: 800 !important;
            background: #ffffff !important;
            color: #000000 !important;
            height: 5.0mm !important;
        }

        .table-planilla th.col-sem {
            width: 6.5% !important;
        }

        .table-planilla th.col-dia {
            width: 8.3% !important;
        }

        .table-planilla th.col-tot {
            width: 11.8% !important;
        }

        .table-planilla th.col-fm {
            width: 11.8% !important;
        }

        .table-planilla th.col-acm {
            width: 11.8% !important;
        }

        .table-planilla tbody {
            page-break-inside: avoid !important;
        }

        /* FILAS CON ALTURA A 5.75mm */
        .table-planilla tbody tr {
            height: 5.8mm !important;
            max-height: 5.8mm !important;
            page-break-inside: avoid !important;
        }

        .table-planilla tbody td {
            border: 0.5pt solid #000000 !important;
            padding: 1px 2px !important;
            font-size: 6.5pt !important;
            color: #000000 !important;
            line-height: 1.15 !important;
            height: 5.8mm !important;
            vertical-align: middle !important;
            overflow: hidden !important;
            white-space: nowrap !important;
        }

        .table-planilla tbody td.cell-sem {
            font-weight: 800 !important;
            text-align: center !important;
        }

        .table-planilla tbody td.cell-tot {
            font-weight: 800 !important;
        }

        .table-planilla tbody td.cell-empty {
            color: transparent !important;
        }
    }
</style>

<div class="container-fluid reporte-lonas-container">

    <!-- Barra de Título y Acciones -->
    <div class="reporte-lonas-bar d-flex justify-content-between align-items-center">
        <div>
            <h1>
                <span class="material-icons" style="font-size: 1.35rem; color: #00796B;">description</span>
                Planilla para Registro Corte (Lonas)
            </h1>
            <p>
                Formato oficial (I CYM SIE PRC 028) para registro de corte diario. Ajustado a media página tamaño carta.
            </p>
        </div>
        <div class="btn-toolbar-actions d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary mr-2" id="btnLimpiarFiltros"
                title="Limpiar todos los filtros">
                <span class="material-icons align-middle" style="font-size: 1rem;">refresh</span>
                Limpiar
            </button>
            <button type="button" class="btn btn-teal mr-2" id="btnConsultar"
                title="Actualizar datos con los filtros seleccionados">
                <span class="material-icons align-middle" style="font-size: 1rem;">search</span>
                Consultar
            </button>
            <button type="button" class="btn btn-outline-success mr-2" id="btnExportarExcel"
                title="Exportar a Excel (CSV)">
                <span class="material-icons align-middle" style="font-size: 1rem;">table_view</span>
                Excel
            </button>
            <button type="button" class="btn btn-success" id="btnImprimir"
                title="Imprimir en tamaño carta (mitad de hoja)">
                <span class="material-icons align-middle" style="font-size: 1rem;">local_printshop</span>
                Imprimir
            </button>
        </div>
    </div>

    <!-- Filtros de Segmentación (desde plane) -->
    <div class="card-filtros-lonas no-print">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="font-weight-bold" style="font-size: 0.85rem; color: #2D3748;">
                <span class="material-icons text-teal align-middle mr-1"
                    style="font-size: 1.1rem; color: #00796B;">tune</span>
                Filtros de Segmentación (Plane)
            </span>
            <span class="badge badge-light text-muted font-weight-normal px-2 py-1"
                style="border: 1px solid #e2e8f0; font-size: 0.72rem;">
                Los selectores se van segmentando automáticamente
            </span>
        </div>

        <div class="row">
            <!-- 1. Finca -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                <label class="filter-label" for="selectFinca">1. Finca</label>
                <select id="selectFinca" class="form-control select2-filter" data-placeholder="-- Finca --">
                    <option value=""></option>
                    <?php foreach ($opciones['fincas'] as $f): ?>
                        <option value="<?php echo htmlspecialchars($f); ?>"><?php echo htmlspecialchars($f); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 2. Bloque -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                <label class="filter-label" for="selectBloque">2. Bloque</label>
                <select id="selectBloque" class="form-control select2-filter" data-placeholder="-- Bloque --">
                    <option value=""></option>
                    <?php foreach ($opciones['bloques'] as $b): ?>
                        <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars($b); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 3. Variedad -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                <label class="filter-label" for="selectVariedad">3. Variedad</label>
                <select id="selectVariedad" class="form-control select2-filter" data-placeholder="-- Variedad --">
                    <option value=""></option>
                    <?php foreach ($opciones['variedades'] as $v): ?>
                        <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 4. Semana Siembra (YYWW) -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                <label class="filter-label" for="selectSemanaSiembra">4. Sem. Siembra (YYWW)</label>
                <select id="selectSemanaSiembra" class="form-control select2-filter"
                    data-placeholder="-- Sem. Siembra --">
                    <option value=""></option>
                    <?php foreach ($opciones['semanas_siembra'] as $ss): ?>
                        <option value="<?php echo htmlspecialchars($ss); ?>"><?php echo htmlspecialchars($ss); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 5. Cosecha -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                <label class="filter-label" for="selectCosecha">5. Cosecha</label>
                <select id="selectCosecha" class="form-control select2-filter" data-placeholder="-- Cosecha --">
                    <option value=""></option>
                    <?php foreach ($opciones['cosechas'] as $c): ?>
                        <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- 6. Año ISO -->
            <div class="col-12 col-sm-6 col-md-4 col-lg-2 mb-2">
                <label class="filter-label" for="selectAnio">Año ISO</label>
                <select id="selectAnio" class="form-control select2-filter" data-placeholder="Año">
                    <?php foreach ($anios as $a): ?>
                        <option value="<?php echo $a; ?>" <?php echo $a === $anioDefault ? 'selected' : ''; ?>>
                            <?php echo $a; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- FORMATO PLANILLA (OCUPA LA MITAD DE LA HOJA CARTA) -->
    <div class="planilla-wrapper position-relative">
        <div class="loader-overlay" id="loaderLonas" style="display: none;">
            <div class="spinner-border spinner-border-teal" role="status"></div>
        </div>

        <!-- Encabezado Fiel a la Imagen -->
        <div class="planilla-header">
            <div class="finca-name" id="hdrFincaNombre">INVERPALMAS</div>
            <div class="form-title">PLANILLA PARA REGISTRO CORTE</div>
            <div class="doc-code">I CYM SIE PRC 028</div>
        </div>

        <!-- Campos de Información del Lote -->
        <div class="planilla-fields">
            <div class="planilla-field-row">
                <div class="planilla-field" style="flex: 1.1;">
                    <span class="field-label">BLOQUE:</span>
                    <span class="field-val" id="lblBloque"></span>
                </div>
                <div class="planilla-field" style="flex: 1;">
                    <span class="field-label">AÑO:</span>
                    <span class="field-val" id="lblAnio"><?php echo $anioDefault; ?></span>
                </div>
                <div class="planilla-field" style="flex: 1.3;">
                    <span class="field-label">No.MATAS:</span>
                    <span class="field-val" id="lblMatas"></span>
                </div>
            </div>
            <div class="planilla-field-row">
                <div class="planilla-field" style="flex: 1.5;">
                    <span class="field-label">VARIEDAD:</span>
                    <span class="field-val" id="lblVariedad"></span>
                </div>
                <div class="planilla-field" style="flex: 1.3;">
                    <span class="field-label">COSECHA:</span>
                    <span class="field-val" id="lblCosecha"></span>
                </div>
                <div class="planilla-field" style="flex: 1.2;">
                    <span class="field-label">SEM.SIEMBRA:</span>
                    <span class="field-val" id="lblSemSiembra"></span>
                </div>
            </div>
        </div>

        <!-- Tabla Planilla: 11 Columnas (SEM, LUN..DOM, TOTAL, F-MATA, ACM-FM) y 53 Semanas -->
        <table class="table-planilla" id="tablaPlanilla">
            <thead>
                <tr>
                    <th class="col-sem">SEM.</th>
                    <th class="col-dia">LUN</th>
                    <th class="col-dia">MAR</th>
                    <th class="col-dia">MIE</th>
                    <th class="col-dia">JUE</th>
                    <th class="col-dia">VIE</th>
                    <th class="col-dia">SAB</th>
                    <th class="col-dia">DOM</th>
                    <th class="col-tot">TOTAL</th>
                    <th class="col-fm">F-MATA</th>
                    <th class="col-acm">ACM-FM</th>
                </tr>
            </thead>
            <tbody id="tbodyPlanilla">
                <!-- 53 Semanas generadas dinámicamente -->
            </tbody>
        </table>
    </div>

</div>

<!-- Lógica JavaScript -->
<script>
    $(document).ready(function () {
        var actualizandoCascada = false;
        var plantasActuales = 0;

        // Inicializar Select2
        $('.select2-filter').select2({
            width: '100%',
            allowClear: true
        });

        // Formatear enteros con separador de miles
        function formatearEntero(num) {
            if (num === null || num === undefined || num === '') {
                return '';
            }
            return Number(num).toLocaleString('es-CO');
        }

        // Formatear decimales para F-MATA y ACM-FM (ej. 0.35)
        function formatearDecimal(num) {
            if (num === null || num === undefined || isNaN(num) || num === '') {
                return '';
            }
            return Number(num).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Obtener filtros actuales
        function obtenerFiltrosActuales() {
            return {
                finca: $('#selectFinca').val() || '',
                bloque: $('#selectBloque').val() || '',
                variedad: $('#selectVariedad').val() || '',
                semana_siembra: $('#selectSemanaSiembra').val() || '',
                cosecha: $('#selectCosecha').val() || '',
                año: $('#selectAnio').val() || '2026'
            };
        }

        // Cascada dinámica al cambiar cualquiera de los filtros
        $('#selectFinca, #selectBloque, #selectVariedad, #selectSemanaSiembra, #selectCosecha').on('change', function () {
            if (actualizandoCascada) return;
            actualizarCascadaFiltros();
        });

        $('#selectAnio').on('change', function () {
            cargarReporte();
        });

        $('#btnConsultar').on('click', function () {
            cargarReporte();
        });

        $('#btnImprimir').on('click', function () {
            window.print();
        });

        $('#btnLimpiarFiltros').on('click', function () {
            actualizandoCascada = true;
            $('#selectFinca').val(null);
            $('#selectBloque').val(null);
            $('#selectVariedad').val(null);
            $('#selectSemanaSiembra').val(null);
            $('#selectCosecha').val(null);
            $('.select2-filter').trigger('change.select2');
            actualizandoCascada = false;

            actualizarCascadaFiltros(function () {
                cargarReporte();
            });
        });

        // Exportar a Excel con las 11 columnas exactas
        $('#btnExportarExcel').on('click', function () {
            var sep = ';';
            var csv = [];

            var fincaNom = $('#hdrFincaNombre').text().trim();
            csv.push(['"' + fincaNom + '"'].join(sep));
            csv.push(['"PLANILLA PARA REGISTRO CORTE"'].join(sep));
            csv.push(['"I CYM SIE PRC 028"'].join(sep));
            csv.push('');
            csv.push(['"BLOQUE:"', '"' + $('#lblBloque').text() + '"', '"AÑO:"', '"' + $('#lblAnio').text() + '"', '"No.MATAS:"', '"' + $('#lblMatas').text() + '"'].join(sep));
            csv.push(['"VARIEDAD:"', '"' + $('#lblVariedad').text() + '"', '"COSECHA:"', '"' + $('#lblCosecha').text() + '"', '"SEM.SIEMBRA:"', '"' + $('#lblSemSiembra').text() + '"'].join(sep));
            csv.push('');

            // Cabeceras
            var headers = [];
            $('#tablaPlanilla thead th').each(function () {
                headers.push('"' + $(this).text().trim().replace(/"/g, '""') + '"');
            });
            csv.push(headers.join(sep));

            // Filas Semanas 1 a 53
            $('#tablaPlanilla tbody tr').each(function () {
                var row = [];
                $(this).find('td').each(function () {
                    var txt = $(this).text().trim().replace(/\s+/g, ' ');
                    row.push('"' + txt.replace(/"/g, '""') + '"');
                });
                csv.push(row.join(sep));
            });

            var csvContent = '\uFEFF' + csv.join('\r\n');
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);
            var variedadNom = $('#lblVariedad').text().trim().replace(/[^a-zA-Z0-9]/g, '_') || 'Lonas';
            var anioNom = $('#lblAnio').text().trim() || '2026';
            link.setAttribute('href', url);
            link.setAttribute('download', 'Planilla_Registro_Corte_' + variedadNom + '_' + anioNom + '.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        // Actualiza opciones de filtro en cascada desde `plane`
        function actualizarCascadaFiltros(callback) {
            var filtros = obtenerFiltrosActuales();
            filtros.accion = 'opciones';

            $.ajax({
                url: 'ajax/reporte_cosecha_lonas.php',
                method: 'GET',
                data: filtros,
                dataType: 'json'
            }).done(function (data) {
                actualizandoCascada = true;

                var mapa = [
                    { id: '#selectFinca', clave: 'fincas' },
                    { id: '#selectBloque', clave: 'bloques' },
                    { id: '#selectVariedad', clave: 'variedades' },
                    { id: '#selectSemanaSiembra', clave: 'semanas_siembra' },
                    { id: '#selectCosecha', clave: 'cosechas' }
                ];

                mapa.forEach(function (item) {
                    var $sel = $(item.id);
                    var valorActual = $sel.val();
                    var disponibles = (data[item.clave] || []).map(String);
                    var mantener = (disponibles.indexOf(String(valorActual)) !== -1) ? valorActual : '';

                    $sel.empty().append(new Option('', '', false, false));
                    disponibles.forEach(function (op) {
                        var isSelected = (op === mantener);
                        $sel.append(new Option(op, op, false, isSelected));
                    });

                    if (!mantener && disponibles.length === 1) {
                        mantener = disponibles[0];
                    }
                    $sel.val(mantener);
                    $sel.trigger('change.select2');
                });

                actualizandoCascada = false;
                if (typeof callback === 'function') callback();
            }).fail(function (xhr, status, error) {
                actualizandoCascada = false;
                console.error('Error en cascada de filtros:', error);
            });
        }

        // Consulta de datos de la matriz y del encabezado
        function cargarReporte() {
            var filtros = obtenerFiltrosActuales();
            filtros.accion = 'datos';

            $('#loaderLonas').fadeIn(80);

            $.ajax({
                url: 'ajax/reporte_cosecha_lonas.php',
                method: 'GET',
                data: filtros,
                dataType: 'json'
            }).done(function (response) {
                $('#loaderLonas').fadeOut(80);
                renderizarEncabezado(response.encabezado, filtros.año);
                renderizarMatriz(response.matriz, plantasActuales);
            }).fail(function (xhr, status, error) {
                $('#loaderLonas').fadeOut(80);
                console.error('Error cargando datos:', error);
            });
        }

        // Renderizar encabezado con nombre de finca, bloque, variedad, cosecha, año, etc.
        function renderizarEncabezado(enc, anio) {
            var finca = enc.finca || $('#selectFinca').val() || 'INVERPALMAS';
            var bloque = enc.bloque || $('#selectBloque').val() || '';
            var variedad = enc.variedad || $('#selectVariedad').val() || '';
            var semSiembra = enc.semana_siembra || $('#selectSemanaSiembra').val() || '';
            var cosecha = enc.cosecha || $('#selectCosecha').val() || '';
            plantasActuales = parseInt(enc.plantas, 10) || 0;

            // Nombre de finca en el título (ej. INVERPALMAS S.A.S o PALERMO S.A.S)
            var fincaLimpia = finca.toUpperCase().replace(/\s+(S\.A\.S\.?|SAS)$/i, '').trim();
            var nombreEmpresa = (fincaLimpia || 'INVERPALMAS');
            $('#hdrFincaNombre').text(nombreEmpresa);

            // Campos de información
            $('#lblBloque').text(bloque);
            $('#lblAnio').text(anio);
            $('#lblMatas').text(plantasActuales > 0 ? formatearEntero(plantasActuales) : '');
            $('#lblVariedad').text(variedad);
            $('#lblCosecha').text(cosecha);
            $('#lblSemSiembra').text(semSiembra);
        }

        // Renderizar las 53 semanas con 11 columnas: SEM, LUN..DOM, TOTAL, F-MATA, ACM-FM
        function renderizarMatriz(matriz, matas) {
            var $tbody = $('#tbodyPlanilla');
            $tbody.empty();

            if (!matriz || !matriz.semanas) {
                $tbody.html('<tr><td colspan="11" class="text-center py-2 text-muted">Sin datos.</td></tr>');
                return;
            }

            var acumFM = 0.0;
            var html = '';

            matriz.semanas.forEach(function (sem) {
                var semNum = sem.semana; // 1 a 53
                var totSem = sem.total_semana;
                var tieneDatos = (totSem !== null && totSem > 0);

                var fmStr = '';
                var acmStr = '';

                if (tieneDatos) {
                    var fm = (matas > 0) ? (totSem / matas) : 0;
                    acumFM += fm;
                    fmStr = formatearDecimal(fm);
                    acmStr = formatearDecimal(acumFM);
                }

                html += '<tr>';
                // 1. SEM. (solo el número)
                html += '<td class="cell-sem">' + semNum + '</td>';

                // 2 a 8. LUN a DOM
                for (var d = 1; d <= 7; d++) {
                    var diaInfo = sem.dias[d];
                    var val = (diaInfo && diaInfo.tallos !== null) ? diaInfo.tallos : null;

                    if (val !== null && val > 0) {
                        html += '<td class="cell-num">' + formatearEntero(val) + '</td>';
                    } else {
                        html += '<td class="cell-empty"></td>';
                    }
                }

                // 9. TOTAL (Semana)
                if (tieneDatos) {
                    html += '<td class="cell-tot">' + formatearEntero(totSem) + '</td>';
                } else {
                    html += '<td class="cell-empty"></td>';
                }

                // 10. F-MATA (Flor por mata semanal)
                if (tieneDatos && fmStr !== '') {
                    html += '<td class="cell-fm">' + fmStr + '</td>';
                } else {
                    html += '<td class="cell-empty"></td>';
                }

                // 11. ACM-FM (Acumulado Flor por mata)
                if (tieneDatos && acmStr !== '') {
                    html += '<td class="cell-acm">' + acmStr + '</td>';
                } else {
                    html += '<td class="cell-empty"></td>';
                }

                html += '</tr>';
            });

            $tbody.html(html);
        }

        // Carga inicial
        cargarReporte();
    });
</script>