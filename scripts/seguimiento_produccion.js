var SeguimientoProduccion = (function () {
    var chartFM = null;
    var chartCorte = null;
    var modo = 'edad'; // 'edad' | 'aass'
    var ultimoPayload = null;
    var actualizandoFiltros = false;
    var defaultSemanaDesde = '';
    var defaultSemanaHasta = '';
    var defaultTipos = [];
    var MAX_TIPOS = 3;

    var TIPO_COLORES = { RE: '#2563eb', AJ: '#16a34a', IN: '#f97316' };
    var PALETA_RESERVA = ['#9333ea', '#dc2626', '#0891b2', '#ca8a04'];

    // 'tipo' se maneja con checkboxes simples (ver getTiposSeleccionados), no
    // por select2, por lo que no forma parte de este mapa de cascada.
    var SELECTS_FILTRO = {
        finca: { id: '#sgpFiltroFinca', opciones: 'fincas' },
        bloque: { id: '#sgpFiltroBloque', opciones: 'bloques' },
        flor: { id: '#sgpFiltroFlor', opciones: 'flores' },
        variedad: { id: '#sgpFiltroVariedad', opciones: 'variedades' },
        cosecha: { id: '#sgpFiltroCosecha', opciones: 'cosechas' }
    };

    function init() {
        $('.select2').select2({ width: '100%', placeholder: 'Todas', allowClear: true });

        defaultSemanaDesde = document.getElementById('sgpFiltroSemanaDesde').value;
        defaultSemanaHasta = document.getElementById('sgpFiltroSemanaHasta').value;
        defaultTipos = getTiposSeleccionados();

        $('#sgpFiltroFinca, #sgpFiltroBloque, #sgpFiltroFlor, #sgpFiltroVariedad, #sgpFiltroCosecha, #sgpFiltroCiclo')
            .on('change', onFiltroChange);
        $('#sgpFiltroSemanaDesde, #sgpFiltroSemanaHasta')
            .on('input', function () { this.value = this.value.replace(/\D/g, '').slice(0, 4); })
            .on('change', onFiltroChange);
        $('.sgp-tipo-check').on('change', function () {
            aplicarLimiteTipos();
            onFiltroChange();
        });

        $('#sgpFiltrosToggle').on('click', toggleSidebar);

        $('.sgp-modo-group button').on('click', function () {
            $('.sgp-modo-group button').removeClass('active');
            $(this).addClass('active');
            modo = $(this).data('modo');
            renderCharts();
        });

        aplicarLimiteTipos();
        cargarDatos();
    }

    function getTiposSeleccionados() {
        return $('.sgp-tipo-check:checked').map(function () { return this.value; }).get();
    }

    // Bloquea (sin desmarcar) las casillas de tipo aún no seleccionadas una
    // vez alcanzado el máximo comparable, igual que hacía maximumSelectionLength.
    function aplicarLimiteTipos() {
        var seleccionados = getTiposSeleccionados();
        var limiteAlcanzado = seleccionados.length >= MAX_TIPOS;
        $('.sgp-tipo-check').each(function () {
            this.disabled = limiteAlcanzado && !this.checked;
        });
    }

    /**
     * Convierte una semana escrita como 'aaww' (aa=año ISO de 2 dígitos,
     * ww=semana ISO, ej. '2637' = semana 37 de 2026) a una fecha AAAA-MM-DD.
     * dayOffset=0 -> lunes de esa semana, 6 -> domingo.
     * Se resuelve a fecha real (no se compara el aaww como texto) porque el
     * aaww derivado no ordena de forma fiable cuando el rango cruza fin de año.
     */
    function semanaAFecha(semanaStr, dayOffset) {
        if (!semanaStr || !/^\d{4}$/.test(semanaStr)) {
            return '';
        }
        var year = 2000 + parseInt(semanaStr.slice(0, 2), 10);
        var week = parseInt(semanaStr.slice(2, 4), 10);
        if (!week || week > 53) {
            return '';
        }
        var simple = new Date(Date.UTC(year, 0, 1 + (week - 1) * 7));
        var dow = simple.getUTCDay() || 7; // lunes=1 ... domingo=7
        var lunes = new Date(simple);
        lunes.setUTCDate(simple.getUTCDate() - dow + 1);
        var objetivo = new Date(lunes);
        objetivo.setUTCDate(lunes.getUTCDate() + dayOffset);

        var mm = String(objetivo.getUTCMonth() + 1).padStart(2, '0');
        var dd = String(objetivo.getUTCDate()).padStart(2, '0');
        return objetivo.getUTCFullYear() + '-' + mm + '-' + dd;
    }

    function construirFiltros() {
        var semanaDesde = $('#sgpFiltroSemanaDesde').val();
        var semanaHasta = $('#sgpFiltroSemanaHasta').val();
        return {
            finca: $('#sgpFiltroFinca').val() || [],
            bloque: $('#sgpFiltroBloque').val() || [],
            flor: $('#sgpFiltroFlor').val() || [],
            variedad: $('#sgpFiltroVariedad').val() || [],
            cosecha: $('#sgpFiltroCosecha').val() || [],
            ciclo: $('#sgpFiltroCiclo').val() || [],
            tipo: getTiposSeleccionados(),
            fecha_desde: semanaAFecha(semanaDesde, 0),
            fecha_hasta: semanaAFecha(semanaHasta, 6)
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
            url: '/ajax/seguimiento_produccion_filters.php',
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
        var el = document.getElementById('sgpEstadoCarga');
        if (!el) {
            return;
        }
        el.className = 'small text-' + (type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'muted'));
        el.textContent = text;
    }

    function colorParaTipo(tipo, idx) {
        return TIPO_COLORES[tipo] || PALETA_RESERVA[idx % PALETA_RESERVA.length];
    }

    function currentTipos() {
        if (!ultimoPayload) {
            return [];
        }
        return ultimoPayload.tipos || [];
    }

    function currentSeriesPorTipo() {
        if (!ultimoPayload) {
            return {};
        }
        return modo === 'edad' ? (ultimoPayload.porEdad || {}) : (ultimoPayload.porAass || {});
    }

    function xDeFila(row) {
        return modo === 'edad' ? row.edad : row.aass;
    }

    function etiquetasOrdenadas(porTipo, tipos) {
        var vistos = {};
        var etiquetas = [];
        tipos.forEach(function (tipo) {
            (porTipo[tipo] || []).forEach(function (row) {
                var x = xDeFila(row);
                if (!(x in vistos)) {
                    vistos[x] = true;
                    etiquetas.push(x);
                }
            });
        });
        etiquetas.sort(function (a, b) {
            return modo === 'edad' ? (Number(a) - Number(b)) : String(a).localeCompare(String(b));
        });
        return etiquetas;
    }

    function renderTabla(porTipo, tipos, etiquetas) {
        var tbody = document.getElementById('sgpTablaDetalle');
        var colX = document.getElementById('sgpColX');
        if (colX) {
            colX.textContent = modo === 'edad' ? 'Edad' : 'Año-Semana';
        }
        if (!tbody) {
            return;
        }

        var filas = [];
        etiquetas.forEach(function (x) {
            tipos.forEach(function (tipo) {
                var encontrada = (porTipo[tipo] || []).filter(function (row) { return xDeFila(row) == x; })[0];
                if (encontrada) {
                    filas.push({ x: x, tipo: tipo, row: encontrada });
                }
            });
        });

        if (filas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Sin datos</td></tr>';
            return;
        }

        var html = '';
        filas.forEach(function (item) {
            html += '<tr>'
                + '<td>' + item.x + '</td>'
                + '<td>' + item.tipo + '</td>'
                + '<td class="text-end">' + decimalFormat(item.row.avg_f_m, 2) + '</td>'
                + '<td class="text-end">' + integerFormat(item.row.total_corte) + '</td>'
                + '<td class="text-end">' + integerFormat(item.row.matas) + '</td>'
                + '<td class="text-end">' + integerFormat(item.row.registros) + '</td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    function renderAcumuladoCiclo(acumuladoPorTipo, tipos) {
        var tbody = document.getElementById('sgpTablaAcumuladoCiclo');
        if (!tbody) {
            return;
        }

        var filas = [];
        tipos.forEach(function (tipo) {
            (acumuladoPorTipo[tipo] || []).forEach(function (row) {
                filas.push({ tipo: tipo, row: row });
            });
        });

        if (filas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Sin datos</td></tr>';
            return;
        }

        var html = '';
        filas.forEach(function (item) {
            html += '<tr>'
                + '<td>' + item.tipo + '</td>'
                + '<td>Ciclo ' + item.row.ciclo + '</td>'
                + '<td class="text-end">' + decimalFormat(item.row.acumulado_f_m, 2) + '</td>'
                + '<td class="text-end">' + integerFormat(item.row.total_corte) + '</td>'
                + '<td class="text-end">' + decimalFormat(item.row.promedio_matas, 2) + '</td>'
                + '<td class="text-end">' + integerFormat(item.row.total_registros) + '</td>'
                + '</tr>';
        });
        tbody.innerHTML = html;
    }

    function buildMultiLineChart(canvasId, existingChart, labels, datasets, title, decimals) {
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
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: true, position: 'bottom' },
                title: { display: true, text: title },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            return ' ' + dataset.label + ': ' + decimalFormat(tooltipItem.yLabel, decimals);
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        scaleLabel: { display: true, labelString: xTitle }
                    }],
                    yAxes: [{
                        ticks: { beginAtZero: true },
                        scaleLabel: { display: true, labelString: title }
                    }]
                }
            }
        });
    }

    function renderCharts() {
        var porTipo = currentSeriesPorTipo();
        var tipos = currentTipos();
        var etiquetas = etiquetasOrdenadas(porTipo, tipos);

        var datasetsFM = [];
        var datasetsCorte = [];

        tipos.forEach(function (tipo, idx) {
            var porX = {};
            (porTipo[tipo] || []).forEach(function (row) { porX[xDeFila(row)] = row; });
            var color = colorParaTipo(tipo, idx);

            var dataFM = etiquetas.map(function (x) { var r = porX[x]; return r ? r.avg_f_m : null; });
            var dataCorte = etiquetas.map(function (x) { var r = porX[x]; return r ? r.total_corte : null; });

            var baseDataset = {
                label: tipo,
                borderColor: color,
                backgroundColor: color,
                fill: false,
                tension: 0.25,
                pointRadius: 2.5,
                pointHoverRadius: 4,
                borderWidth: 2,
                spanGaps: true
            };

            datasetsFM.push($.extend({}, baseDataset, { data: dataFM }));
            datasetsCorte.push($.extend({}, baseDataset, { data: dataCorte }));
        });

        chartFM = buildMultiLineChart('sgpChartFM', chartFM, etiquetas, datasetsFM, 'Promedio F/M', 2);
        chartCorte = buildMultiLineChart('sgpChartCorte', chartCorte, etiquetas, datasetsCorte, 'Total Corte (tallos)', 0);

        renderTabla(porTipo, tipos, etiquetas);
    }

    function cargarDatos() {
        setStatus('muted', 'Consultando seguimiento de producción...');
        $.ajax({
            url: '/ajax/seguimiento_produccion.php',
            method: 'GET',
            data: construirFiltros(),
            dataType: 'json'
        }).done(function (resp) {
            if (!resp || resp.ok !== true) {
                var msg = (resp && resp.message) ? resp.message : 'No se pudo cargar la información';
                setStatus('warning', msg);
                ultimoPayload = { tipos: [], porEdad: {}, porAass: {}, acumuladoCiclo: {} };
                renderCharts();
                renderAcumuladoCiclo({}, []);
                return;
            }
            ultimoPayload = resp;
            renderCharts();
            renderAcumuladoCiclo(resp.acumuladoCiclo || {}, resp.tipos || []);
            if ((resp.tipos || []).length === 0) {
                setStatus('warning', 'No hay datos para los filtros seleccionados.');
            } else {
                setStatus('muted', 'Información actualizada.');
            }
        }).fail(function () {
            setStatus('danger', 'Error al conectar con el servidor.');
        });
    }

    function toggleSidebar() {
        var sidebarCol = document.getElementById('sgpSidebarCol');
        var mainCol = document.getElementById('sgpMainCol');
        var btnMostrar = document.getElementById('sgpBtnMostrarFiltros');
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
            if (chartFM) { chartFM.resize(); }
            if (chartCorte) { chartCorte.resize(); }
        }, 200);
    }

    return {
        init: init,
        aplicarFiltros: cargarDatos,
        toggleSidebar: toggleSidebar,
        limpiarFiltros: function () {
            actualizandoFiltros = true;
            $('#sgpFiltroFinca, #sgpFiltroBloque, #sgpFiltroFlor, #sgpFiltroVariedad, #sgpFiltroCosecha, #sgpFiltroCiclo').val(null).trigger('change');
            $('#sgpFiltroSemanaDesde').val(defaultSemanaDesde);
            $('#sgpFiltroSemanaHasta').val(defaultSemanaHasta);
            $('.sgp-tipo-check').each(function () {
                this.checked = defaultTipos.indexOf(this.value) !== -1;
            });
            aplicarLimiteTipos();
            actualizandoFiltros = false;
            actualizarOpcionesFiltros();
            cargarDatos();
        }
    };
})();

document.addEventListener('DOMContentLoaded', SeguimientoProduccion.init);
