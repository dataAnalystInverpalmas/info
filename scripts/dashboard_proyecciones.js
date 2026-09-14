(function () {
    var chartEdadClavel = null;
    var chartEdadMiniclavel = null;

    function integerFormat(value) {
        var n = Number(value);
        if (!isFinite(n)) {
            return String(value);
        }
        return n.toLocaleString('es-CO', { maximumFractionDigits: 0 });
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
                + '<td class="text-end"><span class="dp-badge-camas">' + integerFormat(row.camas) + '</span></td>'
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
                + '<td class="text-end"><span class="dp-badge-camas">' + integerFormat(row.camas) + '</span></td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    function paletteAt(index) {
        var colors = ['#00796B', '#f97316', '#2563eb', '#7c3aed', '#dc2626', '#0891b2', '#ca8a04', '#334155'];
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
                lineTension: 0.3,
                pointRadius: 2.5,
                pointHoverRadius: 4,
                borderWidth: 2.5
            };
        });
    }

    function renderAgeChart(canvasId, chartRefName, payload) {
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

        var chartConfig;
        if (labels.length === 0 || datasets.length === 0) {
            chartConfig = {
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
                    legend: { display: true },
                    scales: {
                        xAxes: [{ scaleLabel: { display: true, labelString: 'Edad (semanas)' } }],
                        yAxes: [{ ticks: { beginAtZero: true }, scaleLabel: { display: true, labelString: 'Camas' } }]
                    }
                }
            };
        } else {
            chartConfig = {
                type: 'line',
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
                        labels: { usePointStyle: true, boxWidth: 8, padding: 14 }
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                return ' ' + dataset.label + ': ' + integerFormat(tooltipItem.yLabel) + ' camas';
                            }
                        }
                    },
                    scales: {
                        xAxes: [{
                            gridLines: { display: false },
                            scaleLabel: { display: true, labelString: 'Edad (semanas)' }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function (value) { return integerFormat(value); }
                            },
                            gridLines: { color: '#eef2f1' },
                            scaleLabel: { display: true, labelString: 'Camas' }
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

    function loadDashboard() {
        $.ajax({
            url: '/ajax/dashboard_proyecciones.php',
            method: 'GET',
            dataType: 'json'
        }).done(function (resp) {
            if (!resp || resp.ok !== true) {
                return;
            }

            renderRowsFincaFlor(resp.camasPorFincaFlor || []);
            renderRowsFlor(resp.camasPorFlor || []);
            renderAgeChart('dpChartEdadClavel', 'clavel', resp.chartCamasEdadClavel || {});
            renderAgeChart('dpChartEdadMiniclavel', 'miniclavel', resp.chartCamasEdadMiniclavel || {});
        });
    }

    $(document).ready(function () {
        loadDashboard();
    });
})();
