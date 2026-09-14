<style>
    .dp-section-card {
        border: 1px solid #dbe7e4;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        transition: box-shadow 0.2s ease;
    }
    .dp-section-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.09);
    }
    .dp-section-header {
        background: linear-gradient(135deg, #00796B 0%, #00695c 100%);
        color: #fff;
        padding: 0.7rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 700;
        font-size: 0.95rem;
    }
    .dp-section-header .material-icons {
        font-size: 1.15rem;
    }
    .dp-section-body {
        padding: 1rem;
    }
    .dp-table-wrapper {
        max-height: 360px;
        overflow: auto;
    }
    .dp-table thead th {
        position: sticky;
        top: 0;
        background: #f1f5f4;
        color: #345c56;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-bottom: 2px solid #dbe7e4;
        z-index: 2;
    }
    .dp-table td,
    .dp-table th {
        padding: 0.55rem 0.75rem;
        vertical-align: middle;
    }
    .dp-table td.text-end,
    .dp-table th.text-end {
        font-variant-numeric: tabular-nums;
    }
    .dp-table tbody tr:hover {
        background: #f6faf9;
    }
    .dp-badge-camas {
        display: inline-block;
        min-width: 3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        background: #e6f4f1;
        color: #00695c;
        font-weight: 700;
        font-size: 0.85rem;
    }
    .dp-chart-wrap {
        position: relative;
        height: 380px;
        padding: 0.25rem 0.5rem 0;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-lg-7 mb-3">
            <div class="dp-section-card h-100">
                <div class="dp-section-header">
                    <span class="material-icons">table_chart</span>
                    Camas por finca y flor
                </div>
                <div class="dp-section-body">
                    <div class="dp-table-wrapper">
                        <table class="table table-sm table-striped dp-table mb-0">
                            <thead>
                                <tr>
                                    <th>Finca</th>
                                    <th>Flor</th>
                                    <th class="text-end">Plantas</th>
                                    <th class="text-end">Camas</th>
                                </tr>
                            </thead>
                            <tbody id="dpTablaFincaFlor">
                                <tr><td colspan="4" class="text-center text-muted">Sin datos</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 mb-3">
            <div class="dp-section-card h-100">
                <div class="dp-section-header">
                    <span class="material-icons">local_florist</span>
                    Camas por flor (agregado)
                </div>
                <div class="dp-section-body">
                    <div class="dp-table-wrapper">
                        <table class="table table-sm table-striped dp-table mb-0">
                            <thead>
                                <tr>
                                    <th>Flor</th>
                                    <th class="text-end">Plantas</th>
                                    <th class="text-end">Camas</th>
                                </tr>
                            </thead>
                            <tbody id="dpTablaFlor">
                                <tr><td colspan="3" class="text-center text-muted">Sin datos</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="dp-section-card">
                <div class="dp-section-header">
                    <span class="material-icons">show_chart</span>
                    CLAVEL: camas por edad y finca
                </div>
                <div class="dp-section-body">
                    <div class="dp-chart-wrap">
                        <canvas id="dpChartEdadClavel"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="dp-section-card">
                <div class="dp-section-header">
                    <span class="material-icons">show_chart</span>
                    MINICLAVEL: camas por edad y finca
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

<script src="/scripts/dashboard_proyecciones.js?v=7"></script>
