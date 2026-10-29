var CurvasClavelProduccion = (function () {
    var chartFM = null;
    var chartCorte = null;
    var modo = 'edad'; // 'edad' | 'aass'
    var ultimoPayload = null;
    var actualizandoFiltros = false;

    var SELECTS_FILTRO = {
        finca: { id: '#ccpFiltroFinca', opciones: 'fincas' },
        bloque: { id: '#ccpFiltroBloque', opciones: 'bloques' },
        flor: { id: '#ccpFiltroFlor', opciones: 'flores' },
        variedad: { id: '#ccpFiltroVariedad', opciones: 'variedades' },
        cosecha: { id: '#ccpFiltroCosecha', opciones: 'cosechas' }
    };

    function getEndpointUrl(path) {
        if (window.APP_BASE_URL) {
            return window.APP_BASE_URL.replace(/\/+$/, '') + '/' + path.replace(/^\/+/, '');
        }
        var loc = window.location.pathname;
        var subDirMatch = loc.match(/^(.*\/info\/)/i);
        if (subDirMatch) {
            return subDirMatch[1] + path.replace(/^\/+/, '');
        }
        return path;
    }

    function init() {
        $('.select2').each(function () { $(this).select2({ width: '100%', placeholder: 'Todas', allowClear: true, dropdownParent: $(this).closest('.offcanvas, body') }); });

        $('#ccpFiltroFinca, #ccpFiltroBloque, #ccpFiltroFlor, #ccpFiltroVariedad, #ccpFiltroCosecha, #ccpFiltroCiclo')
            .on('change', onFiltroChange);

        $('#ccpFiltrosToggle').on('click', toggleSidebar);

        $('.ccp-modo-group button').on('click', function () {
            $('.ccp-modo-group button').removeClass('active');
            $(this).addClass('active');
            modo = $(this).data('modo');
            renderCharts();
        });

        cargarDatos();
    }

    function construirFiltros() {
        return {
            finca: $('#ccpFiltroFinca').val() || [],
            bloque: $('#ccpFiltroBloque').val() || [],
            flor: $('#ccpFiltroFlor').val() || [],
            variedad: $('#ccpFiltroVariedad').val() || [],
            cosecha: $('#ccpFiltroCosecha').val() || [],
            ciclo: $('#ccpFiltroCiclo').val() || []
        };
    }

    // Al cambiar un filtro, los demás selects se recalculan en cascada (solo
    // opciones compatibles con lo ya elegido); la recarga de datos sigue
    // disparándose con "Aplicar".
    function onFiltroChange() {
        if (actualizandoFiltros) { return; }
        actualizarOpcionesFiltros();
    }

    function actualizarOpcionesFiltros() {
        var filtros = construirFiltros();
        $.ajax({
            url: getEndpointUrl('ajax/curvas_clavel_produccion_filters.php'),
            method: 'GET',
            data: filtros,
            dataType: 'json'
        }).done(function (opciones) {
            actualizandoFiltros = true;
            Object.keys(SELECTS_FILTRO).forEach(function (clave) {
                var cfg = SELECTS_FILTRO[clave];
                var $select = $(cfg.id);
                var seleccionActual = $select.val() || [];
                var disponibles = (opciones[cfg.opciones] || []).map(String);
                var nuevaSeleccion = seleccionActual.filter(function (v) {
                    return disponibles.indexOf(String(v)) !== -1;
                });

                $select.empty();
                disponibles.forEach(function (valor) {
                    $select.append(new Option(valor, valor, false, nuevaSeleccion.indexOf(valor) !== -1));
                });
                $select.val(nuevaSeleccion).trigger('change');
            });
            actualizandoFiltros = false;
        }).fail(function () {
            actualizandoFiltros = false;
            console.error('No se pudieron actualizar las opciones de filtro');
        });
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

    function integerFormat(value) {
        var n = Number(value);
        if (!isFinite(n)) {
            return String(value);
        }
        return n.toLocaleString('es-CO', { maximumFractionDigits: 0 });
    }

    function setStatus(type, text) {
        var el = document.getElementById('ccpEstadoCarga');
        if (!el) {
            return;
        }
        el.className = 'small text-' + (type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'muted'));
        el.textContent = text;
    }

    function currentSeries() {
        if (!ultimoPayload) {
            return [];
        }
        return modo === 'edad' ? (ultimoPayload.porEdad || []) : (ultimoPayload.porAass || []);
    }

    function currentLabels(rows) {
        return rows.map(function (row) {
            return modo === 'edad' ? row.edad : row.aass;
        });
    }

    function renderTabla(rows) {
        var tbody = document.getElementById('ccpTablaDetalle');
        var colX = document.getElementById('ccpColX');
        if (colX) {
            colX.textContent = modo === 'edad' ? 'Edad' : 'Año-Semana';
        }
        if (!tbody) {
            return;
        }
        if (!rows || rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Sin datos</td></tr>';
            return;
        }
        var html = '';
        rows.forEach(function (row) {
            var x = modo === 'edad' ? row.edad : row.aass;
            html += '<tr>'
                + '<td>' + x + '</td>'
                + '<td class="text-end">' + decimalFormat(row.avg_f_m, 2) + '</td>'
                + '<td class="text-end">' + integerFormat(row.total_corte) + '</td>'
                + '<td class="text-end">' + integerFormat(row.matas) + '</td>'
                + '<td class="text-end">' + integerFormat(row.registros) + '</td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    function renderAcumuladoCiclo(rows) {
        var tbody = document.getElementById('ccpTablaAcumuladoCiclo');
        if (!tbody) {
            return;
        }
        if (!rows || rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Sin datos</td></tr>';
            return;
        }
        var html = '';
        rows.forEach(function (row) {
            html += '<tr>'
                + '<td>Ciclo ' + row.ciclo + '</td>'
                + '<td class="text-end">' + decimalFormat(row.acumulado_f_m, 2) + '</td>'
                + '<td class="text-end">' + integerFormat(row.total_corte) + '</td>'
                + '<td class="text-end">' + decimalFormat(row.promedio_matas, 2) + '</td>'
                + '<td class="text-end">' + integerFormat(row.total_registros) + '</td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    function buildLineChart(canvasId, existingChart, labels, data, label, color, title, decimals) {
        var canvas = document.getElementById(canvasId);
        if (!canvas) {
            return existingChart;
        }
        if (existingChart) {
            existingChart.destroy();
        }

        var xTitle = modo === 'edad' ? 'Edad (días)' : 'Año-Semana (yyww)';

        return new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    borderColor: color,
                    backgroundColor: color,
                    fill: false,
                    tension: 0.25,
                    pointRadius: 2.5,
                    pointHoverRadius: 4,
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: true, position: 'bottom' },
                title: { display: true, text: title },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem) {
                            return ' ' + decimalFormat(tooltipItem.yLabel, decimals);
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        scaleLabel: { display: true, labelString: xTitle }
                    }],
                    yAxes: [{
                        ticks: { beginAtZero: true },
                        scaleLabel: { display: true, labelString: label }
                    }]
                }
            }
        });
    }

    function renderCharts() {
        var rows = currentSeries();
        var labels = currentLabels(rows);
        var dataFM = rows.map(function (row) { return row.avg_f_m; });
        var dataCorte = rows.map(function (row) { return row.total_corte; });

        chartFM = buildLineChart('ccpChartFM', chartFM, labels, dataFM, 'Promedio F/M', '#2563eb', 'Promedio F/M', 2);
        chartCorte = buildLineChart('ccpChartCorte', chartCorte, labels, dataCorte, 'Total Corte (tallos)', '#16a34a', 'Total Corte (tallos)', 0);

        renderTabla(rows);
    }

    function cargarDatos() {
        setStatus('muted', 'Consultando curvas de producción...');
        $.ajax({
            url: getEndpointUrl('ajax/curvas_clavel_produccion.php'),
            method: 'GET',
            data: construirFiltros(),
            dataType: 'json'
        }).done(function (resp) {
            if (!resp || resp.ok !== true) {
                var msg = (resp && resp.message) ? resp.message : 'No se pudo cargar la información';
                setStatus('warning', msg);
                ultimoPayload = { porEdad: [], porAass: [], acumuladoCiclo: [] };
                renderCharts();
                renderAcumuladoCiclo([]);
                return;
            }
            ultimoPayload = resp;
            renderCharts();
            renderAcumuladoCiclo(resp.acumuladoCiclo || []);
            setStatus('muted', 'Información actualizada.');
        }).fail(function () {
            setStatus('danger', 'Error al conectar con el servidor.');
        });
    }

    function toggleSidebar() {
        var wrapper = document.getElementById('ccpFiltersWrapper');
        if (!wrapper) return;
        var btn = document.querySelector('[data-target="#ccpFiltersWrapper"]');
        var isExp = wrapper.classList.toggle('is-expanded');
        wrapper.classList.toggle('is-collapsed', !isExp);
        if (btn) {
            btn.classList.toggle('active', isExp);
            var textEl = btn.querySelector('.toggle-text');
            var iconEl = btn.querySelector('.toggle-icon');
            if (textEl) textEl.textContent = isExp ? 'Menos filtros' : 'Más filtros';
            if (iconEl) iconEl.textContent = isExp ? 'expand_less' : 'expand_more';
        }
        setTimeout(function () {
            if (chartFM) { chartFM.resize(); }
            if (chartCorte) { chartCorte.resize(); }
        }, 320);
    }

    return {
        init: init,
        aplicarFiltros: cargarDatos,
        toggleSidebar: toggleSidebar,
        limpiarFiltros: function () {
            actualizandoFiltros = true;
            $('#ccpFiltroFinca, #ccpFiltroBloque, #ccpFiltroFlor, #ccpFiltroVariedad, #ccpFiltroCosecha, #ccpFiltroCiclo').val(null).trigger('change');
            actualizandoFiltros = false;
            actualizarOpcionesFiltros();
            cargarDatos();
        }
    };
})();

document.addEventListener('DOMContentLoaded', CurvasClavelProduccion.init);
