var DemandaTika = (function () {
    var chartResumen = null;
    var agrupar = 'mercado';
    var ultimoPayload = null;
    var actualizandoFiltros = false;
    var defaultSemanaDesde = '';
    var defaultSemanaHasta = '';

    var PALETA = ['#2563eb', '#16a34a', '#f97316', '#9333ea', '#dc2626', '#0891b2', '#ca8a04', '#db2777'];

    // Los selects de semana (desde/hasta) son de valor único y se manejan
    // aparte; este mapa solo cubre los multi-select en cascada.
    var SELECTS_FILTRO = {
        flor: { id: '#dtkFiltroFlor', opciones: 'flores' },
        mercado: { id: '#dtkFiltroMercado', opciones: 'mercados' },
        submercado: { id: '#dtkFiltroSubmercado', opciones: 'submercados' },
        grupo_demanda: { id: '#dtkFiltroGrupo', opciones: 'gruposDemanda' },
        item: { id: '#dtkFiltroItem', opciones: 'items' }
    };

    var SELECTS_SEMANA = {
        semana_desde: { id: '#dtkFiltroSemanaDesde' },
        semana_hasta: { id: '#dtkFiltroSemanaHasta' }
    };

    function init() {
        $('.select2').select2({ width: '100%', placeholder: 'Todas', allowClear: true });
        $('.select2-single').select2({ width: '100%' });

        defaultSemanaDesde = $('#dtkFiltroSemanaDesde').val() || '';
        defaultSemanaHasta = $('#dtkFiltroSemanaHasta').val() || '';

        $('#dtkFiltroFlor, #dtkFiltroMercado, #dtkFiltroSubmercado, #dtkFiltroGrupo, #dtkFiltroItem, #dtkFiltroSemanaDesde, #dtkFiltroSemanaHasta')
            .on('change', onFiltroChange);

        $('#dtkFiltrosToggle').on('click', toggleSidebar);

        $('#dtkAgruparGroup button').on('click', function () {
            $('#dtkAgruparGroup button').removeClass('active');
            $(this).addClass('active');
            agrupar = $(this).data('agrupar');
            cargarDatos();
        });

        cargarDatos();
    }

    function construirFiltros() {
        return {
            flor: $('#dtkFiltroFlor').val() || [],
            mercado: $('#dtkFiltroMercado').val() || [],
            submercado: $('#dtkFiltroSubmercado').val() || [],
            grupo_demanda: $('#dtkFiltroGrupo').val() || [],
            item: $('#dtkFiltroItem').val() || [],
            semana_desde: $('#dtkFiltroSemanaDesde').val() || '',
            semana_hasta: $('#dtkFiltroSemanaHasta').val() || '',
            agrupar: agrupar
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
            url: '/ajax/demanda_tika_filters.php',
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

            var semanasDisponibles = (opciones.semanas || []).map(String);
            Object.keys(SELECTS_SEMANA).forEach(function (clave) {
                var $select = $(SELECTS_SEMANA[clave].id);
                var actual = $select.val();
                $select.empty();
                semanasDisponibles.forEach(function (valor) {
                    $select.append(new Option(valor, valor, false, valor === actual));
                });
                if (semanasDisponibles.indexOf(actual) === -1 && semanasDisponibles.length) {
                    actual = clave === 'semana_desde' ? semanasDisponibles[0] : semanasDisponibles[semanasDisponibles.length - 1];
                }
                $select.val(actual).trigger('change');
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
            minimumFractionDigits: 0,
            maximumFractionDigits: decimals
        });
    }

    function setStatus(type, text) {
        var el = document.getElementById('dtkEstadoCarga');
        if (!el) {
            return;
        }
        el.className = 'small text-' + (type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'muted'));
        el.textContent = text;
    }

    // Columnas fijas (izquierda) de cada tabla pivote: campo del payload + clase sticky.
    var COLUMNAS_PIVOTE_ITEM = [
        { campo: 'flor', clase: 'dtk-col-flor' },
        { campo: 'color', clase: 'dtk-col-color' },
        { campo: 'item', clase: 'dtk-col-item' }
    ];
    var COLUMNAS_RESUMEN_FC = [
        { campo: 'flor', clase: 'dtk-col-flor' },
        { campo: 'color', clase: 'dtk-col-color' }
    ];

    /**
     * Renderiza una tabla pivote genérica: columnas fijas a la izquierda
     * (definidas en `columnas`), una columna por cada semana y una columna
     * de total, con fila de totales en el pie. La usan tanto el pivote por
     * ítem como el resumen por flor y color.
     */
    function renderTablaPivote(idHead, idBody, idFoot, columnas, filas, semanas, totales, totalGeneral) {
        var $head = $(idHead);
        $head.find('th.dtk-semana').remove();
        semanas.forEach(function (semana) {
            $('<th class="dtk-semana text-end"></th>').text(semana).insertBefore($head.find('.dtk-col-total'));
        });

        var $body = $(idBody);
        if (filas.length === 0) {
            $body.html('<tr><td colspan="' + (columnas.length + 1 + semanas.length) + '" class="text-center text-muted">Sin datos</td></tr>');
        } else {
            var html = '';
            filas.forEach(function (fila) {
                html += '<tr>';
                columnas.forEach(function (col) {
                    html += '<td class="' + col.clase + '">' + fila[col.campo] + '</td>';
                });
                semanas.forEach(function (semana) {
                    var valor = fila.valores[semana];
                    html += '<td class="text-end">' + (valor === undefined ? '' : decimalFormat(valor, 2)) + '</td>';
                });
                html += '<td class="dtk-col-total text-end">' + decimalFormat(fila.total, 2) + '</td>'
                    + '</tr>';
            });
            $body.html(html);
        }

        var $foot = $(idFoot);
        $foot.find('td.dtk-semana').remove();
        var $footTotalCol = $foot.find('.dtk-col-total');
        semanas.forEach(function (semana) {
            $('<td class="dtk-semana text-end"></td>').text(decimalFormat(totales[semana] || 0, 2)).insertBefore($footTotalCol);
        });
        $footTotalCol.text(decimalFormat(totalGeneral || 0, 2));
    }

    function renderPivote(payload) {
        renderTablaPivote(
            '#dtkPivoteHead', '#dtkPivoteBody', '#dtkPivoteFoot',
            COLUMNAS_PIVOTE_ITEM,
            payload.filas || [],
            payload.semanas || [],
            payload.totalesPorSemana || {},
            payload.totalGeneral || 0
        );
    }

    function renderResumenFlorColor(payload) {
        renderTablaPivote(
            '#dtkResumenFCHead', '#dtkResumenFCBody', '#dtkResumenFCFoot',
            COLUMNAS_RESUMEN_FC,
            payload.resumenFlorColor || [],
            payload.semanas || [],
            payload.totalesPorSemana || {},
            payload.totalGeneral || 0
        );
    }

    function colorParaGrupo(idx) {
        return PALETA[idx % PALETA.length];
    }

    function renderChartResumen(payload) {
        var canvas = document.getElementById('dtkChartResumen');
        if (!canvas) {
            return;
        }
        if (chartResumen) {
            chartResumen.destroy();
            chartResumen = null;
        }

        var semanas = payload.semanas || [];
        var grupos = payload.gruposResumen || [];
        var resumen = payload.resumen || {};

        var datasets = grupos.map(function (grupo, idx) {
            var porSemana = {};
            (resumen[grupo] || []).forEach(function (row) { porSemana[row.semana] = row.cantidad; });
            var color = colorParaGrupo(idx);
            return {
                label: grupo,
                data: semanas.map(function (semana) { return porSemana[semana] !== undefined ? porSemana[semana] : null; }),
                borderColor: color,
                backgroundColor: color,
                fill: false,
                tension: 0.25,
                pointRadius: 2.5,
                pointHoverRadius: 4,
                borderWidth: 2,
                spanGaps: true
            };
        });

        chartResumen = new Chart(canvas, {
            type: 'line',
            data: { labels: semanas, datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: true, position: 'bottom' },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            return ' ' + dataset.label + ': ' + decimalFormat(tooltipItem.yLabel, 2);
                        }
                    }
                },
                scales: {
                    xAxes: [{ scaleLabel: { display: true, labelString: 'Semana' } }],
                    yAxes: [{ ticks: { beginAtZero: true }, scaleLabel: { display: true, labelString: 'Cantidad' } }]
                }
            }
        });
    }

    function cargarDatos() {
        setStatus('muted', 'Consultando demanda...');
        $.ajax({
            url: '/ajax/demanda_tika.php',
            method: 'GET',
            data: construirFiltros(),
            dataType: 'json'
        }).done(function (resp) {
            if (!resp || resp.ok !== true) {
                var msg = (resp && resp.message) ? resp.message : 'No se pudo cargar la información';
                setStatus('warning', msg);
                ultimoPayload = { semanas: [], filas: [], resumenFlorColor: [], totalesPorSemana: {}, totalGeneral: 0, gruposResumen: [], resumen: {} };
                renderPivote(ultimoPayload);
                renderResumenFlorColor(ultimoPayload);
                renderChartResumen(ultimoPayload);
                return;
            }
            ultimoPayload = resp;
            renderPivote(resp);
            renderResumenFlorColor(resp);
            renderChartResumen(resp);
            if ((resp.filas || []).length === 0) {
                setStatus('warning', 'No hay datos para los filtros seleccionados.');
            } else {
                setStatus('muted', 'Información actualizada.');
            }
        }).fail(function () {
            setStatus('danger', 'Error al conectar con el servidor.');
        });
    }

    function toggleSidebar() {
        var sidebarCol = document.getElementById('dtkSidebarCol');
        var mainCol = document.getElementById('dtkMainCol');
        var btnMostrar = document.getElementById('dtkBtnMostrarFiltros');
        var oculto = sidebarCol.classList.toggle('d-none');

        if (oculto) {
            mainCol.classList.remove('col-md-8', 'col-lg-9');
            mainCol.classList.add('col-12');
            btnMostrar.style.display = 'inline-flex';
        } else {
            mainCol.classList.remove('col-12');
            mainCol.classList.add('col-md-8', 'col-lg-9');
            btnMostrar.style.display = 'none';
        }

        setTimeout(function () {
            if (chartResumen) { chartResumen.resize(); }
        }, 200);
    }

    return {
        init: init,
        aplicarFiltros: cargarDatos,
        toggleSidebar: toggleSidebar,
        limpiarFiltros: function () {
            actualizandoFiltros = true;
            $('#dtkFiltroFlor, #dtkFiltroMercado, #dtkFiltroSubmercado, #dtkFiltroGrupo, #dtkFiltroItem').val(null).trigger('change');
            $('#dtkFiltroSemanaDesde').val(defaultSemanaDesde).trigger('change');
            $('#dtkFiltroSemanaHasta').val(defaultSemanaHasta).trigger('change');
            actualizandoFiltros = false;
            actualizarOpcionesFiltros();
            cargarDatos();
        }
    };
})();

document.addEventListener('DOMContentLoaded', DemandaTika.init);
