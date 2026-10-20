/**
 * Dashboard de Camas y Proyecciones - Inverpalmas
 * Modern UI/UX Interactive Controller
 */
(function () {
    'use strict';

    // State
    var chartEdadClavel = null;
    var chartEdadMiniclavel = null;
    var clavelChartType = 'line';
    var miniclavelChartType = 'line';
    var cachedData = null;
    var activeFilters = {
        finca: '',
        producto: '',
        fecha_desde: '',
        fecha_hasta: '',
        sin_rango_fechas: false
    };
    var isCatalogsLoaded = false;

    // Helpers
    function integerFormat(value) {
        var n = Number(value);
        if (!isFinite(n)) {
            return String(value || 0);
        }
        return n.toLocaleString('es-CO', { maximumFractionDigits: 0 });
    }

    function compactFormat(num) {
        var n = Number(num);
        if (!isFinite(n)) return '0';
        if (n >= 1000000) return (n / 1000000).toFixed(1).replace('.0', '') + 'M';
        if (n >= 1000) return (n / 1000).toFixed(1).replace('.0', '') + 'k';
        return String(n);
    }

    function sanitize(str) {
        if (!str) return '';
        var temp = document.createElement('div');
        temp.textContent = str;
        return temp.innerHTML;
    }

    function getFincaClass(finca) {
        var f = String(finca || '').toUpperCase();
        if (f.indexOf('INVERPALMAS') !== -1) return 'inverpalmas';
        if (f.indexOf('PALERMO') !== -1) return 'palermo';
        return 'other';
    }

    function paletteAt(index, alpha) {
        var colors = [
            { border: '#00897B', fill: 'rgba(0, 137, 123, ' + (alpha || 0.12) + ')' }, // Inverpalmas Teal
            { border: '#2563EB', fill: 'rgba(37, 99, 235, ' + (alpha || 0.12) + ')' },  // Palermo Blue
            { border: '#7C3AED', fill: 'rgba(124, 58, 237, ' + (alpha || 0.12) + ')' }, // Purple
            { border: '#D97706', fill: 'rgba(217, 119, 6, ' + (alpha || 0.12) + ')' },  // Amber
            { border: '#DC2626', fill: 'rgba(220, 38, 38, ' + (alpha || 0.12) + ')' },  // Red
            { border: '#059669', fill: 'rgba(5, 150, 105, ' + (alpha || 0.12) + ')' },  // Emerald
            { border: '#DB2777', fill: 'rgba(219, 39, 119, ' + (alpha || 0.12) + ')' }  // Pink
        ];
        return colors[index % colors.length];
    }

    // --- KPI CARDS RENDERING ---
    function renderKpis(data) {
        var totales = data.totales || { plantas: 0, camas: 0 };
        var totalCamas = Number(totales.camas) || 0;
        var totalPlantas = Number(totales.plantas) || 0;

        // KPI 1: Camas
        var kpiCamasEl = document.getElementById('dpKpiCamasVal');
        if (kpiCamasEl) {
            kpiCamasEl.textContent = integerFormat(totalCamas) + ' camas';
        }

        // KPI 2: Plantas
        var kpiPlantasEl = document.getElementById('dpKpiPlantasVal');
        if (kpiPlantasEl) {
            kpiPlantasEl.textContent = integerFormat(totalPlantas);
        }

        // KPI 3: Flor Mix
        var flores = data.camasPorFlor || [];
        var clavelCamas = 0;
        var miniclavelCamas = 0;
        flores.forEach(function (f) {
            var name = String(f.flor || '').toUpperCase();
            var c = Number(f.camas) || 0;
            if (name === 'CLAVEL') clavelCamas += c;
            else if (name === 'MINICLAVEL') miniclavelCamas += c;
        });

        var kpiMixValEl = document.getElementById('dpKpiMixVal');
        var kpiMixMetaEl = document.getElementById('dpKpiMixMeta');
        var kpiMixProgressEl = document.getElementById('dpKpiMixProgress');

        if (totalCamas > 0) {
            var pctClavel = Math.round((clavelCamas / totalCamas) * 100);
            var pctMini = Math.round((miniclavelCamas / totalCamas) * 100);
            var pctOtros = 100 - pctClavel - pctMini;
            if (pctOtros < 0) pctOtros = 0;

            if (kpiMixValEl) {
                kpiMixValEl.innerHTML = '<span style="color:#7C3AED;">' + pctClavel + '%</span> Clavel <span class="text-muted" style="font-size:0.9rem;">/ ' + pctMini + '% Mini</span>';
            }
            if (kpiMixProgressEl) {
                kpiMixProgressEl.innerHTML =
                    '<div class="dp-kpi-progress-bar" style="width:' + pctClavel + '%; background:#7C3AED;" title="Clavel: ' + integerFormat(clavelCamas) + ' camas"></div>' +
                    '<div class="dp-kpi-progress-bar" style="width:' + pctMini + '%; background:#EC4899;" title="Miniclavel: ' + integerFormat(miniclavelCamas) + ' camas"></div>' +
                    (pctOtros > 0 ? '<div class="dp-kpi-progress-bar" style="width:' + pctOtros + '%; background:#94A3B8;" title="Otras: ' + pctOtros + '%"></div>' : '');
            }
            if (kpiMixMetaEl) {
                kpiMixMetaEl.innerHTML = '<span>' + integerFormat(clavelCamas) + ' camas Clavel • ' + integerFormat(miniclavelCamas) + ' Miniclavel</span>';
            }
        } else {
            if (kpiMixValEl) kpiMixValEl.textContent = 'Sin datos';
            if (kpiMixMetaEl) kpiMixMetaEl.textContent = 'Sin camas activas';
        }

        // KPI 4: Fincas
        var detalle = data.camasPorFincaFlor || [];
        var fincasMap = {};
        detalle.forEach(function (row) {
            var f = row.finca || 'SIN FINCA';
            if (!fincasMap[f]) fincasMap[f] = 0;
            fincasMap[f] += Number(row.camas) || 0;
        });

        var fincasList = Object.keys(fincasMap);
        var kpiFincasValEl = document.getElementById('dpKpiFincasVal');
        var kpiFincasMetaEl = document.getElementById('dpKpiFincasMeta');

        if (kpiFincasValEl) {
            kpiFincasValEl.textContent = fincasList.length + (fincasList.length === 1 ? ' Finca Activa' : ' Fincas Activas');
        }
        if (kpiFincasMetaEl) {
            var metaHtml = fincasList.map(function (f) {
                return '<span class="font-weight-bold">' + sanitize(f) + ':</span> ' + integerFormat(fincasMap[f]) + ' c.';
            }).join(' &nbsp;•&nbsp; ');
            kpiFincasMetaEl.innerHTML = metaHtml || 'Sin fincas registradas';
        }
    }

    // --- TABLA FINCA Y FLOR ---
    function renderRowsFincaFlor(rows, totalCamasGlobal) {
        var tbody = document.getElementById('dpTablaFincaFlor');
        var foot = document.getElementById('dpFootFincaFlor');
        var countEl = document.getElementById('dpSubtitleFincaFlor');
        if (!tbody) return;

        if (!rows || rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-4"><span class="material-icons d-block mb-1 text-muted" style="font-size:1.8rem;">inbox</span>No se encontraron registros para los filtros seleccionados</td></tr>';
            if (foot) foot.classList.add('d-none');
            if (countEl) countEl.textContent = '0 registros encontrados';
            return;
        }

        var totalPlantas = 0;
        var totalCamas = 0;
        var html = '';

        rows.forEach(function (row) {
            var camas = Number(row.camas) || 0;
            var plantas = Number(row.plantas) || 0;
            totalPlantas += plantas;
            totalCamas += camas;

            var pct = totalCamasGlobal > 0 ? ((camas / totalCamasGlobal) * 100).toFixed(1) : '0';
            var fClass = getFincaClass(row.finca);

            html += '<tr data-finca="' + sanitize(row.finca).toLowerCase() + '" data-flor="' + sanitize(row.flor).toLowerCase() + '">'
                + '<td><span class="dp-pill-finca ' + fClass + '">' + sanitize(row.finca || '-') + '</span></td>'
                + '<td><span class="dp-pill-flor"><span class="material-icons text-muted" style="font-size:0.95rem;">spa</span>' + sanitize(row.flor || '-') + '</span></td>'
                + '<td class="text-right">' + integerFormat(plantas) + '</td>'
                + '<td class="text-right"><span class="dp-badge-camas">' + integerFormat(camas) + '</span></td>'
                + '<td>'
                + '<div class="dp-percent-bar-container">'
                + '<div class="dp-percent-bar-bg"><div class="dp-percent-bar-fill" style="width:' + pct + '%;"></div></div>'
                + '<span class="dp-percent-bar-label">' + pct + '%</span>'
                + '</div>'
                + '</td>'
                + '</tr>';
        });

        tbody.innerHTML = html;
        if (countEl) countEl.textContent = rows.length + ' combinaciones de lote';

        if (foot) {
            foot.classList.remove('d-none');
            var footPlantas = document.getElementById('dpFootTotalPlantas');
            var footCamas = document.getElementById('dpFootTotalCamas');
            if (footPlantas) footPlantas.textContent = integerFormat(totalPlantas);
            if (footCamas) footCamas.textContent = integerFormat(totalCamas) + ' camas';
        }
    }

    // --- TABLA FLOR (CONSOLIDADO) ---
    function renderRowsFlor(rows, totalCamasGlobal) {
        var tbody = document.getElementById('dpTablaFlor');
        var foot = document.getElementById('dpFootFlor');
        var badge = document.getElementById('dpCountFloresBadge');
        if (!tbody) return;

        if (!rows || rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">Sin datos de flor disponibles</td></tr>';
            if (foot) foot.classList.add('d-none');
            if (badge) badge.textContent = '0 flores';
            return;
        }

        var totalPlantas = 0;
        var totalCamas = 0;
        var html = '';

        rows.forEach(function (row) {
            var camas = Number(row.camas) || 0;
            var plantas = Number(row.plantas) || 0;
            totalPlantas += plantas;
            totalCamas += camas;

            var pct = totalCamasGlobal > 0 ? ((camas / totalCamasGlobal) * 100).toFixed(1) : '0';

            html += '<tr>'
                + '<td><span class="dp-pill-flor"><span class="material-icons text-muted" style="font-size:0.95rem;">local_florist</span><strong>' + sanitize(row.flor || '-') + '</strong></span></td>'
                + '<td class="text-right">' + integerFormat(plantas) + '</td>'
                + '<td class="text-right"><span class="dp-badge-camas">' + integerFormat(camas) + '</span></td>'
                + '<td>'
                + '<div class="dp-percent-bar-container">'
                + '<div class="dp-percent-bar-bg"><div class="dp-percent-bar-fill" style="width:' + pct + '%;"></div></div>'
                + '<span class="dp-percent-bar-label">' + pct + '%</span>'
                + '</div>'
                + '</td>'
                + '</tr>';
        });

        tbody.innerHTML = html;
        if (badge) badge.textContent = rows.length + (rows.length === 1 ? ' variedad' : ' variedades');

        if (foot) {
            foot.classList.remove('d-none');
            var footPlantas = document.getElementById('dpFootFlorPlantas');
            var footCamas = document.getElementById('dpFootFlorCamas');
            if (footPlantas) footPlantas.textContent = integerFormat(totalPlantas);
            if (footCamas) footCamas.textContent = integerFormat(totalCamas) + ' camas';
        }
    }

    // --- CHART DATA NORMALIZATION ---
    function normalizeDatasets(payload, chartType) {
        var datasets = (payload && payload.datasets) ? payload.datasets : [];
        return datasets.map(function (dataset, idx) {
            var color = paletteAt(idx);
            var isBar = (chartType === 'bar');

            return {
                label: dataset.label || ('Finca ' + (idx + 1)),
                data: dataset.data || [],
                borderColor: color.border,
                backgroundColor: isBar ? color.border : color.fill,
                fill: !isBar,
                lineTension: 0.35,
                borderWidth: isBar ? 1 : 2.5,
                pointRadius: isBar ? 0 : 2.5,
                pointHoverRadius: isBar ? 0 : 5,
                pointBackgroundColor: color.border,
                pointBorderColor: '#FFFFFF',
                pointBorderWidth: 1.5
            };
        });
    }

    // --- CHART RENDERING ---
    function renderAgeChart(canvasId, chartRefName, payload, chartType) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) return;

        var labels = (payload && payload.labels) ? payload.labels : [];
        var datasets = normalizeDatasets(payload, chartType);

        if (chartRefName === 'clavel' && chartEdadClavel) {
            chartEdadClavel.destroy();
        }
        if (chartRefName === 'miniclavel' && chartEdadMiniclavel) {
            chartEdadMiniclavel.destroy();
        }

        var chartConfig;
        if (labels.length === 0 || datasets.length === 0) {
            chartConfig = {
                type: 'line',
                data: {
                    labels: [0],
                    datasets: [{
                        label: 'Sin registros',
                        data: [0],
                        borderColor: '#CBD5E1',
                        backgroundColor: '#F1F5F9'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { display: true, position: 'bottom' },
                    scales: {
                        xAxes: [{ scaleLabel: { display: true, labelString: 'Edad (semanas)' } }],
                        yAxes: [{ ticks: { beginAtZero: true }, scaleLabel: { display: true, labelString: 'Camas' } }]
                    }
                }
            };
        } else {
            chartConfig = {
                type: chartType || 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 16,
                            fontFamily: "'Inter', sans-serif",
                            fontSize: 12,
                            fontColor: '#475569'
                        }
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#0F172A',
                        titleFontFamily: "'Inter', sans-serif",
                        titleFontSize: 12,
                        titleFontColor: '#F8FAFC',
                        titleSpacing: 4,
                        bodyFontFamily: "'Inter', sans-serif",
                        bodyFontSize: 12,
                        bodyFontColor: '#FFFFFF',
                        bodySpacing: 6,
                        xPadding: 12,
                        yPadding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            title: function (items) {
                                if (!items || items.length === 0) return '';
                                return 'Semana ' + items[0].xLabel + ' de cultivo';
                            },
                            label: function (tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var val = integerFormat(tooltipItem.yLabel);
                                return '  ' + dataset.label + ': ' + val + ' camas';
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                fontFamily: "'Inter', sans-serif",
                                fontSize: 11,
                                fontColor: '#64748B',
                                maxTicksLimit: 20
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Edad de la planta (semanas desde siembra)',
                                fontFamily: "'Inter', sans-serif",
                                fontSize: 11,
                                fontColor: '#94A3B8'
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                fontFamily: "'Inter', sans-serif",
                                fontSize: 11,
                                fontColor: '#64748B',
                                callback: function (value) { return integerFormat(value); }
                            },
                            gridLines: {
                                color: '#F1F5F9',
                                zeroLineColor: '#E2E8F0',
                                drawBorder: false
                            },
                            scaleLabel: {
                                display: true,
                                labelString: 'Camas activas',
                                fontFamily: "'Inter', sans-serif",
                                fontSize: 11,
                                fontColor: '#94A3B8'
                            }
                        }]
                    }
                }
            };
        }

        var chart = new Chart(canvas.getContext('2d'), chartConfig);

        if (chartRefName === 'clavel') {
            chartEdadClavel = chart;
        } else {
            chartEdadMiniclavel = chart;
        }
    }

    // --- PEAKS DISPLAY ---
    function updatePeakBadges(resp) {
        var picoClavelEl = document.getElementById('dpClavelPeakText');
        var picoClavelChip = document.getElementById('dpClavelPeakChip');
        if (picoClavelEl && picoClavelChip) {
            if (resp.picoClavel && resp.picoClavel.camas > 0) {
                picoClavelEl.textContent = 'Sem ' + resp.picoClavel.semana + ' (' + integerFormat(resp.picoClavel.camas) + ' c. - ' + resp.picoClavel.finca + ')';
                picoClavelChip.style.display = 'inline-flex';
            } else {
                picoClavelChip.style.display = 'none';
            }
        }

        var picoMiniEl = document.getElementById('dpMiniclavelPeakText');
        var picoMiniChip = document.getElementById('dpMiniclavelPeakChip');
        if (picoMiniEl && picoMiniChip) {
            if (resp.picoMiniclavel && resp.picoMiniclavel.camas > 0) {
                picoMiniEl.textContent = 'Sem ' + resp.picoMiniclavel.semana + ' (' + integerFormat(resp.picoMiniclavel.camas) + ' c. - ' + resp.picoMiniclavel.finca + ')';
                picoMiniChip.style.display = 'inline-flex';
            } else {
                picoMiniChip.style.display = 'none';
            }
        }
    }

    // --- POPULATE FILTER CATALOGS ---
    function populateFilterOptions(resp) {
        if (isCatalogsLoaded) return;

        var fincas = resp.fincasDisponibles || [];
        var flores = resp.floresDisponibles || [];

        var selectFinca = document.getElementById('dpSelectFinca');
        if (selectFinca && fincas.length > 0) {
            fincas.forEach(function (f) {
                var opt = document.createElement('option');
                opt.value = f;
                opt.textContent = f;
                selectFinca.appendChild(opt);
            });
        }

        var selectFlor = document.getElementById('dpSelectFlor');
        if (selectFlor && flores.length > 0) {
            flores.forEach(function (fl) {
                var opt = document.createElement('option');
                opt.value = fl;
                opt.textContent = fl;
                selectFlor.appendChild(opt);
            });
        }

        // Default dates from backend
        if (resp.filters) {
            var fDesde = document.getElementById('dpInputFechaDesde');
            var fHasta = document.getElementById('dpInputFechaHasta');
            if (fDesde && resp.filters.fecha_desde) fDesde.value = resp.filters.fecha_desde;
            if (fHasta && resp.filters.fecha_hasta) fHasta.value = resp.filters.fecha_hasta;
        }

        isCatalogsLoaded = true;
    }

    // --- ACTIVE FILTER CHIPS ---
    function updateActiveChipsBar() {
        var container = document.getElementById('dpActiveChipsBar');
        var list = document.getElementById('dpActiveChipsList');
        var counterBadge = document.getElementById('dpFilterCounterBadge');
        if (!container || !list) return;

        var chips = [];

        if (activeFilters.finca) {
            chips.push({ key: 'finca', label: 'Finca: ' + activeFilters.finca });
        }
        if (activeFilters.producto) {
            chips.push({ key: 'producto', label: 'Flor: ' + activeFilters.producto });
        }
        if (activeFilters.sin_rango_fechas) {
            chips.push({ key: 'fechas', label: 'Fechas: Histórico Completo' });
        } else if (activeFilters.fecha_desde || activeFilters.fecha_hasta) {
            chips.push({ key: 'fechas', label: (activeFilters.fecha_desde || 'Inicio') + ' a ' + (activeFilters.fecha_hasta || 'Hoy') });
        }

        if (counterBadge) {
            if (chips.length > 0) {
                counterBadge.textContent = chips.length;
                counterBadge.classList.remove('d-none');
            } else {
                counterBadge.classList.add('d-none');
            }
        }

        if (chips.length === 0) {
            container.classList.add('d-none');
            list.innerHTML = '';
            return;
        }

        container.classList.remove('d-none');
        var html = '';
        chips.forEach(function (c) {
            html += '<span class="dp-active-tag">'
                + '<span>' + sanitize(c.label) + '</span>'
                + '<span class="material-icons dp-remove-chip" data-chip="' + c.key + '">close</span>'
                + '</span>';
        });
        list.innerHTML = html;

        // Bind remove chip events
        var closeBtns = list.querySelectorAll('.dp-remove-chip');
        closeBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var key = this.getAttribute('data-chip');
                if (key === 'finca') {
                    activeFilters.finca = '';
                    var sf = document.getElementById('dpSelectFinca');
                    if (sf) sf.value = '';
                } else if (key === 'producto') {
                    activeFilters.producto = '';
                    var sp = document.getElementById('dpSelectFlor');
                    if (sp) sp.value = '';
                } else if (key === 'fechas') {
                    activeFilters.sin_rango_fechas = false;
                    activeFilters.fecha_desde = '';
                    activeFilters.fecha_hasta = '';
                    var fd = document.getElementById('dpInputFechaDesde');
                    var fh = document.getElementById('dpInputFechaHasta');
                    if (fd) fd.value = '';
                    if (fh) fh.value = '';
                }
                loadDashboard(activeFilters);
            });
        });
    }

    // --- DATA FETCHER ---
    function loadDashboard(filters) {
        var refreshIcon = document.getElementById('dpRefreshIcon');
        if (refreshIcon) refreshIcon.classList.add('dp-rotating');

        var params = {};
        if (filters) {
            if (filters.finca) params.finca = filters.finca;
            if (filters.producto) params.producto = filters.producto;
            if (filters.sin_rango_fechas) {
                params.sin_rango_fechas = '1';
            } else {
                if (filters.fecha_desde) params.fecha_desde = filters.fecha_desde;
                if (filters.fecha_hasta) params.fecha_hasta = filters.fecha_hasta;
            }
        }

        $.ajax({
            url: '/ajax/dashboard_proyecciones.php',
            method: 'GET',
            data: params,
            dataType: 'json'
        }).done(function (resp) {
            if (!resp || resp.ok !== true) {
                return;
            }

            cachedData = resp;
            var totalCamasGlobal = (resp.totales && resp.totales.camas) ? Number(resp.totales.camas) : 0;

            renderKpis(resp);
            renderRowsFincaFlor(resp.camasPorFincaFlor || [], totalCamasGlobal);
            renderRowsFlor(resp.camasPorFlor || [], totalCamasGlobal);
            renderAgeChart('dpChartEdadClavel', 'clavel', resp.chartCamasEdadClavel || {}, clavelChartType);
            renderAgeChart('dpChartEdadMiniclavel', 'miniclavel', resp.chartCamasEdadMiniclavel || {}, miniclavelChartType);
            updatePeakBadges(resp);
            populateFilterOptions(resp);
            updateActiveChipsBar();

            // Update live status text
            var statusEl = document.getElementById('dpLiveStatusText');
            if (statusEl) {
                var now = new Date();
                var timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                statusEl.textContent = 'ACTUALIZADO ' + timeStr;
            }
        }).always(function () {
            if (refreshIcon) {
                setTimeout(function () {
                    refreshIcon.classList.remove('dp-rotating');
                }, 400);
            }
        });
    }

    // --- EVENT BINDINGS ---
    function bindEvents() {
        // Toggle Filter Drawer
        var btnToggleFilters = document.getElementById('dpBtnToggleFilters');
        var filterPanel = document.getElementById('dpFilterPanel');
        var btnCloseFilters = document.getElementById('dpBtnCloseFilters');

        if (btnToggleFilters && filterPanel) {
            btnToggleFilters.addEventListener('click', function () {
                var isVisible = filterPanel.style.display === 'block';
                filterPanel.style.display = isVisible ? 'none' : 'block';
                btnToggleFilters.classList.toggle('active', !isVisible);
            });
        }
        if (btnCloseFilters && filterPanel && btnToggleFilters) {
            btnCloseFilters.addEventListener('click', function () {
                filterPanel.style.display = 'none';
                btnToggleFilters.classList.remove('active');
            });
        }

        // Filter Form Submit
        var filterForm = document.getElementById('dpFilterForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var fincaVal = (document.getElementById('dpSelectFinca') || {}).value || '';
                var florVal = (document.getElementById('dpSelectFlor') || {}).value || '';
                var desdeVal = (document.getElementById('dpInputFechaDesde') || {}).value || '';
                var hastaVal = (document.getElementById('dpInputFechaHasta') || {}).value || '';

                activeFilters = {
                    finca: fincaVal,
                    producto: florVal,
                    fecha_desde: desdeVal,
                    fecha_hasta: hastaVal,
                    sin_rango_fechas: false
                };

                loadDashboard(activeFilters);
            });
        }

        // Reset Filters Button
        var btnReset = document.getElementById('dpBtnResetFilters');
        if (btnReset) {
            btnReset.addEventListener('click', function () {
                activeFilters = {
                    finca: '',
                    producto: '',
                    fecha_desde: '',
                    fecha_hasta: '',
                    sin_rango_fechas: false
                };
                var sf = document.getElementById('dpSelectFinca');
                var sp = document.getElementById('dpSelectFlor');
                var fd = document.getElementById('dpInputFechaDesde');
                var fh = document.getElementById('dpInputFechaHasta');
                if (sf) sf.value = '';
                if (sp) sp.value = '';
                if (fd) fd.value = '';
                if (fh) fh.value = '';

                // Reset range chips
                var chips = document.querySelectorAll('.dp-btn-chip');
                chips.forEach(function (c) { c.classList.remove('active'); });
                var defaultChip = document.querySelector('.dp-btn-chip[data-range="365"]');
                if (defaultChip) defaultChip.classList.add('active');

                loadDashboard(activeFilters);
            });
        }

        // Quick Date Range Chips
        var rangeChips = document.querySelectorAll('.dp-btn-chip');
        rangeChips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                rangeChips.forEach(function (c) { c.classList.remove('active'); });
                this.classList.add('active');

                var range = this.getAttribute('data-range');
                var fd = document.getElementById('dpInputFechaDesde');
                var fh = document.getElementById('dpInputFechaHasta');
                var now = new Date();
                var pad = function (n) { return (n < 10 ? '0' : '') + n; };
                var formatYmd = function (d) {
                    return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
                };

                if (range === 'all') {
                    activeFilters.sin_rango_fechas = true;
                    if (fd) fd.value = '';
                    if (fh) fh.value = '';
                } else if (range === 'ytd') {
                    activeFilters.sin_rango_fechas = false;
                    var startOfYear = new Date(now.getFullYear(), 0, 1);
                    if (fd) fd.value = formatYmd(startOfYear);
                    if (fh) fh.value = formatYmd(now);
                    activeFilters.fecha_desde = formatYmd(startOfYear);
                    activeFilters.fecha_hasta = formatYmd(now);
                } else {
                    var days = parseInt(range, 10) || 365;
                    activeFilters.sin_rango_fechas = false;
                    var past = new Date(now.getTime() - (days * 24 * 60 * 60 * 1000));
                    if (fd) fd.value = formatYmd(past);
                    if (fh) fh.value = formatYmd(now);
                    activeFilters.fecha_desde = formatYmd(past);
                    activeFilters.fecha_hasta = formatYmd(now);
                }

                loadDashboard(activeFilters);
            });
        });

        // Clear All Chips
        var clearAllChips = document.getElementById('dpClearAllChips');
        if (clearAllChips && btnReset) {
            clearAllChips.addEventListener('click', function () {
                btnReset.click();
            });
        }

        // Refresh Button
        var btnRefresh = document.getElementById('dpBtnRefresh');
        if (btnRefresh) {
            btnRefresh.addEventListener('click', function () {
                loadDashboard(activeFilters);
            });
        }

        // Instant Live Search in Table
        var searchInput = document.getElementById('dpSearchFincaFlor');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                var q = (this.value || '').toLowerCase().trim();
                var rows = document.querySelectorAll('#dpTablaFincaFlor tr[data-finca]');
                var visibleCount = 0;
                var visiblePlantas = 0;
                var visibleCamas = 0;

                rows.forEach(function (tr) {
                    var f = tr.getAttribute('data-finca') || '';
                    var fl = tr.getAttribute('data-flor') || '';
                    if (q === '' || f.indexOf(q) !== -1 || fl.indexOf(q) !== -1) {
                        tr.style.display = '';
                        visibleCount++;
                    } else {
                        tr.style.display = 'none';
                    }
                });

                var countEl = document.getElementById('dpSubtitleFincaFlor');
                if (countEl) {
                    countEl.textContent = q === '' ?
                        (rows.length + ' combinaciones de lote') :
                        (visibleCount + ' de ' + rows.length + ' filas encontradas');
                }
            });
        }

        // Chart Type Switchers (Line vs Bar)
        var chartToggles = document.querySelectorAll('.dp-btn-chart-toggle');
        chartToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                var chartName = this.getAttribute('data-chart');
                var type = this.getAttribute('data-type');

                var group = this.parentElement.querySelectorAll('.dp-btn-chart-toggle');
                group.forEach(function (b) { b.classList.remove('active'); });
                this.classList.add('active');

                if (chartName === 'clavel') {
                    clavelChartType = type;
                    if (cachedData) {
                        renderAgeChart('dpChartEdadClavel', 'clavel', cachedData.chartCamasEdadClavel || {}, clavelChartType);
                    }
                } else if (chartName === 'miniclavel') {
                    miniclavelChartType = type;
                    if (cachedData) {
                        renderAgeChart('dpChartEdadMiniclavel', 'miniclavel', cachedData.chartCamasEdadMiniclavel || {}, miniclavelChartType);
                    }
                }
            });
        });
    }

    // --- INITIALIZATION ---
    $(document).ready(function () {
        bindEvents();
        loadDashboard();
    });
})();
