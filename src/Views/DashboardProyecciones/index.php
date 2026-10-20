<style>
    /* ========================================================
       MODERN DASHBOARD PROYECCIONES - DESIGN SYSTEM & STYLES
       ======================================================== */
    :root {
        --dp-primary: #00796B;
        --dp-primary-dark: #004D40;
        --dp-primary-light: #E0F2F1;
        --dp-primary-border: #80CBC4;
        --dp-accent-blue: #2563EB;
        --dp-accent-purple: #7C3AED;
        --dp-accent-amber: #D97706;
        --dp-accent-rose: #E11D48;
        --dp-surface: #FFFFFF;
        --dp-bg: #F8FAFC;
        --dp-border: #E2E8F0;
        --dp-text-title: #0F172A;
        --dp-text-body: #334155;
        --dp-text-muted: #64748B;
        --dp-radius-lg: 16px;
        --dp-radius-md: 10px;
        --dp-radius-sm: 6px;
        --dp-shadow-card: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
        --dp-shadow-hover: 0 12px 30px -4px rgba(0, 121, 107, 0.12), 0 4px 10px -2px rgba(15, 23, 42, 0.04);
    }

    .dp-dashboard-wrapper {
        padding: 0.5rem 0 2.5rem;
        color: var(--dp-text-body);
        font-family: inherit;
    }

    /* --- HERO HEADER --- */
    .dp-hero-header {
        background: linear-gradient(135deg, #00796B 0%, #00564C 60%, #003D36 100%);
        border-radius: var(--dp-radius-lg);
        padding: 1.5rem 1.75rem;
        color: #FFFFFF;
        box-shadow: 0 10px 25px -5px rgba(0, 121, 107, 0.35);
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .dp-hero-header::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .dp-hero-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1.25rem;
        position: relative;
        z-index: 1;
    }
    .dp-hero-titles h1 {
        font-size: 1.5rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        margin: 0 0 0.35rem 0;
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .dp-hero-titles p {
        font-size: 0.88rem;
        color: rgba(255, 255, 255, 0.85);
        margin: 0;
        max-width: 650px;
        line-height: 1.45;
    }
    .dp-hero-actions {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
    }
    .dp-btn-hero {
        background: rgba(255, 255, 255, 0.15);
        color: #FFFFFF !important;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 999px;
        padding: 0.45rem 1rem;
        font-size: 0.84rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        backdrop-filter: blur(8px);
        transition: all 0.2s ease;
        text-decoration: none !important;
        cursor: pointer;
    }
    .dp-btn-hero:hover {
        background: rgba(255, 255, 255, 0.25);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-1px);
    }
    .dp-btn-hero.active {
        background: #FFFFFF;
        color: var(--dp-primary-dark) !important;
        border-color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    .dp-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(16, 185, 129, 0.2);
        border: 1px solid rgba(52, 211, 153, 0.4);
        color: #A7F3D0;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }
    .dp-live-dot {
        width: 7px;
        height: 7px;
        background-color: #34D399;
        border-radius: 50%;
        box-shadow: 0 0 8px #34D399;
        animation: dpPulse 2s infinite;
    }
    @keyframes dpPulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.2); }
    }

    /* --- FILTER PANEL --- */
    .dp-filter-card {
        background: var(--dp-surface);
        border: 1px solid var(--dp-border);
        border-radius: var(--dp-radius-lg);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--dp-shadow-card);
        display: none;
        animation: dpSlideDown 0.25s ease-out;
    }
    @keyframes dpSlideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .dp-filter-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--dp-text-title);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dp-filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        align-items: end;
    }
    .dp-filter-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--dp-text-muted);
        margin-bottom: 0.35rem;
    }
    .dp-filter-input {
        width: 100%;
        border-radius: var(--dp-radius-md) !important;
        border: 1px solid var(--dp-border) !important;
        padding: 0.45rem 0.75rem !important;
        font-size: 0.85rem !important;
        color: var(--dp-text-body) !important;
        background-color: #FFFFFF !important;
        transition: all 0.2s ease;
    }
    .dp-filter-input:focus {
        border-color: var(--dp-primary) !important;
        box-shadow: 0 0 0 3px rgba(0, 121, 107, 0.15) !important;
        outline: none;
    }
    .dp-filter-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .dp-quick-ranges {
        display: flex;
        gap: 0.35rem;
        margin-top: 0.75rem;
        flex-wrap: wrap;
    }
    .dp-btn-chip {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.65rem;
        border-radius: 999px;
        background: #F1F5F9;
        border: 1px solid #E2E8F0;
        color: var(--dp-text-muted);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .dp-btn-chip:hover, .dp-btn-chip.active {
        background: var(--dp-primary-light);
        color: var(--dp-primary-dark);
        border-color: var(--dp-primary-border);
    }
    .dp-active-chips-bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }
    .dp-active-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #EEF7F5;
        border: 1px solid #BCE2DB;
        color: var(--dp-primary-dark);
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
    }
    .dp-active-tag .material-icons {
        font-size: 0.95rem;
        cursor: pointer;
        color: #5A7E77;
    }
    .dp-active-tag .material-icons:hover {
        color: #DC2626;
    }

    /* --- KPI SUMMARY METRICS (4 CARDS) --- */
    .dp-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .dp-kpi-card {
        background: var(--dp-surface);
        border: 1px solid var(--dp-border);
        border-radius: var(--dp-radius-lg);
        padding: 1.25rem 1.4rem;
        box-shadow: var(--dp-shadow-card);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .dp-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--dp-shadow-hover);
        border-color: #CBD5E1;
    }
    .dp-kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: transparent;
        transition: background 0.2s ease;
    }
    .dp-kpi-card.kpi-camas::before { background: linear-gradient(90deg, #00796B, #10B981); }
    .dp-kpi-card.kpi-plantas::before { background: linear-gradient(90deg, #0284C7, #38BDF8); }
    .dp-kpi-card.kpi-variedades::before { background: linear-gradient(90deg, #7C3AED, #A855F7); }
    .dp-kpi-card.kpi-fincas::before { background: linear-gradient(90deg, #D97706, #FBBF24); }

    .dp-kpi-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }
    .dp-kpi-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--dp-text-muted);
    }
    .dp-kpi-icon-wrap {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }
    .dp-kpi-card:hover .dp-kpi-icon-wrap {
        transform: scale(1.08);
    }
    .kpi-camas .dp-kpi-icon-wrap { background: #E6F4F1; color: #00796B; }
    .kpi-plantas .dp-kpi-icon-wrap { background: #E0F2FE; color: #0284C7; }
    .kpi-variedades .dp-kpi-icon-wrap { background: #F3E8FF; color: #7C3AED; }
    .kpi-fincas .dp-kpi-icon-wrap { background: #FEF3C7; color: #D97706; }

    .dp-kpi-value {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--dp-text-title);
        letter-spacing: -0.02em;
        line-height: 1.1;
        margin-bottom: 0.35rem;
        font-variant-numeric: tabular-nums;
    }
    .dp-kpi-meta {
        font-size: 0.78rem;
        color: var(--dp-text-muted);
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .dp-kpi-progress {
        height: 6px;
        border-radius: 999px;
        background: #F1F5F9;
        overflow: hidden;
        margin-top: 0.6rem;
        display: flex;
    }
    .dp-kpi-progress-bar {
        height: 100%;
        transition: width 0.6s ease;
    }

    /* --- SECTION CARDS (TABLES & CHARTS) --- */
    .dp-section-card {
        background: var(--dp-surface);
        border: 1px solid var(--dp-border);
        border-radius: var(--dp-radius-lg);
        box-shadow: var(--dp-shadow-card);
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: all 0.2s ease;
    }
    .dp-section-card:hover {
        box-shadow: 0 8px 24px -4px rgba(15, 23, 42, 0.08);
    }
    .dp-section-header {
        background: #FFFFFF;
        border-bottom: 1px solid var(--dp-border);
        padding: 0.9rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .dp-section-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }
    .dp-section-title-wrap .material-icons {
        color: var(--dp-primary);
        font-size: 1.35rem;
    }
    .dp-section-title {
        font-size: 0.96rem;
        font-weight: 700;
        color: var(--dp-text-title);
        margin: 0;
    }
    .dp-section-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .dp-table-search {
        position: relative;
        min-width: 180px;
    }
    .dp-table-search input {
        border-radius: 999px !important;
        border: 1px solid var(--dp-border) !important;
        padding: 0.35rem 0.85rem 0.35rem 2rem !important;
        font-size: 0.8rem !important;
        width: 100%;
        background: #F8FAFC !important;
        transition: all 0.2s ease;
    }
    .dp-table-search input:focus {
        background: #FFFFFF !important;
        border-color: var(--dp-primary) !important;
        box-shadow: 0 0 0 2px rgba(0, 121, 107, 0.12) !important;
        outline: none;
    }
    .dp-table-search .material-icons {
        position: absolute;
        left: 0.65rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1rem;
        color: var(--dp-text-muted);
        pointer-events: none;
    }
    .dp-section-body {
        padding: 1.1rem;
    }

    /* --- MODERN TABLES --- */
    .dp-table-wrapper {
        max-height: 390px;
        overflow-y: auto;
        overflow-x: auto;
        border-radius: var(--dp-radius-md);
        border: 1px solid var(--dp-border);
    }
    .dp-table-wrapper::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .dp-table-wrapper::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 999px;
    }
    .dp-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .dp-table thead th {
        position: sticky;
        top: 0;
        background: #F8FAFC !important;
        color: var(--dp-text-muted) !important;
        font-size: 0.74rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 0.75rem 0.9rem !important;
        border-bottom: 2px solid var(--dp-border) !important;
        z-index: 2;
        white-space: nowrap;
    }
    .dp-table tbody td {
        padding: 0.65rem 0.9rem !important;
        font-size: 0.84rem !important;
        color: var(--dp-text-body) !important;
        border-bottom: 1px solid #F1F5F9 !important;
        vertical-align: middle !important;
    }
    .dp-table tbody tr:hover td {
        background-color: #F8FBFA !important;
    }
    .dp-table tfoot td {
        position: sticky;
        bottom: 0;
        background: #F8FAFC !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        color: var(--dp-text-title) !important;
        border-top: 2px solid var(--dp-border) !important;
        padding: 0.75rem 0.9rem !important;
        z-index: 2;
    }

    /* --- TABLE PILLS & BARS --- */
    .dp-pill-finca {
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .dp-pill-finca.inverpalmas {
        background: #E6F4F1;
        color: #00796B;
        border: 1px solid #BCE2DB;
    }
    .dp-pill-finca.palermo {
        background: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #BFDBFE;
    }
    .dp-pill-finca.other {
        background: #F1F5F9;
        color: #475569;
        border: 1px solid #E2E8F0;
    }
    .dp-pill-flor {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--dp-text-title);
    }
    .dp-badge-camas {
        display: inline-block;
        min-width: 3.2rem;
        padding: 0.22rem 0.6rem;
        border-radius: 999px;
        background: #E6F4F1;
        color: #00796B;
        font-weight: 700;
        font-size: 0.85rem;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }
    .dp-percent-bar-container {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 110px;
    }
    .dp-percent-bar-bg {
        flex: 1;
        height: 6px;
        background: #F1F5F9;
        border-radius: 999px;
        overflow: hidden;
    }
    .dp-percent-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #00796B, #10B981);
        border-radius: 999px;
        transition: width 0.4s ease;
    }
    .dp-percent-bar-label {
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--dp-text-muted);
        width: 38px;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    /* --- CHART CARD ENHANCEMENTS --- */
    .dp-chart-wrap {
        position: relative;
        height: 380px;
        padding: 0.5rem 0.5rem 0;
    }
    .dp-chart-stat-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.76rem;
        font-weight: 600;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        background: #F8FAFC;
        border: 1px solid var(--dp-border);
        color: var(--dp-text-muted);
    }
    .dp-chart-stat-chip strong {
        color: var(--dp-primary-dark);
    }
    .dp-btn-chart-toggle {
        border: 1px solid var(--dp-border);
        background: #FFFFFF;
        color: var(--dp-text-muted);
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .dp-btn-chart-toggle:hover, .dp-btn-chart-toggle.active {
        background: var(--dp-primary);
        color: #FFFFFF;
        border-color: var(--dp-primary);
    }

    /* --- SHIMMER / SKELETON LOADER --- */
    .dp-skeleton {
        background: linear-gradient(90deg, #F1F5F9 25%, #E2E8F0 50%, #F1F5F9 75%);
        background-size: 200% 100%;
        animation: dpShimmer 1.5s infinite;
        border-radius: 6px;
        display: inline-block;
    }
    @keyframes dpShimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    .dp-skeleton-text {
        height: 1rem;
        width: 80%;
    }
    .dp-skeleton-num {
        height: 2.2rem;
        width: 60%;
    }

    /* --- ROTATING REFRESH ICON --- */
    .dp-rotating {
        animation: dpRotate 0.8s linear infinite;
    }
    @keyframes dpRotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    /* Responsive tweaks */
    @media (max-width: 768px) {
        .dp-hero-content {
            flex-direction: column;
            align-items: flex-start;
        }
        .dp-hero-actions {
            width: 100%;
            justify-content: flex-start;
        }
        .dp-section-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .dp-section-actions {
            width: 100%;
            justify-content: space-between;
        }
        .dp-table-search {
            width: 100%;
        }
        .dp-chart-wrap {
            height: 300px;
        }
    }
</style>

<div class="dp-dashboard-wrapper container-fluid">
    <!-- ==========================================
         1. HERO HEADER SECTION
         ========================================== -->
    <div class="dp-hero-header">
        <div class="dp-hero-content">
            <div class="dp-hero-titles">
                <h1>
                    <span class="material-icons" style="font-size: 1.8rem;">dashboard</span>
                    Dashboard de Camas y Proyecciones
                </h1>
                <p>
                    Monitoreo en tiempo real de siembras, densidad poblacional y curvas fenológicas por edad en fincas de cultivo Inverpalmas.
                </p>
            </div>
            <div class="dp-hero-actions">
                <span class="dp-live-badge">
                    <span class="dp-live-dot"></span>
                    <span id="dpLiveStatusText">EN LÍNEA</span>
                </span>
                <button type="button" class="dp-btn-hero" id="dpBtnToggleFilters" title="Filtrar por finca, flor o fechas">
                    <span class="material-icons" style="font-size: 1.05rem;">tune</span>
                    Filtros
                    <span class="badge badge-light ml-1 text-dark d-none" id="dpFilterCounterBadge">0</span>
                </button>
                <button type="button" class="dp-btn-hero" id="dpBtnRefresh" title="Actualizar datos ahora">
                    <span class="material-icons" id="dpRefreshIcon" style="font-size: 1.05rem;">sync</span>
                    Actualizar
                </button>
                <button type="button" class="dp-btn-hero" onclick="window.print();" title="Imprimir o exportar a PDF">
                    <span class="material-icons" style="font-size: 1.05rem;">print</span>
                    Imprimir
                </button>
            </div>
        </div>
    </div>

    <!-- ==========================================
         2. COLLAPSIBLE FILTER PANEL
         ========================================== -->
    <div class="dp-filter-card" id="dpFilterPanel">
        <div class="dp-filter-title">
            <div class="d-flex align-items-center">
                <span class="material-icons mr-2 text-muted" style="font-size: 1.2rem;">filter_alt</span>
                <span>Filtros Operativos de Siembras</span>
            </div>
            <button type="button" class="close text-muted" id="dpBtnCloseFilters" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="dpFilterForm">
            <div class="dp-filter-grid">
                <div class="dp-filter-group">
                    <label for="dpSelectFinca">Finca</label>
                    <select class="form-control dp-filter-input" id="dpSelectFinca" name="finca">
                        <option value="">Todas las fincas</option>
                    </select>
                </div>
                <div class="dp-filter-group">
                    <label for="dpSelectFlor">Flor / Especie</label>
                    <select class="form-control dp-filter-input" id="dpSelectFlor" name="producto">
                        <option value="">Todas las flores</option>
                    </select>
                </div>
                <div class="dp-filter-group">
                    <label for="dpInputFechaDesde">Fecha Siembra Desde</label>
                    <input type="date" class="form-control dp-filter-input" id="dpInputFechaDesde" name="fecha_desde">
                </div>
                <div class="dp-filter-group">
                    <label for="dpInputFechaHasta">Fecha Siembra Hasta</label>
                    <input type="date" class="form-control dp-filter-input" id="dpInputFechaHasta" name="fecha_hasta">
                </div>
                <div class="dp-filter-actions">
                    <button type="submit" class="btn btn-success d-inline-flex align-items-center" id="dpBtnApplyFilters">
                        <span class="material-icons mr-1" style="font-size: 1rem;">check_circle</span>
                        Aplicar
                    </button>
                    <button type="button" class="btn btn-outline-secondary d-inline-flex align-items-center" id="dpBtnResetFilters">
                        <span class="material-icons mr-1" style="font-size: 1rem;">restart_alt</span>
                        Restablecer
                    </button>
                </div>
            </div>
            <div class="dp-quick-ranges">
                <span class="text-muted small align-self-center mr-1 font-weight-bold">Accesos rápidos:</span>
                <button type="button" class="dp-btn-chip active" data-range="365">Último Año (365d)</button>
                <button type="button" class="dp-btn-chip" data-range="180">Últimos 6 Meses</button>
                <button type="button" class="dp-btn-chip" data-range="ytd">Año en Curso</button>
                <button type="button" class="dp-btn-chip" data-range="all">Histórico Completo</button>
            </div>
        </form>
    </div>

    <!-- ACTIVE FILTER CHIPS (Visible when any filter is set) -->
    <div class="dp-active-chips-bar d-none" id="dpActiveChipsBar">
        <span class="text-muted small font-weight-bold mr-1">Filtros aplicados:</span>
        <div id="dpActiveChipsList" class="d-inline-flex flex-wrap" style="gap: 0.4rem;"></div>
        <button type="button" class="btn btn-link btn-sm text-danger p-0 ml-2" id="dpClearAllChips" style="font-size: 0.78rem;">
            Limpiar todo
        </button>
    </div>

    <!-- ==========================================
         3. EXECUTIVE KPI SUMMARY METRICS (4 CARDS)
         ========================================== -->
    <div class="dp-kpi-grid">
        <!-- KPI 1: CAMAS -->
        <div class="dp-kpi-card kpi-camas">
            <div class="dp-kpi-top">
                <span class="dp-kpi-label">Total Camas Sembradas</span>
                <div class="dp-kpi-icon-wrap">
                    <span class="material-icons">grid_view</span>
                </div>
            </div>
            <div class="dp-kpi-value" id="dpKpiCamasVal">
                <span class="dp-skeleton dp-skeleton-num"></span>
            </div>
            <div class="dp-kpi-meta" id="dpKpiCamasMeta">
                <span class="material-icons" style="font-size: 0.95rem; color: #10B981;">check_circle</span>
                <span>Área activa presupuestada</span>
            </div>
        </div>

        <!-- KPI 2: PLANTAS -->
        <div class="dp-kpi-card kpi-plantas">
            <div class="dp-kpi-top">
                <span class="dp-kpi-label">Población de Plantas</span>
                <div class="dp-kpi-icon-wrap">
                    <span class="material-icons">local_florist</span>
                </div>
            </div>
            <div class="dp-kpi-value" id="dpKpiPlantasVal">
                <span class="dp-skeleton dp-skeleton-num"></span>
            </div>
            <div class="dp-kpi-meta" id="dpKpiPlantasMeta">
                <span class="material-icons" style="font-size: 0.95rem; color: #0284C7;">grass</span>
                <span>Densidad: ~960 plantas / cama</span>
            </div>
        </div>

        <!-- KPI 3: PARTICIPACIÓN FLOR -->
        <div class="dp-kpi-card kpi-variedades">
            <div class="dp-kpi-top">
                <span class="dp-kpi-label">Mix Flor Principal</span>
                <div class="dp-kpi-icon-wrap">
                    <span class="material-icons">pie_chart</span>
                </div>
            </div>
            <div class="dp-kpi-value" id="dpKpiMixVal" style="font-size: 1.45rem;">
                <span class="dp-skeleton dp-skeleton-num"></span>
            </div>
            <div class="dp-kpi-progress" id="dpKpiMixProgress">
                <div class="dp-kpi-progress-bar" style="width: 70%; background: #7C3AED;"></div>
                <div class="dp-kpi-progress-bar" style="width: 30%; background: #EC4899;"></div>
            </div>
            <div class="dp-kpi-meta mt-2" id="dpKpiMixMeta">
                <span>Clavel vs Miniclavel</span>
            </div>
        </div>

        <!-- KPI 4: FINCAS -->
        <div class="dp-kpi-card kpi-fincas">
            <div class="dp-kpi-top">
                <span class="dp-kpi-label">Fincas en Producción</span>
                <div class="dp-kpi-icon-wrap">
                    <span class="material-icons">domain</span>
                </div>
            </div>
            <div class="dp-kpi-value" id="dpKpiFincasVal">
                <span class="dp-skeleton dp-skeleton-num"></span>
            </div>
            <div class="dp-kpi-meta" id="dpKpiFincasMeta">
                <span>Inverpalmas &amp; Palermo activas</span>
            </div>
        </div>
    </div>

    <!-- ==========================================
         4. DISTRIBUTION TABLES ROW (7 col / 5 col)
         ========================================== -->
    <div class="row">
        <!-- TABLA 1: CAMAS POR FINCA Y FLOR -->
        <div class="col-lg-7 mb-4">
            <div class="dp-section-card h-100">
                <div class="dp-section-header">
                    <div class="dp-section-title-wrap">
                        <span class="material-icons">table_chart</span>
                        <div>
                            <h2 class="dp-section-title">Camas por Finca y Flor</h2>
                            <small class="text-muted" id="dpSubtitleFincaFlor">Detalle cruzado de lotes y camas</small>
                        </div>
                    </div>
                    <div class="dp-section-actions">
                        <div class="dp-table-search">
                            <span class="material-icons">search</span>
                            <input type="text" id="dpSearchFincaFlor" placeholder="Filtrar fila..." autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="dp-section-body">
                    <div class="dp-table-wrapper">
                        <table class="table table-sm dp-table mb-0">
                            <thead>
                                <tr>
                                    <th>Finca</th>
                                    <th>Flor</th>
                                    <th class="text-right">Plantas</th>
                                    <th class="text-right">Camas</th>
                                    <th style="min-width: 120px;">% Part.</th>
                                </tr>
                            </thead>
                            <tbody id="dpTablaFincaFlor">
                                <tr><td colspan="5" class="text-center text-muted py-4"><span class="dp-skeleton dp-skeleton-text"></span></td></tr>
                            </tbody>
                            <tfoot id="dpFootFincaFlor" class="d-none">
                                <tr>
                                    <td colspan="2">TOTAL CONSOLIDADO</td>
                                    <td class="text-right" id="dpFootTotalPlantas">-</td>
                                    <td class="text-right" id="dpFootTotalCamas">-</td>
                                    <td>100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABLA 2: CAMAS POR FLOR (AGREGADO) -->
        <div class="col-lg-5 mb-4">
            <div class="dp-section-card h-100">
                <div class="dp-section-header">
                    <div class="dp-section-title-wrap">
                        <span class="material-icons">local_florist</span>
                        <div>
                            <h2 class="dp-section-title">Consolidado por Flor</h2>
                            <small class="text-muted">Totalizado por variedad agrícola</small>
                        </div>
                    </div>
                    <div class="dp-section-actions">
                        <span class="badge badge-light border text-muted px-2 py-1" id="dpCountFloresBadge">0 flores</span>
                    </div>
                </div>
                <div class="dp-section-body">
                    <div class="dp-table-wrapper">
                        <table class="table table-sm dp-table mb-0">
                            <thead>
                                <tr>
                                    <th>Flor</th>
                                    <th class="text-right">Plantas</th>
                                    <th class="text-right">Camas</th>
                                    <th style="min-width: 100px;">% Part.</th>
                                </tr>
                            </thead>
                            <tbody id="dpTablaFlor">
                                <tr><td colspan="4" class="text-center text-muted py-4"><span class="dp-skeleton dp-skeleton-text"></span></td></tr>
                            </tbody>
                            <tfoot id="dpFootFlor" class="d-none">
                                <tr>
                                    <td>TOTAL</td>
                                    <td class="text-right" id="dpFootFlorPlantas">-</td>
                                    <td class="text-right" id="dpFootFlorCamas">-</td>
                                    <td>100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         5. PHENOLOGICAL AGE CURVES (CHARTS)
         ========================================== -->
    <div class="row">
        <!-- CHART 1: CLAVEL -->
        <div class="col-lg-6 mb-4">
            <div class="dp-section-card">
                <div class="dp-section-header">
                    <div class="dp-section-title-wrap">
                        <span class="material-icons" style="color: #00796B;">show_chart</span>
                        <div>
                            <h2 class="dp-section-title">CLAVEL: Camas por Edad</h2>
                            <small class="text-muted">Distribución de semanas por finca</small>
                        </div>
                    </div>
                    <div class="dp-section-actions">
                        <span class="dp-chart-stat-chip" id="dpClavelPeakChip" title="Semana con mayor cantidad de camas">
                            <span class="material-icons text-warning" style="font-size: 0.95rem;">star</span>
                            <span>Pico: <strong id="dpClavelPeakText">-</strong></span>
                        </span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="dp-btn-chart-toggle active" data-chart="clavel" data-type="line" title="Ver como curva continua">Línea</button>
                            <button type="button" class="dp-btn-chart-toggle" data-chart="clavel" data-type="bar" title="Ver como barras">Barras</button>
                        </div>
                    </div>
                </div>
                <div class="dp-section-body">
                    <div class="dp-chart-wrap">
                        <canvas id="dpChartEdadClavel"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- CHART 2: MINICLAVEL -->
        <div class="col-lg-6 mb-4">
            <div class="dp-section-card">
                <div class="dp-section-header">
                    <div class="dp-section-title-wrap">
                        <span class="material-icons" style="color: #2563EB;">show_chart</span>
                        <div>
                            <h2 class="dp-section-title">MINICLAVEL: Camas por Edad</h2>
                            <small class="text-muted">Distribución de semanas por finca</small>
                        </div>
                    </div>
                    <div class="dp-section-actions">
                        <span class="dp-chart-stat-chip" id="dpMiniclavelPeakChip" title="Semana con mayor cantidad de camas">
                            <span class="material-icons text-warning" style="font-size: 0.95rem;">star</span>
                            <span>Pico: <strong id="dpMiniclavelPeakText">-</strong></span>
                        </span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="dp-btn-chart-toggle active" data-chart="miniclavel" data-type="line" title="Ver como curva continua">Línea</button>
                            <button type="button" class="dp-btn-chart-toggle" data-chart="miniclavel" data-type="bar" title="Ver como barras">Barras</button>
                        </div>
                    </div>
                </div>
                <div class="dp-section-body">
                    <div class="dp-chart-wrap">
                        <canvas id="dpChartEdadMiniclavel"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/scripts/dashboard_proyecciones.js?v=<?php echo time(); ?>"></script>
