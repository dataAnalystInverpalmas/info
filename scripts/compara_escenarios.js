/**
 * Comparar Escenarios — reporte interactivo.
 * Los datos llegan agregados una sola vez (por rango de semanas) y todo el resto
 * (filtros en cascada, series, gráficos, comparación, pivotes y detalle) se
 * recalcula en el navegador.
 *
 * Campos de cada fila: s=semana yyww, f=fecha (solo granularidad día), a=año,
 * v=variedad, u=fuente, t=tipo, l=flor, c=color (ld_colores.orden), n=tallos.
 */
var ComparaEscenarios = (function () {
    var DIMS = { fuente: 'u', tipo: 't', anio: 'a', flor: 'l', color: 'c', variedad: 'v', semana: 'x' };
    var DIM_LABEL = { fuente: 'Fuente', tipo: 'Tipo', anio: 'Año', flor: 'Flor', color: 'Color', variedad: 'Variedad', semana: 'Semana' };
    var FILTROS = {
        fuente: '#ceFFuente', tipo: '#ceFTipo', anio: '#ceFAnio',
        flor: '#ceFFlor', color: '#ceFColor', variedad: '#ceFVariedad'
    };
    var PALETA = ['#00796B', '#2563eb', '#f97316', '#9333ea', '#dc2626', '#0891b2', '#ca8a04', '#db2777', '#4d7c0f', '#475569'];

    var rows = [];
    var F = { fuente: [], tipo: [], anio: [], flor: [], color: [], variedad: [] };
    var serieDim = 'fuente';
    var tipoGrafico = 'line';
    var topN = 12;
    var acumulado = false;
    var foco = null;
    var hidden = {};
    var busqueda = '';
    var base = '';
    var comp = '';
    var granularidad = 'semana';
    var chart = null;
    var chartDiff = null;
    var suprimir = false;
    var colorCache = {};
    var colorCount = 0;
    var tablas = {};          // id -> {cols, rows} para exportar CSV
    var sorts = {};           // id -> {key, desc}
    var detallePag = 0;
    var DETALLE_TAM = 100;
    var defaults = { desde: '', hasta: '' };

    // ---------- utilidades ----------
    function esc(v) {
        return String(v === null || v === undefined ? '' : v).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function fmt(n) {
        n = Number(n);
        return isFinite(n) ? n.toLocaleString('es-CO', { maximumFractionDigits: 0 }) : '';
    }
    function pct(n) {
        return n === null || !isFinite(n) ? '—' : n.toLocaleString('es-CO', { maximumFractionDigits: 1 }) + '%';
    }
    function xKey(r) { return r.f || r.s; }
    function val(r, dim) { return dim === 'semana' ? xKey(r) : r[DIMS[dim]]; }
    function natural(a, b) { return String(a).localeCompare(String(b), 'es', { numeric: true }); }

    function colorFor(dim, name) {
        if (name === 'Otros') { return '#9aa5a1'; }
        var k = dim + '|' + name;
        if (!colorCache[k]) {
            var i = colorCount++;
            colorCache[k] = i < PALETA.length ? PALETA[i] : 'hsl(' + Math.round((i * 137.508) % 360) + ',62%,46%)';
        }
        return colorCache[k];
    }
    function alpha(c, a) {
        if (c.charAt(0) === '#') {
            var n = parseInt(c.slice(1), 16);
            return 'rgba(' + (n >> 16) + ',' + ((n >> 8) & 255) + ',' + (n & 255) + ',' + a + ')';
        }
        return c.replace('hsl(', 'hsla(').replace(')', ',' + a + ')');
    }

    // ---------- filtrado ----------
    function filtrar(excluir, opts) {
        opts = opts || {};
        return rows.filter(function (r) {
            for (var k in F) {
                if (k === excluir || !F[k].length) { continue; }
                if (F[k].indexOf(String(r[DIMS[k]])) === -1) { return false; }
            }
            if (opts.foco && foco !== null && xKey(r) !== foco) { return false; }
            return true;
        });
    }

    function agrupar(rs, dim) {
        var xsSet = {}, mapa = {};
        rs.forEach(function (r) {
            var x = xKey(r), nombre = String(val(r, dim));
            xsSet[x] = true;
            var s = mapa[nombre] || (mapa[nombre] = { name: nombre, byX: {}, total: 0 });
            s.byX[x] = (s.byX[x] || 0) + r.n;
            s.total += r.n;
        });
        var xs = Object.keys(xsSet).sort();
        var series = Object.keys(mapa).map(function (k) { return mapa[k]; })
            .sort(function (a, b) { return b.total - a.total; });
        return { xs: xs, series: series };
    }

    // ---------- opciones de filtro en cascada ----------
    function actualizarOpciones() {
        // 1) depura selecciones que ya no son compatibles
        for (var pasada = 0; pasada < 3; pasada++) {
            var cambio = false;
            Object.keys(F).forEach(function (k) {
                if (!F[k].length) { return; }
                var disp = distintos(k);
                var nuevo = F[k].filter(function (v) { return disp.indexOf(v) !== -1; });
                if (nuevo.length !== F[k].length) { F[k] = nuevo; cambio = true; }
            });
            if (!cambio) { break; }
        }
        // 2) reconstruye los selects
        suprimir = true;
        Object.keys(FILTROS).forEach(function (k) {
            var $s = $(FILTROS[k]);
            $s.empty();
            distintos(k).forEach(function (v) {
                $s.append(new Option(v, v, false, F[k].indexOf(v) !== -1));
            });
            $s.trigger('change.select2');
        });
        suprimir = false;
    }
    function distintos(k) {
        var set = {};
        filtrar(k).forEach(function (r) { set[String(r[DIMS[k]])] = true; });
        return Object.keys(set).sort(natural);
    }
    function leerFiltros() {
        Object.keys(FILTROS).forEach(function (k) { F[k] = ($(FILTROS[k]).val() || []).map(String); });
    }
    function onFiltro() {
        if (suprimir) { return; }
        leerFiltros();
        actualizarOpciones();
        render();
    }

    // ---------- carga ----------
    function cargar() {
        granularidad = $('#ceGranularidad').val();
        setEstado('muted', 'Consultando escenarios...');
        $('#ceMainCol').addClass('ce-loading');
        $.ajax({
            url: window.CE_CONFIG.endpoint, method: 'GET', dataType: 'json',
            data: {
                semana_desde: $('#ceSemanaDesde').val() || '',
                semana_hasta: $('#ceSemanaHasta').val() || '',
                granularidad: granularidad
            }
        }).done(function (resp) {
            if (!resp || resp.ok !== true) {
                rows = [];
                setEstado('warning', (resp && resp.message) || 'No se pudo cargar la información');
            } else {
                rows = (resp.rows || []).map(function (r) { r.x = xKey(r); return r; });
                setEstado('muted', fmt(rows.length) + ' registros cargados (' + (granularidad === 'dia' ? 'por día' : 'por semana') + ').');
            }
            foco = null;
            detallePag = 0;
            leerFiltros();
            actualizarOpciones();
            render();
        }).fail(function () {
            setEstado('danger', 'Error al conectar con el servidor.');
        }).always(function () {
            $('#ceMainCol').removeClass('ce-loading');
        });
    }
    function setEstado(tipo, texto) {
        $('#ceEstado').attr('class', 'small text-' + (tipo === 'danger' ? 'danger' : tipo === 'warning' ? 'warning' : 'muted')).text(texto);
    }

    // ---------- render general ----------
    function render() {
        renderChips();
        renderKpis();
        renderChart();
        renderComparacion();
        renderPivot();
        renderResumen();
        renderDetalle();
    }

    function renderChips() {
        var html = '';
        if (foco !== null) {
            html += '<span class="ce-chip" id="ceChipFoco" title="Quitar foco">' + (granularidad === 'dia' ? 'Día ' : 'Semana ') + esc(foco) + '<span class="material-icons">close</span></span> ';
        }
        $('#ceChips').html(html);
    }

    function renderKpis() {
        var rs = filtrar();
        var total = rs.reduce(function (a, r) { return a + r.n; }, 0);
        var porX = {};
        rs.forEach(function (r) { porX[xKey(r)] = (porX[xKey(r)] || 0) + r.n; });
        var pico = null;
        Object.keys(porX).forEach(function (x) { if (pico === null || porX[x] > porX[pico]) { pico = x; } });
        var vars = {};
        rs.forEach(function (r) { vars[r.v] = true; });

        var html = kpi('Total tallos', fmt(total), fmt(Object.keys(porX).length) + ' periodos', '#00796B', '', false);
        html += kpi('Pico', pico !== null ? esc(pico) : '—', pico !== null ? fmt(porX[pico]) + ' tallos' : '', '#f97316', '', false);
        html += kpi('Variedades', fmt(Object.keys(vars).length), 'con datos en el filtro', '#475569', '', false);

        // Una tarjeta por fuente: clic filtra solo esa fuente
        var fu = agrupar(filtrar('fuente'), 'fuente').series;
        fu.forEach(function (s) {
            var activo = F.fuente.length === 0 || F.fuente.indexOf(s.name) !== -1;
            html += kpi(esc(s.name), fmt(s.total), total && activo ? pct(s.total / total * 100) + ' del total' : 'tallos', colorFor('fuente', s.name),
                'data-fuente="' + esc(s.name) + '"', true, !activo);
        });
        $('#ceKpis').html(html);
    }
    function kpi(label, value, help, color, attrs, clickable, off) {
        return '<div class="ce-kpi ' + (clickable ? 'ce-clickable ' : '') + (off ? 'ce-off' : '') + '" style="border-left-color:' + color + '" ' + (attrs || '') + '>' +
            '<div class="ce-kpi-label">' + label + '</div><div class="ce-kpi-value">' + value + '</div>' +
            '<div class="ce-kpi-help">' + help + '</div></div>';
    }

    // ---------- gráfico principal ----------
    var pluginFondo = {
        beforeDraw: function (ch) {
            var c = ch.chart.ctx;
            c.save(); c.globalCompositeOperation = 'destination-over'; c.fillStyle = '#fff';
            c.fillRect(0, 0, ch.chart.width, ch.chart.height); c.restore();
        }
    };
    var pluginFoco = {
        afterDraw: function (ch) {
            if (foco === null) { return; }
            var idx = ch.data.labels.indexOf(foco);
            var scale = ch.scales['x-axis-0'];
            if (idx < 0 || !scale) { return; }
            var x = scale.getPixelForValue(null, idx), area = ch.chartArea, c = ch.chart.ctx;
            c.save(); c.strokeStyle = '#f97316'; c.lineWidth = 2; c.setLineDash([5, 4]);
            c.beginPath(); c.moveTo(x, area.top); c.lineTo(x, area.bottom); c.stroke(); c.restore();
        }
    };

    function datosSerie(dim, rs) {
        var g = agrupar(rs, dim);
        var series = g.series;
        if (topN > 0 && series.length > topN) {
            var otros = { name: 'Otros', byX: {}, total: 0 };
            series.slice(topN).forEach(function (s) {
                otros.total += s.total;
                Object.keys(s.byX).forEach(function (x) { otros.byX[x] = (otros.byX[x] || 0) + s.byX[x]; });
            });
            series = series.slice(0, topN).concat([otros]);
        }
        return { xs: g.xs, series: series };
    }

    function renderChart() {
        var g = datosSerie(serieDim, filtrar());
        var stack = tipoGrafico === 'stack';
        var esBarra = tipoGrafico === 'bar' || stack;
        $('#ceChartTitle').text('Tallos por ' + (granularidad === 'dia' ? 'día' : 'semana') + ' · series por ' + DIM_LABEL[serieDim].toLowerCase() + (acumulado ? ' (acumulado)' : ''));

        var datasets = g.series.map(function (s) {
            var run = 0;
            var data = g.xs.map(function (x) {
                var v = s.byX[x] || 0;
                if (acumulado) { run += v; return run; }
                return v;
            });
            var color = colorFor(serieDim, s.name);
            var ds = { label: s.name, data: data, hidden: !!hidden[s.name], borderColor: color, backgroundColor: color };
            if (!esBarra) {
                ds.fill = tipoGrafico === 'area';
                ds.backgroundColor = tipoGrafico === 'area' ? alpha(color, 0.2) : color;
                ds.tension = 0.25; ds.pointRadius = 2; ds.pointHoverRadius = 5; ds.borderWidth = 2;
            }
            return ds;
        });

        if (chart) { chart.destroy(); }
        chart = new Chart(document.getElementById('ceChart'), {
            type: esBarra ? 'bar' : 'line',
            data: { labels: g.xs, datasets: datasets },
            plugins: [pluginFondo, pluginFoco],
            options: {
                responsive: true, maintainAspectRatio: false, animation: { duration: 300 },
                hover: { mode: 'index', intersect: false },
                legend: {
                    position: 'bottom',
                    onClick: function (e, item) {
                        var n = datasets[item.datasetIndex].label;
                        hidden[n] = !hidden[n];
                        renderChart(); renderPivot();
                    }
                },
                tooltips: {
                    mode: 'index', intersect: false,
                    callbacks: {
                        label: function (ti, d) { return ' ' + d.datasets[ti.datasetIndex].label + ': ' + fmt(ti.yLabel); },
                        footer: function (items) {
                            return 'Total: ' + fmt(items.reduce(function (a, i) { return a + (Number(i.yLabel) || 0); }, 0));
                        }
                    }
                },
                onClick: function (evt) {
                    var els = chart.getElementsAtXAxis(evt);
                    if (!els.length) { return; }
                    setFoco(chart.data.labels[els[0]._index]);
                },
                scales: {
                    xAxes: [{ stacked: stack, scaleLabel: { display: true, labelString: granularidad === 'dia' ? 'Día' : 'Semana (yyww)' } }],
                    yAxes: [{ stacked: stack, ticks: { beginAtZero: true, callback: function (v) { return fmt(v); } }, scaleLabel: { display: true, labelString: 'Tallos' } }]
                }
            }
        });
    }

    function setFoco(x) {
        foco = (foco === x) ? null : x;
        detallePag = 0;
        renderChips(); renderChart(); renderComparacion(); renderPivot(); renderResumen(); renderDetalle();
    }

    // ---------- comparación de dos escenarios ----------
    function renderComparacion() {
        var rs = filtrar('fuente');
        var fuentes = agrupar(rs, 'fuente').series.map(function (s) { return s.name; }).sort(natural);
        if (fuentes.indexOf(base) === -1) { base = fuentes[0] || ''; }
        if (fuentes.indexOf(comp) === -1 || comp === base) { comp = fuentes.filter(function (f) { return f !== base; })[0] || base; }
        ['#ceBase', '#ceComp'].forEach(function (id) {
            var $s = $(id).empty();
            fuentes.forEach(function (f) { $s.append(new Option(f, f)); });
            $s.val(id === '#ceBase' ? base : comp);
        });

        var byB = {}, byC = {}, xsSet = {}, tb = 0, tc = 0;
        rs.forEach(function (r) {
            var x = xKey(r);
            if (r.u === base) { byB[x] = (byB[x] || 0) + r.n; xsSet[x] = true; tb += r.n; }
            if (r.u === comp) { byC[x] = (byC[x] || 0) + r.n; xsSet[x] = true; tc += r.n; }
        });
        var xs = Object.keys(xsSet).sort();
        var tabla = xs.map(function (x) {
            var b = byB[x] || 0, c = byC[x] || 0;
            return { x: x, base: b, comp: c, diff: c - b, pct: b ? (c - b) / b * 100 : null };
        });
        var mayores = tabla.filter(function (t) { return t.diff > 0; }).length;

        $('#ceCompKpis').html(
            kpi('Base · ' + esc(base), fmt(tb), 'tallos', colorFor('fuente', base), '', false) +
            kpi('Comparar · ' + esc(comp), fmt(tc), 'tallos', colorFor('fuente', comp), '', false) +
            kpi('Diferencia', (tc - tb > 0 ? '+' : '') + fmt(tc - tb), 'Comparar − Base', tc - tb >= 0 ? '#15803d' : '#b91c1c', '', false) +
            kpi('Variación', tb ? (tc - tb > 0 ? '+' : '') + pct((tc - tb) / tb * 100) : '—', 'sobre la base', tc - tb >= 0 ? '#15803d' : '#b91c1c', '', false) +
            kpi('Periodos mayores', fmt(mayores) + ' / ' + fmt(tabla.length), 'Comparar > Base', '#475569', '', false)
        );

        var cb = colorFor('fuente', base), cc = colorFor('fuente', comp);
        if (chartDiff) { chartDiff.destroy(); }
        chartDiff = new Chart(document.getElementById('ceChartDiff'), {
            type: 'bar',
            plugins: [pluginFondo, pluginFoco],
            data: {
                labels: xs,
                datasets: [
                    {
                        type: 'bar', label: 'Δ (Comparar − Base)', yAxisID: 'y-diff',
                        data: tabla.map(function (t) { return t.diff; }),
                        backgroundColor: tabla.map(function (t) { return t.diff >= 0 ? 'rgba(21,128,61,.55)' : 'rgba(185,28,28,.55)'; })
                    },
                    { type: 'line', label: base, yAxisID: 'y-main', data: tabla.map(function (t) { return t.base; }), borderColor: cb, backgroundColor: cb, fill: false, tension: .25, pointRadius: 2, borderWidth: 2 },
                    { type: 'line', label: comp, yAxisID: 'y-main', data: tabla.map(function (t) { return t.comp; }), borderColor: cc, backgroundColor: cc, fill: false, tension: .25, pointRadius: 2, borderWidth: 2 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false, animation: { duration: 300 },
                legend: { position: 'bottom' },
                tooltips: { mode: 'index', intersect: false, callbacks: { label: function (ti, d) { return ' ' + d.datasets[ti.datasetIndex].label + ': ' + fmt(ti.yLabel); } } },
                onClick: function (evt) {
                    var els = chartDiff.getElementsAtXAxis(evt);
                    if (els.length) { setFoco(chartDiff.data.labels[els[0]._index]); }
                },
                scales: {
                    xAxes: [{ scaleLabel: { display: true, labelString: granularidad === 'dia' ? 'Día' : 'Semana (yyww)' } }],
                    yAxes: [
                        { id: 'y-main', position: 'left', ticks: { beginAtZero: true, callback: function (v) { return fmt(v); } }, scaleLabel: { display: true, labelString: 'Tallos' } },
                        { id: 'y-diff', position: 'right', gridLines: { drawOnChartArea: false }, ticks: { callback: function (v) { return fmt(v); } }, scaleLabel: { display: true, labelString: 'Diferencia' } }
                    ]
                }
            }
        });

        var totalRow = { x: 'Total', base: tb, comp: tc, diff: tc - tb, pct: tb ? (tc - tb) / tb * 100 : null };
        renderTabla('ceTblComp', [
            { key: 'x', title: granularidad === 'dia' ? 'Día' : 'Semana', sticky: true },
            { key: 'base', title: base, num: true },
            { key: 'comp', title: comp, num: true },
            { key: 'diff', title: 'Δ', num: true, signed: true },
            { key: 'pct', title: 'Δ %', num: true, signed: true, pct: true }
        ], tabla, {
            footer: totalRow,
            rowClass: function (r) { return 'ce-row-click'; },
            onRow: function (r) { setFoco(r.x); }
        });
    }

    // ---------- tabla genérica ----------
    function renderTabla(id, cols, data, opts) {
        opts = opts || {};
        var st = sorts[id];
        var lista = data.slice();
        if (st) {
            lista.sort(function (a, b) {
                var va = a[st.key], vb = b[st.key], r;
                if (typeof va === 'number' || typeof vb === 'number') { r = (va === null ? -Infinity : va) - (vb === null ? -Infinity : vb); }
                else { r = natural(va, vb); }
                return st.desc ? -r : r;
            });
        }
        tablas[id] = { cols: cols, rows: lista, footer: opts.footer };
        var html = '<thead><tr>';
        cols.forEach(function (c) {
            var cls = (c.sticky ? 'ce-sticky ' : '') + (c.num ? 'text-right ' : '') + (c.total ? 'ce-total ' : '') +
                (st && st.key === c.key ? 'ce-sorted ' + (st.desc ? 'ce-desc' : '') : '');
            html += '<th class="' + cls + '" data-key="' + esc(c.key) + '">' + esc(c.title) + '</th>';
        });
        html += '</tr></thead><tbody>';
        var pag = opts.paginar;
        var visibles = pag ? lista.slice(pag.desde, pag.hasta) : lista;
        visibles.forEach(function (r, i) {
            var rc = opts.rowClass ? opts.rowClass(r) : '';
            html += '<tr class="' + esc(rc) + '" data-i="' + (pag ? pag.desde + i : i) + '">';
            cols.forEach(function (c) {
                var v = r[c.key], txt, cls = (c.sticky ? 'ce-sticky ' : '') + (c.num ? 'text-right ' : '') + (c.total ? 'ce-total ' : ''), style = '';
                if (c.num) {
                    if (v === null || v === undefined) { txt = '—'; }
                    else if (c.pct) { txt = (c.signed && v > 0 ? '+' : '') + pct(v); }
                    else { txt = (c.signed && v > 0 ? '+' : '') + fmt(v); }
                    if (c.signed && v !== null) { cls += v > 0 ? 'ce-pos ' : v < 0 ? 'ce-neg ' : ''; }
                    if (opts.cellStyle) { style = opts.cellStyle(c, r) || ''; }
                } else { txt = esc(v); if (c.dot) { txt = '<span class="ce-dot" style="background:' + c.dot(r) + '"></span>' + txt; } }
                if (opts.focoCol && opts.focoCol === c.key) { cls += 'ce-focus '; }
                html += '<td class="' + cls + '" style="' + style + '">' + txt + '</td>';
            });
            html += '</tr>';
        });
        if (!lista.length) { html += '<tr><td colspan="' + cols.length + '" class="text-center text-muted">Sin datos</td></tr>'; }
        html += '</tbody>';
        if (opts.footer) {
            html += '<tfoot><tr>';
            cols.forEach(function (c) {
                var v = opts.footer[c.key], txt = '';
                if (c.num && v !== null && v !== undefined) {
                    txt = c.pct ? (c.signed && v > 0 ? '+' : '') + pct(v) : (c.signed && v > 0 ? '+' : '') + fmt(v);
                } else if (v !== undefined && v !== null) { txt = esc(v); }
                html += '<td class="' + (c.sticky ? 'ce-sticky ' : '') + (c.num ? 'text-right' : '') + '">' + txt + '</td>';
            });
            html += '</tr></tfoot>';
        }
        var $t = $('#' + id).html(html);
        $t.data('onRow', opts.onRow || null);
    }

    // ---------- pivote semanal ----------
    function renderPivot() {
        var rs = filtrar();
        var g = agrupar(rs, serieDim);
        $('#cePivotTitle').text('Pivote por ' + DIM_LABEL[serieDim].toLowerCase() + ' y ' + (granularidad === 'dia' ? 'día' : 'semana'));

        var max = 0, totalesX = {}, totalGeneral = 0;
        g.series.forEach(function (s) {
            g.xs.forEach(function (x) { var v = s.byX[x] || 0; if (v > max) { max = v; } totalesX[x] = (totalesX[x] || 0) + v; });
            totalGeneral += s.total;
        });

        var cols = [{ key: '_name', title: DIM_LABEL[serieDim], sticky: true, dot: function (r) { return colorFor(serieDim, r._name); } }];
        g.xs.forEach(function (x) { cols.push({ key: 'x_' + x, title: x, num: true }); });
        cols.push({ key: '_total', title: 'Total', num: true, total: true });

        var data = g.series.map(function (s) {
            var o = { _name: s.name, _total: s.total };
            g.xs.forEach(function (x) { o['x_' + x] = s.byX[x] || 0; });
            return o;
        });
        var footer = { _name: 'Total', _total: totalGeneral };
        g.xs.forEach(function (x) { footer['x_' + x] = totalesX[x] || 0; });

        renderTabla('ceTblPivot', cols, data, {
            footer: footer,
            focoCol: foco !== null ? 'x_' + foco : null,
            rowClass: function (r) { return 'ce-row-click' + (hidden[r._name] ? ' ce-row-hidden' : ''); },
            cellStyle: function (c, r) {
                if (c.total || !max) { return ''; }
                var v = r[c.key];
                return v ? 'background:' + alpha('#00796B', Math.min(0.55, (v / max) * 0.55)) : '';
            },
            onRow: function (r) { hidden[r._name] = !hidden[r._name]; renderChart(); renderPivot(); }
        });
    }

    // ---------- resumen por dimensión x fuente ----------
    function renderResumen() {
        var dim = $('#ceDimResumen').val();
        var rs = filtrar(null, { foco: true });
        var fuentes = agrupar(rs, 'fuente').series.map(function (s) { return s.name; }).sort(natural);
        var mapa = {};
        rs.forEach(function (r) {
            var k = String(val(r, dim));
            var o = mapa[k] || (mapa[k] = { _dim: k, _total: 0 });
            o['f_' + r.u] = (o['f_' + r.u] || 0) + r.n;
            o._total += r.n;
        });
        var tieneComp = base && comp && base !== comp;
        var data = Object.keys(mapa).map(function (k) {
            var o = mapa[k];
            fuentes.forEach(function (f) { if (o['f_' + f] === undefined) { o['f_' + f] = 0; } });
            if (tieneComp) {
                var b = o['f_' + base] || 0, c = o['f_' + comp] || 0;
                o._diff = c - b; o._pct = b ? (c - b) / b * 100 : null;
            }
            return o;
        });
        var cols = [{ key: '_dim', title: DIM_LABEL[dim], sticky: true, dot: dim === 'fuente' ? function (r) { return colorFor('fuente', r._dim); } : null }];
        fuentes.forEach(function (f) { cols.push({ key: 'f_' + f, title: f, num: true }); });
        cols.push({ key: '_total', title: 'Total', num: true, total: true });
        if (tieneComp) {
            cols.push({ key: '_diff', title: 'Δ ' + comp + ' − ' + base, num: true, signed: true });
            cols.push({ key: '_pct', title: 'Δ %', num: true, signed: true, pct: true });
        }
        var footer = { _dim: 'Total', _total: 0 };
        data.forEach(function (o) {
            fuentes.forEach(function (f) { footer['f_' + f] = (footer['f_' + f] || 0) + o['f_' + f]; });
            footer._total += o._total;
        });
        if (tieneComp) {
            footer._diff = (footer['f_' + comp] || 0) - (footer['f_' + base] || 0);
            footer._pct = footer['f_' + base] ? footer._diff / footer['f_' + base] * 100 : null;
        }
        if (!sorts.ceTblResumen) { sorts.ceTblResumen = { key: '_total', desc: true }; }
        renderTabla('ceTblResumen', cols, data, {
            footer: footer,
            rowClass: function () { return 'ce-row-click'; },
            onRow: function (r) {
                // Clic en una fila: filtra el resto del reporte por ese valor (si la dimensión es filtrable)
                if (FILTROS[dim]) {
                    F[dim] = (F[dim].length === 1 && F[dim][0] === r._dim) ? [] : [r._dim];
                    actualizarOpciones(); render();
                }
            }
        });
    }

    // ---------- detalle ----------
    function renderDetalle() {
        var rs = filtrar(null, { foco: true });
        if (busqueda) {
            var q = busqueda.toLowerCase();
            rs = rs.filter(function (r) {
                return (r.x + ' ' + r.a + ' ' + r.u + ' ' + r.t + ' ' + r.l + ' ' + r.v + ' ' + r.c).toLowerCase().indexOf(q) !== -1;
            });
        }
        var total = rs.reduce(function (a, r) { return a + r.n; }, 0);
        var maxPag = Math.max(0, Math.ceil(rs.length / DETALLE_TAM) - 1);
        if (detallePag > maxPag) { detallePag = maxPag; }
        var cols = [
            { key: 'x', title: granularidad === 'dia' ? 'Día' : 'Semana' },
            { key: 'a', title: 'Año' }, { key: 'u', title: 'Fuente', dot: function (r) { return colorFor('fuente', r.u); } },
            { key: 't', title: 'Tipo' }, { key: 'l', title: 'Flor' },
            { key: 'v', title: 'Variedad' }, { key: 'c', title: 'Color' },
            { key: 'n', title: 'Tallos', num: true }
        ];
        if (!sorts.ceTblDetalle) { sorts.ceTblDetalle = { key: 'x', desc: false }; }
        var desde = detallePag * DETALLE_TAM;
        renderTabla('ceTblDetalle', cols, rs, { footer: { x: 'Total', n: total }, paginar: { desde: desde, hasta: desde + DETALLE_TAM } });
        $('#ceDetalleInfo').text(fmt(rs.length) ? 'Mostrando ' + fmt(Math.min(rs.length, desde + 1)) + '–' + fmt(Math.min(rs.length, desde + DETALLE_TAM)) + ' de ' + fmt(rs.length) : '');
    }

    // ---------- CSV ----------
    function exportarCsv(id, nombre) {
        var t = tablas[id];
        if (!t) { return; }
        var linea = function (vals) { return vals.map(function (v) { return '"' + String(v === null || v === undefined ? '' : v).replace(/"/g, '""') + '"'; }).join(';'); };
        var out = [linea(t.cols.map(function (c) { return c.title; }))];
        t.rows.forEach(function (r) {
            out.push(linea(t.cols.map(function (c) { var v = r[c.key]; return typeof v === 'number' ? String(Math.round(v * 100) / 100).replace('.', ',') : v; })));
        });
        var blob = new Blob(['\ufeff' + out.join('\r\n')], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = nombre + '.csv';
        document.body.appendChild(a); a.click(); document.body.removeChild(a);
    }

    // ---------- UI ----------
    function toggleSidebar() {
        var wrapper = document.getElementById('ceFiltersWrapper');
        if (!wrapper) return;
        var btn = document.querySelector('[data-target="#ceFiltersWrapper"]');
        var isExp = wrapper.classList.toggle('is-expanded');
        wrapper.classList.toggle('is-collapsed', !isExp);
        if (btn) {
            btn.classList.toggle('active', isExp);
            var textEl = btn.querySelector('.toggle-text');
            var iconEl = btn.querySelector('.toggle-icon');
            if (textEl) textEl.textContent = isExp ? 'Menos filtros' : 'Más filtros';
            if (iconEl) iconEl.textContent = isExp ? 'expand_less' : 'expand_more';
        }
        setTimeout(function () { if (chart) { chart.resize(); } if (chartDiff) { chartDiff.resize(); } }, 320);
    }

    function segmento(id, cb) {
        $(id + ' button').on('click', function () {
            $(id + ' button').removeClass('active');
            $(this).addClass('active');
            cb($(this).data('v'));
        });
    }

    function limpiar() {
        Object.keys(F).forEach(function (k) { F[k] = []; });
        hidden = {}; foco = null; busqueda = ''; $('#ceBuscar').val('');
        $('#ceSemanaDesde').val(defaults.desde).trigger('change.select2');
        $('#ceSemanaHasta').val(defaults.hasta).trigger('change.select2');
        cargar();
    }

    function init() {
        $('.ce-ms').select2({ width: '100%', placeholder: 'Todos', allowClear: true, dropdownParent: $('#ceFiltros') });
        $('.ce-single').select2({ width: '100%', dropdownParent: $('#ceFiltros') });
        defaults.desde = $('#ceSemanaDesde').val() || '';
        defaults.hasta = $('#ceSemanaHasta').val() || '';

        $('.ce-ms').on('change', onFiltro);
        $('#ceFiltrosToggle, #ceBtnMostrarFiltros').on('click', toggleSidebar);
        $('#ceBtnCargar').on('click', cargar);
        $('#ceBtnLimpiar').on('click', limpiar);

        segmento('#ceSegSerie', function (v) { serieDim = v; renderChart(); renderPivot(); });
        segmento('#ceSegTipo', function (v) { tipoGrafico = v; renderChart(); });
        $('#ceTopN').on('change', function () { topN = parseInt(this.value, 10); renderChart(); });
        $('#ceAcumulado').on('change', function () { acumulado = this.checked; renderChart(); });
        $('#ceBtnPng').on('click', function () {
            var a = document.createElement('a');
            a.href = document.getElementById('ceChart').toDataURL('image/png');
            a.download = 'comparar_escenarios.png'; a.click();
        });

        $('#ceBase').on('change', function () { base = this.value; if (comp === base) { comp = ''; } renderComparacion(); renderResumen(); });
        $('#ceComp').on('change', function () { comp = this.value; if (comp === base) { base = ''; } renderComparacion(); renderResumen(); });
        $('#ceSwap').on('click', function () { var t = base; base = comp; comp = t; renderComparacion(); renderResumen(); });

        $('#ceDimResumen').on('change', function () { delete sorts.ceTblResumen; renderResumen(); });
        $('#ceBuscar').on('input', function () { busqueda = this.value.trim(); detallePag = 0; renderDetalle(); });
        $('#cePrev').on('click', function () { if (detallePag > 0) { detallePag--; renderDetalle(); } });
        $('#ceNext').on('click', function () { detallePag++; renderDetalle(); });
        $('#ceCsvPivot').on('click', function () { exportarCsv('ceTblPivot', 'pivote_escenarios'); });
        $('#ceCsvResumen').on('click', function () { exportarCsv('ceTblResumen', 'resumen_escenarios'); });
        $('#ceCsvDetalle').on('click', function () { exportarCsv('ceTblDetalle', 'detalle_escenarios'); });

        // Delegación: ordenar por encabezado, clic en fila, chips y tarjetas de fuente
        $(document).on('click', '.ce-tbl thead th', function () {
            var id = $(this).closest('table').attr('id'), key = $(this).data('key');
            var s = sorts[id];
            sorts[id] = { key: key, desc: s && s.key === key ? !s.desc : (key !== 'x' && key !== '_name' && key !== '_dim') };
            if (id === 'ceTblPivot') { renderPivot(); } else if (id === 'ceTblResumen') { renderResumen(); }
            else if (id === 'ceTblDetalle') { detallePag = 0; renderDetalle(); } else { renderComparacion(); }
        });
        $(document).on('click', '.ce-tbl tbody tr[data-i]', function () {
            var $t = $(this).closest('table'), fn = $t.data('onRow'), t = tablas[$t.attr('id')];
            if (fn && t) { fn(t.rows[$(this).data('i')]); }
        });
        $(document).on('click', '#ceChipFoco', function () { setFoco(foco); });
        $(document).on('click', '.ce-kpi[data-fuente]', function () {
            var f = String($(this).data('fuente'));
            F.fuente = (F.fuente.length === 1 && F.fuente[0] === f) ? [] : [f];
            actualizarOpciones(); render();
        });

        cargar();
    }

    return { init: init };
})();

document.addEventListener('DOMContentLoaded', ComparaEscenarios.init);
