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

    /* Barra lateral de filtros (mismo estilo que el sidebar principal) */
    .filtros-sidebar.offcanvas {
        --bs-offcanvas-width: 290px;
        background-color: #FFFFFF;
        border-left: 1px solid #E2E8F0;
        box-shadow: -2px 0 12px rgba(15, 23, 42, 0.06);
    }

    .filtros-sidebar .offcanvas-header {
        height: 60px;
        padding: 0 1.25rem;
        border-bottom: 1px solid #F1F5F9;
        flex-shrink: 0;
    }

    .filtros-sidebar .offcanvas-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #004D40;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filtros-sidebar .offcanvas-body {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        padding: 1.1rem 1.25rem;
    }

    .filtros-sidebar .filtros-footer {
        padding: 0.85rem 1.25rem;
        border-top: 1px solid #F1F5F9;
        background: #F8FAFC;
        flex-shrink: 0;
    }

    .filtros-sidebar .filter-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #4A5568;
        margin-bottom: 0.3rem;
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
        padding: 2.5px 2px;
        font-size: 0.69rem;
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
        height: 22px;
    }

    .table-planilla tbody td {
        border: 1px solid #000000;
        padding: 1.5px 3px;
        vertical-align: middle;
        line-height: 19px;
        height: 22px;
        font-variant-numeric: tabular-nums;
        font-size: 0.72rem;
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
       - Ocupa exactamente el largo de la hoja carta (279.4mm) en 1 sola página
       - Mantiene el encabezado unido a la tabla sin saltos de página huérfanos
       - Formato media carta (50% de ancho) en la mitad izquierda
       - 1CM de separación en el encabezado
       ======================================================== */
    @media print {
        @page {
            size: letter portrait;
            margin: 2.0mm 3.0mm 2.0mm 3.0mm;
        }

        html,
        body {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Ocultar elementos de UI y navegación */
        .fixed-top,
        .navbar,
        .navbar-custom,
        .reporte-lonas-bar,
        .card-filtros-lonas,
        .btn-toolbar-actions,
        .no-print,
        footer,
        header,
        nav,
        aside,
        #sidebar,
        #mostrar_loading,
        #mostrar_listo,
        #mostrar_error,
        .toast-container {
            display: none !important;
        }

        /* Contenedores sin paddings ni márgenes que empujen la tabla */
        section,
        .container-fluid,
        .reporte-lonas-container {
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
            border: none !important;
            box-shadow: none !important;
            overflow: visible !important;
            height: auto !important;
            min-height: 0 !important;
        }

        /* EXACTAMENTE LA MITAD DE LA HOJA CARTA (50% ANCHO) */
        .planilla-wrapper {
            width: 50% !important;
            max-width: 50% !important;
            float: left !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            box-shadow: none !important;
            box-sizing: border-box !important;
            display: block !important;
            height: auto !important;
            min-height: 0 !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
            break-after: avoid !important;
            page-break-after: avoid !important;
        }

        /* ENCABEZADO DE LA PLANILLA (1CM superior en encabezado) */
        .planilla-header {
            text-align: center !important;
            margin-top: 10mm !important;
            margin-bottom: 0.4mm !important;
            padding: 0 !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
            break-after: avoid !important;
            page-break-after: avoid !important;
        }

        .planilla-header .finca-name {
            font-size: 8.0pt !important;
            font-weight: 800 !important;
            color: #000000 !important;
            line-height: 1.1 !important;
            margin: 0 !important;
            letter-spacing: 0.04em !important;
        }

        .planilla-header .form-title {
            font-size: 7.0pt !important;
            font-weight: 800 !important;
            color: #000000 !important;
            line-height: 1.1 !important;
            margin: 0.2mm 0 !important;
            letter-spacing: 0.02em !important;
        }

        .planilla-header .doc-code {
            font-size: 5.8pt !important;
            font-weight: 700 !important;
            color: #000000 !important;
            line-height: 1 !important;
            margin: 0 !important;
            letter-spacing: 0.05em !important;
        }

        /* CAMPOS DE INFORMACIÓN */
        .planilla-fields {
            margin-top: 0.3mm !important;
            margin-bottom: 0.3mm !important;
            padding: 0 !important;
            font-size: 5.8pt !important;
            color: #000000 !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
            break-after: avoid !important;
            page-break-after: avoid !important;
        }

        .planilla-field-row {
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-end !important;
            margin-bottom: 0.25mm !important;
            gap: 3px !important;
        }

        .planilla-field {
            display: flex !important;
            align-items: flex-end !important;
            white-space: nowrap !important;
        }

        .planilla-field .field-label {
            font-size: 5.8pt !important;
            font-weight: 800 !important;
            margin-right: 2px !important;
            color: #000000 !important;
            line-height: 1 !important;
        }

        .planilla-field .field-val {
            font-size: 5.8pt !important;
            font-weight: 700 !important;
            border-bottom: 0.5pt solid #000000 !important;
            min-height: 2.2mm !important;
            padding: 0 2px !important;
            color: #000000 !important;
            line-height: 1.05 !important;
            text-align: center !important;
        }

        /* TABLA TIPO CUADRÍCULA: EVITAR SALTOS ANTES Y DENTRO */
        .table-planilla {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            margin: 0 !important;
            color: #000000 !important;
            break-before: avoid !important;
            page-break-before: avoid !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }

        .table-planilla thead {
            display: table-header-group !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
            break-after: avoid !important;
            page-break-after: avoid !important;
        }

        .table-planilla thead th {
            border: 0.5pt solid #000000 !important;
            padding: 0 1px !important;
            font-size: 5.9pt !important;
            font-weight: 800 !important;
            background: #ffffff !important;
            color: #000000 !important;
            height: 3.6mm !important;
            max-height: 3.6mm !important;
            line-height: 3.6mm !important;
            vertical-align: middle !important;
            text-align: center !important;
            box-sizing: border-box !important;
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
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }

        /* FILAS CALIBRADAS EXACTAMENTE PARA 53 SEMANAS EN 1 SOLA HOJA CARTA CON 1CM EN ENCABEZADO */
        .table-planilla tbody tr {
            height: 3.6mm !important;
            max-height: 3.6mm !important;
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }

        .table-planilla tbody td {
            border: 0.5pt solid #000000 !important;
            padding: 0 1px !important;
            font-size: 5.9pt !important;
            color: #000000 !important;
            line-height: 3.6mm !important;
            height: 3.6mm !important;
            max-height: 3.6mm !important;
            vertical-align: middle !important;
            overflow: hidden !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
        }

        .table-planilla tbody td.cell-sem {
            font-weight: 800 !important;
            text-align: center !important;
        }

        .table-planilla tbody td.cell-num {
            text-align: right !important;
        }

        .table-planilla tbody td.cell-tot {
            font-weight: 800 !important;
            text-align: right !important;
        }

        .table-planilla tbody td.cell-fm,
        .table-planilla tbody td.cell-acm {
            text-align: center !important;
            font-weight: 700 !important;
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
            <h1 id="reportTitle">
                <span class="material-icons" style="font-size: 1.35rem; color: #00796B;">description</span>
                Planilla para Registro Corte (Lonas)
            </h1>
            <p>
                Formato oficial (I CYM SIE PRC 028) para registro de corte diario. Ajustado a media página tamaño carta.
            </p>
        </div>
        <div class="btn-toolbar-actions d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary" id="btnExportarExcel"
                title="Exportar planilla a Excel (CSV)">
                <span class="material-icons align-middle" style="font-size: 1rem;">table_view</span>
                Excel
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btnImprimir" title="Imprimir planilla">
                <span class="material-icons align-middle" style="font-size: 1rem;">print</span>
                Imprimir
            </button>
            <button type="button" class="btn btn-teal" id="btnMostrarFiltros" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasFiltros" aria-controls="offcanvasFiltros" title="Mostrar filtros">
                <span class="material-icons align-middle" style="font-size: 1rem;">filter_list</span>
                Filtros
                <span class="badge rounded-pill bg-light text-dark ms-1 d-none" id="badgeFiltrosActivos">0</span>
            </button>
        </div>
    </div>

    <!-- Barra lateral vertical de filtros (alto completo) -->
    <div class="offcanvas offcanvas-end filtros-sidebar no-print" tabindex="-1" id="offcanvasFiltros"
        data-bs-scroll="true" data-bs-backdrop="false" aria-labelledby="offcanvasFiltrosLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasFiltrosLabel">
                <span class="material-icons" style="font-size: 1.2rem; color: #00796B;">filter_list</span>
                Filtros
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
        </div>
        <div class="offcanvas-body">
        <!-- 1. Finca -->
        <div>
            <label class="filter-label" for="selectFinca">1. Finca</label>
            <select id="selectFinca" class="form-control select2-filter" data-placeholder="-- Finca --">
                <option value=""></option>
                <?php foreach ($opciones['fincas'] as $f): ?>
                    <option value="<?php echo htmlspecialchars($f); ?>"><?php echo htmlspecialchars($f); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 2. Bloque -->
        <div>
            <label class="filter-label" for="selectBloque">2. Bloque</label>
            <select id="selectBloque" class="form-control select2-filter" data-placeholder="-- Bloque --">
                <option value=""></option>
                <?php foreach ($opciones['bloques'] as $b): ?>
                    <option value="<?php echo htmlspecialchars($b); ?>"><?php echo htmlspecialchars($b); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 3. Variedad -->
        <div>
            <label class="filter-label" for="selectVariedad">3. Variedad</label>
            <select id="selectVariedad" class="form-control select2-filter" data-placeholder="-- Variedad --">
                <option value=""></option>
                <?php foreach ($opciones['variedades'] as $v): ?>
                    <option value="<?php echo htmlspecialchars($v); ?>"><?php echo htmlspecialchars($v); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 4. Semana Siembra (YYWW) -->
        <div>
            <label class="filter-label" for="selectSemanaSiembra">4. Sem. Siembra (YYWW)</label>
            <select id="selectSemanaSiembra" class="form-control select2-filter" data-placeholder="-- Sem. Siembra --">
                <option value=""></option>
                <?php foreach ($opciones['semanas_siembra'] as $ss): ?>
                    <option value="<?php echo htmlspecialchars($ss); ?>"><?php echo htmlspecialchars($ss); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 5. Cosecha -->
        <div>
            <label class="filter-label" for="selectCosecha">5. Cosecha</label>
            <select id="selectCosecha" class="form-control select2-filter" data-placeholder="-- Cosecha --">
                <option value=""></option>
                <?php foreach ($opciones['cosechas'] as $c): ?>
                    <option value="<?php echo htmlspecialchars($c); ?>"><?php echo htmlspecialchars($c); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <!-- 6. Año ISO -->
        <div>
            <label class="filter-label" for="selectAnio">Año ISO</label>
            <select id="selectAnio" class="form-control select2-filter" data-placeholder="Año">
                <?php foreach ($anios as $a): ?>
                    <option value="<?php echo $a; ?>" <?php echo $a === $anioDefault ? 'selected' : ''; ?> >
                        <?php echo $a; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        </div>
        <div class="filtros-footer d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary flex-fill" id="btnLimpiarFiltros"
                title="Limpiar todos los filtros">
                <span class="material-icons align-middle" style="font-size: 1rem;">refresh</span>
                Limpiar
            </button>
            <button type="button" class="btn btn-teal flex-fill" id="btnConsultar"
                title="Actualizar datos con los filtros seleccionados">
                <span class="material-icons align-middle" style="font-size: 1rem;">search</span>
                Consultar
            </button>
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
        // dropdownParent: el offcanvas atrapa el foco, el buscador de Select2 debe vivir dentro
        $('.select2-filter').select2({
            width: '100%',
            allowClear: true,
            dropdownParent: $('#offcanvasFiltros')
        });

        // Contador de filtros activos en el botón
        function actualizarBadgeFiltros() {
            const activos = ['#selectFinca', '#selectBloque', '#selectVariedad', '#selectSemanaSiembra', '#selectCosecha']
                .filter((id) => $(id).val()).length;
            $('#badgeFiltrosActivos').text(activos).toggleClass('d-none', activos === 0);
        }
        $('.select2-filter').on('change', actualizarBadgeFiltros);

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
                actualizarBadgeFiltros();
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

            var tallosHistoricos = parseInt(matriz.tallos_historicos, 10) || 0;
            var acumFM = (matas > 0 && tallosHistoricos > 0) ? (tallosHistoricos / matas) : 0.0;
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