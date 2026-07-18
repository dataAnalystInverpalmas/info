(function () {
    var FLORES = ['CLA', 'CM0', 'ROC', 'ROS'];
    var FLOR_COLORS = {
        CLA: '#0d6efd',
        CM0: '#198754',
        ROC: '#dc3545',
        ROS: '#fd7e14'
    };

    var chartPlantasFlorInstance = null;
    var chartEdadesInstance = null;
    var chartVariedadesInstance = null;

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
        if (desde && !desde.value) { desde.value = '2025-12-29'; }
        if (hasta && !hasta.value) { hasta.value = getLastWeekSunday(); }
    }

    function getFilters() {
        return {
            finca: (document.getElementById('dpFiltroFinca').value || '').trim(),
            fecha_desde: (document.getElementById('dpFechaDesde').value || '').trim(),
            fecha_hasta: (document.getElementById('dpFechaHasta').value || '').trim()
        };
    }

    function numberFormat(value) {
        var n = Number(value);
        if (!isFinite(n)) {
            return String(value);
        }
        return n.toLocaleString('es-CO', { maximumFractionDigits: 0 });
    }

    function subCard(label, value, cssClass) {
        return '<div class="dp-sub-card">'
            + '<div class="dp-sub-label">' + label + '</div>'
            + '<div class="dp-sub-value ' + cssClass + '">' + numberFormat(value) + '</div>'
            + '</div>';
    }

    function renderFlorCards(florCards) {
        var container = document.getElementById('dpFlorCards');
        if (!container) {
            return;
        }

        var html = '';
        FLORES.forEach(function (flor) {
            var data = (florCards && florCards[flor]) ? florCards[flor] : {};
            var re  = Number(data.RE)  || 0;
            var pt  = Number(data.PT)  || 0;
            var aj  = Number(data.AJ)  || 0;
            var diffREAJ = re - aj;
            var diffREPT = re - pt;
            var color = FLOR_COLORS[flor] || '#6c757d';

            html += '<div class="col-md-3 col-sm-6 mb-3">';
            html += '<div class="dp-flor-card">';
            html += '<div class="dp-flor-card-header" style="background:' + color + '">' + flor + '</div>';
            html += '<div class="dp-flor-card-body">';
            html += subCard('Tallos RE (Real)',         re,       'dp-neutral');
            html += subCard('Tallos PT (Presupuesto)',  pt,       'dp-neutral');
            html += subCard('Tallos AJ (Ajuste)',       aj,       'dp-neutral');
            html += '<div class="dp-divider"></div>';
            html += subCard('Diferencia RE &minus; AJ', diffREAJ, diffREAJ >= 0 ? 'dp-positive' : 'dp-negative');
            html += subCard('Diferencia RE &minus; PT', diffREPT, diffREPT >= 0 ? 'dp-positive' : 'dp-negative');
            html += '</div></div></div>';
        });

        container.innerHTML = html;
    }

    function showCardsLoading() {
        var container = document.getElementById('dpFlorCards');
        if (container) {
            container.innerHTML = '<div class="col-12 text-center text-muted py-5">'
                + '<span class="spinner-border spinner-border-sm me-2"></span> Cargando...</div>';
        }
    }

    function updateCharts(resp) {
        // --- 1. Gráfico Plantas Sembradas por Flor ---
        var ctxPlantasFlor = document.getElementById('chartPlantasFlor');
        if (ctxPlantasFlor && resp.plantasPorFlor) {
            if (chartPlantasFlorInstance) { chartPlantasFlorInstance.destroy(); }
            chartPlantasFlorInstance = new Chart(ctxPlantasFlor, {
                type: 'bar',
                data: {
                    labels: resp.plantasPorFlor.labels || [],
                    datasets: [{
                        label: 'Plantas Sembradas',
                        data: resp.plantasPorFlor.data || [],
                        backgroundColor: '#00796B',
                        borderRadius: 6,
                        maxBarThickness: 45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + numberFormat(context.raw) + ' plantas';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#EDF2F7' },
                            ticks: {
                                callback: function(value) { return numberFormat(value); }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // --- 2. Gráfico Edades y Cantidad de Plantas ---
        var ctxEdades = document.getElementById('chartEdades');
        if (ctxEdades && resp.edadesYPlantas) {
            if (chartEdadesInstance) { chartEdadesInstance.destroy(); }
            chartEdadesInstance = new Chart(ctxEdades, {
                type: 'bar',
                data: {
                    labels: resp.edadesYPlantas.labels || [],
                    datasets: [{
                        label: 'Densidad de Plantas',
                        data: resp.edadesYPlantas.data || [],
                        backgroundColor: '#4E8098',
                        borderRadius: 6,
                        maxBarThickness: 45
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + numberFormat(context.raw) + ' plantas';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#EDF2F7' },
                            ticks: {
                                callback: function(value) { return numberFormat(value); }
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // --- 3. Gráfico Distribución por Color (%) ---
        var ctxVariedades = document.getElementById('chartVariedades');
        var dataDistribucion = resp.distribucionColor || resp.distribucionVariedad;
        if (ctxVariedades && dataDistribucion) {
            if (chartVariedadesInstance) { chartVariedadesInstance.destroy(); }
            
            var totalCount = (dataDistribucion.data || []).reduce(function(a, b) { return a + b; }, 0);

            chartVariedadesInstance = new Chart(ctxVariedades, {
                type: 'doughnut',
                data: {
                    labels: dataDistribucion.labels || [],
                    datasets: [{
                        data: dataDistribucion.data || [],
                        backgroundColor: [
                            '#FF6384',
                            '#36A2EB',
                            '#FFCE56',
                            '#4BC0C0',
                            '#9966FF',
                            '#FF9F40'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                padding: 10,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var val = context.raw;
                                    var pct = totalCount > 0 ? ((val / totalCount) * 100).toFixed(1) : 0;
                                    return ' ' + context.label + ': ' + numberFormat(val) + ' (' + pct + '%)';
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }
    }

    function loadDashboard() {
        showCardsLoading();
        var filters = getFilters();

        $.ajax({
            url: '/ajax/dashboard_proyecciones.php',
            method: 'GET',
            data: filters,
            dataType: 'json'
        }).done(function (resp) {
            if (!resp || resp.ok !== true) {
                var msg = (resp && resp.message) ? resp.message : 'No se pudo cargar el dashboard';
                document.getElementById('dpFlorCards').innerHTML =
                    '<div class="col-12"><div class="alert alert-warning">' + msg + '</div></div>';
                return;
            }

            renderFlorCards(resp.florCards || {});
            updateCharts(resp);

        }).fail(function () {
            document.getElementById('dpFlorCards').innerHTML =
                '<div class="col-12"><div class="alert alert-danger">Error al conectar con el servidor.</div></div>';
        });
    }

    $(document).ready(function () {
        setDefaultDates();

        $('#dpBtnAplicarFiltros').on('click', function () {
            loadDashboard();
        });

        $('#dpFiltroFinca, #dpFechaDesde, #dpFechaHasta').on('keypress', function (e) {
            if (e.key === 'Enter') {
                loadDashboard();
            }
        });

        loadDashboard();
    });
})();
