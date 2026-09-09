(function () {
    var chartEdadClavel = null;
    var chartEdadMiniclavel = null;

    function getLastWeekSunday() {
        var today = new Date();
        var dow = today.getDay(); // 0=Sun, 1=Mon ... 6=Sat
        // Days since last Monday
        var daysSinceMonday = (dow === 0) ? 6 : dow - 1;
        // Previous Sunday = this Monday - 1 day
        var d = new Date(today);
        d.setDate(today.getDate() - daysSinceMonday - 1);
        return d.toISOString().slice(0, 10);
    }

    function setDefaultDates() {
        var desde = document.getElementById('dpFechaDesde');
        var hasta = document.getElementById('dpFechaHasta');
        if (desde && !desde.value) {
            var d = new Date();
            d.setFullYear(d.getFullYear() - 1);
            desde.value = d.toISOString().slice(0, 10);
        }
        if (hasta && !hasta.value) { hasta.value = getLastWeekSunday(); }
    }

    function getFilters() {
        return {
            finca: (document.getElementById('dpFiltroFinca').value || '').trim(),
            producto: (document.getElementById('dpFiltroProducto').value || '').trim(),
            fecha_desde: (document.getElementById('dpFechaDesde').value || '').trim(),
            fecha_hasta: (document.getElementById('dpFechaHasta').value || '').trim()
        };
    }

    function integerFormat(value) {
        var n = Number(value);
        if (!isFinite(n)) {
            return String(value);
        }
        return n.toLocaleString('es-CO', { maximumFractionDigits: 0 });
    }

    function decimalFormat(value, decimals) {
        var n = Number(value);
        if (!isFinite(n)) {
            return String(value);
        }
        return n.toLocaleString('es-CO', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        });
    }

    function setStatus(type, text) {
        var container = document.getElementById('dpEstadoCarga');
        if (!container) {
            return;
        }
        container.innerHTML = '<div class="alert alert-' + type + ' mb-0">' + text + '</div>';
    }

    function renderKpi(totales) {
        var el = document.getElementById('dpTotalCamas');
        if (!el) {
            return;
        }
        var camas = totales && totales.camas ? totales.camas : 0;
        el.textContent = decimalFormat(camas, 1);
    }

    function renderRowsFincaFlor(rows) {
        var tbody = document.getElementById('dpTablaFincaFlor');
        if (!tbody) {
            return;
        }

        if (!rows || rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Sin datos</td></tr>';
            return;
        }

        var html = '';
        rows.forEach(function (row) {
            html += '<tr>'
                + '<td>' + (row.finca || '-') + '</td>'
                + '<td>' + (row.flor || '-') + '</td>'
                + '<td class="text-end">' + integerFormat(row.plantas) + '</td>'
                + '<td class="text-end">' + decimalFormat(row.camas, 1) + '</td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    function renderRowsFlor(rows) {
        var tbody = document.getElementById('dpTablaFlor');
        if (!tbody) {
            return;
        }

        if (!rows || rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">Sin datos</td></tr>';
            return;
        }

        var html = '';
        rows.forEach(function (row) {
            html += '<tr>'
                + '<td>' + (row.flor || '-') + '</td>'
                + '<td class="text-end">' + integerFormat(row.plantas) + '</td>'
                + '<td class="text-end">' + decimalFormat(row.camas, 1) + '</td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    function paletteAt(index) {
        var colors = ['#2563eb', '#f97316', '#16a34a', '#7c3aed', '#dc2626', '#0891b2', '#ca8a04', '#334155'];
        return colors[index % colors.length];
    }

    function normalizeDatasets(payload) {
        var datasets = (payload && payload.datasets) ? payload.datasets : [];
        return datasets.map(function (dataset, idx) {
            var color = paletteAt(idx);
            return {
                label: dataset.label || ('Finca ' + (idx + 1)),
                data: dataset.data || [],
                borderColor: color,
                backgroundColor: color,
                fill: false,
                tension: 0.25,
                pointRadius: 2.5,
                pointHoverRadius: 4,
                borderWidth: 2
            };
        });
    }

    function renderAgeChart(canvasId, chartRefName, payload, title) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) {
            return;
        }

        var labels = (payload && payload.labels) ? payload.labels : [];
        var datasets = normalizeDatasets(payload);

        if (chartRefName === 'clavel' && chartEdadClavel) {
            chartEdadClavel.destroy();
        }
        if (chartRefName === 'miniclavel' && chartEdadMiniclavel) {
            chartEdadMiniclavel.destroy();
        }

        if (labels.length === 0 || datasets.length === 0) {
            var emptyChart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: [0],
                    datasets: [{
                        label: 'Sin datos',
                        data: [0],
                        borderColor: '#cbd5e1',
                        backgroundColor: '#cbd5e1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: true },
                        title: { display: true, text: title }
                    },
                    scales: {
                        x: { title: { display: true, text: 'Edad (semanas)' } },
                        y: { beginAtZero: true, title: { display: true, text: 'Camas' } }
                    }
                }
            });
            if (chartRefName === 'clavel') {
                chartEdadClavel = emptyChart;
            } else {
                chartEdadMiniclavel = emptyChart;
            }
            return;
        }

        var chart = new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: title
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ' ' + decimalFormat(context.raw, 1) + ' camas';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Edad (semanas)'
                        },
                        ticks: {
                            callback: function (value) {
                                var label = this.getLabelForValue(value);
                                return label;
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Camas'
                        },
                        ticks: {
                            callback: function (value) { return decimalFormat(value, 1); }
                        }
                    }
                }
            }
        });

        if (chartRefName === 'clavel') {
            chartEdadClavel = chart;
        } else {
            chartEdadMiniclavel = chart;
        }
    }

    function loadDashboard() {
        setStatus('info', 'Consultando camas sembradas...');
        var filters = getFilters();

        $.ajax({
            url: '/ajax/dashboard_proyecciones.php',
            method: 'GET',
            data: filters,
            dataType: 'json'
        }).done(function (resp) {
            if (!resp || resp.ok !== true) {
                var msg = (resp && resp.message) ? resp.message : 'No se pudo cargar el dashboard';
                setStatus('warning', msg);
                return;
            }

            renderKpi(resp.totales || {});
            renderRowsFincaFlor(resp.camasPorFincaFlor || []);
            renderRowsFlor(resp.camasPorFlor || []);
            renderAgeChart('dpChartEdadClavel', 'clavel', resp.chartCamasEdadClavel || {}, 'CLAVEL');
            renderAgeChart('dpChartEdadMiniclavel', 'miniclavel', resp.chartCamasEdadMiniclavel || {}, 'MINICLAVEL');
            setStatus('success', 'Información actualizada.');

        }).fail(function () {
            setStatus('danger', 'Error al conectar con el servidor.');
        });
    }

    $(document).ready(function () {
        setDefaultDates();

        $('#dpBtnAplicarFiltros').on('click', function () {
            loadDashboard();
        });

        $('#dpFiltroFinca, #dpFiltroProducto, #dpFechaDesde, #dpFechaHasta').on('keypress', function (e) {
            if (e.key === 'Enter') {
                loadDashboard();
            }
        });

        loadDashboard();
    });
})();
