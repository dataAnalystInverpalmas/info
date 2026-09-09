<!-- Vista Kanban de Tareas -->
<style>
    #kanbanBoard {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.5rem;
        align-items: stretch;
        height: calc(100dvh - 230px);
        min-height: 440px;
        padding-bottom: 0.25rem;
        width: 100%;
    }

    .kanban-col {
        background: #f1f5f9;
        border: 1px solid #e4e9f0;
        border-radius: 14px;
        padding: 0.85rem;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .kanban-col-header {
        font-weight: 700;
        font-size: 0.82rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
        padding: 0.45rem 0.75rem;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .col-pendiente   .kanban-col-header { background: #fef2f2; color: #b91c1c; }
    .col-en_progreso .kanban-col-header { background: #e0f2fe; color: #0369a1; }
    .col-completada  .kanban-col-header { background: #dcfce7; color: #15803d; }
    .col-cancelada   .kanban-col-header { background: #f1f5f9; color: #475569; }

    .kanban-cards {
        min-height: 60px;
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding-right: 0.2rem;
    }

    @media (max-width: 1199px) {
        #kanbanBoard {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        #kanbanBoard {
            grid-template-columns: minmax(0, 1fr);
            height: auto;
            min-height: 360px;
        }
    }

    /* ── Tarjetas de tareas ─────────────────────────────────── */
    .kanban-card {
        background: #ffffff;
        border-radius: 12px;
        border-left: 4px solid #cbd5e1;
        padding: 0.9rem 1rem 2.1rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08), 0 1px 2px rgba(16, 24, 40, 0.04);
        cursor: grab;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        font-size: 0.84rem;
        -webkit-user-select: none;
        user-select: none;
        position: relative;
    }
    .kanban-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(16, 24, 40, 0.10);
    }
    .kanban-card:active { cursor: grabbing; }
    .kanban-card.dragging { opacity: .45; }
    .kanban-card.drag-over-target { box-shadow: 0 0 0 2px #00796B; }

    .kanban-col.drag-over { background: #eafff8; border-color: #8fd8c8; }

    /* Prioridad → borde izquierdo de 4px */
    .prio-urgente  { border-left-color: #dc2626; }
    .prio-alta     { border-left-color: #f97316; }
    .prio-media    { border-left-color: #f5b301; }
    .prio-baja     { border-left-color: #94a3b8; }

    .kanban-card .card-title {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1f2937;
        line-height: 1.35;
        margin-bottom: 4px;
    }
    .kanban-card .card-meta { color: #6b7280; font-size: 0.74rem; line-height: 1.5; }

    /* Insignia de prioridad sutil (estilo outline, sin saturación) */
    .badge-prio {
        font-size: 0.64rem;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 999px;
        border: 1px solid transparent;
        letter-spacing: 0.02em;
        flex: 0 0 auto;
    }
    .badge-urgente { background: #fdecea; color: #c0392b; border-color: #f3c1ba; }
    .badge-alta    { background: #fff3e6; color: #b45309; border-color: #fcd9b0; }
    .badge-media   { background: #fdf6e3; color: #92610c; border-color: #f0dc9c; }
    .badge-baja    { background: #eef2f6; color: #4b5563; border-color: #dde4ec; }

    .count-badge {
        background: #ffffff !important;
        color: #334155 !important;
        border: 1px solid #d8dee8 !important;
        font-weight: 600;
        border-radius: 999px;
        padding: 3px 9px;
    }

    /* % Avance en la esquina inferior derecha */
    .kanban-card .card-avance {
        position: absolute;
        right: 0.85rem;
        bottom: 0.6rem;
        font-size: 0.7rem;
        font-weight: 600;
        color: #00796B;
        background: #e6f4f1;
        border-radius: 999px;
        padding: 2px 10px;
    }

    #kGeneralWrap {
        min-width: 280px;
    }

    .gestion-title {
        font-size: 1.15rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
</style>

<?php
$gestionActive = 'kanban';
$gestionTitle = 'Gestión de Kanban';
$gestionSubtitle = 'Visualiza el flujo de trabajo por estado y actúa rápido sobre proyectos y tareas vinculadas.';
$gestionQuickActions = [];
require __DIR__ . '/../Shared/gestion_header.php';
?>

<div class="container-fluid px-3 px-lg-4 pt-2 pb-4">
    <div class="row">
        <div class="col-md-2 col-lg-2">
            <div class="filtro-sidebar mr-0 mr-lg-3">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">
                        <span class="material-icons" style="font-size:1rem;vertical-align:middle;">filter_list</span>
                        Filtros
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <label for="kDesde">Desde</label>
                            <input type="date" id="kDesde" class="form-control form-control-sm">
                        </div>
                        <div class="mb-3">
                            <label for="kHasta">Hasta</label>
                            <input type="date" id="kHasta" class="form-control form-control-sm">
                        </div>
                        <div class="mt-3">
                            <button class="btn btn-sm btn-block btn-brand-green mb-2" onclick="cargarKanban()">Aplicar</button>
                            <button class="btn btn-sm btn-block btn-outline-secondary" onclick="semanaActual(); cargarKanban();">Esta semana</button>
                        </div>
                        <hr class="my-3">
                        <button class="btn btn-sm btn-block btn-outline-secondary mb-2" onclick="exportarKanbanExcel()">
                            <span class="material-icons" style="font-size:0.95rem;vertical-align:text-bottom;">file_download</span> Exportar Excel
                        </button>
                        <button class="btn btn-sm btn-block btn-outline-secondary" onclick="exportarKanbanPDF()">
                            <span class="material-icons" style="font-size:0.95rem;vertical-align:text-bottom;">picture_as_pdf</span> Exportar PDF
                        </button>
                    </div>
                </div>
                <div id="kGeneralWrap">
                    <div id="kResumen" class="small text-muted"></div>
                    <div class="small text-muted mt-1">Avance general: <span id="kGeneralPct">0%</span></div>
                    <div class="progress mt-1" style="height: 8px;">
                        <div id="kGeneralBar" class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-10 col-lg-10">
            <div id="kanbanBoard">
                <?php
                $columnas = [
                    'pendiente'   => 'Pendiente',
                    'en_progreso' => 'En Progreso',
                    'completada'  => 'Completada',
                    'cancelada'   => 'Cancelada',
                ];
                foreach ($columnas as $estado => $label): ?>
                    <div class="kanban-col col-<?php echo $estado; ?>"
                         id="col-<?php echo $estado; ?>"
                         data-estado="<?php echo $estado; ?>"
                         ondragover="onDragOver(event)"
                         ondragleave="onDragLeave(event)"
                         ondrop="onDrop(event)">
                        <div class="kanban-col-header">
                            <span><?php echo $label; ?></span>
                            <span class="badge bg-secondary count-badge" id="count-<?php echo $estado; ?>">0</span>
                        </div>
                        <div class="kanban-cards" id="cards-<?php echo $estado; ?>"
                             ondragover="onDragOver(event)"
                             ondragleave="onDragLeave(event)"
                             ondrop="onDrop(event)"></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    // ── Semana actual (lun–dom) ──────────────────────────────
    function semanaActual() {
        var hoy  = new Date();
        var dia  = hoy.getDay(); // 0=dom
        var lun  = new Date(hoy);
        lun.setDate(hoy.getDate() - ((dia === 0 ? 7 : dia) - 1));
        var dom  = new Date(lun);
        dom.setDate(lun.getDate() + 6);
        document.getElementById('kDesde').value = toISO(lun);
        document.getElementById('kHasta').value = toISO(dom);
    }
    window.semanaActual = semanaActual;

    function toISO(d) {
        return d.getFullYear() + '-'
            + String(d.getMonth() + 1).padStart(2, '0') + '-'
            + String(d.getDate()).padStart(2, '0');
    }

    function actualizarIndicadorGeneral() {
        var cards = document.querySelectorAll('.kanban-card .card-avance');
        if (cards.length === 0) {
            document.getElementById('kGeneralPct').textContent = '0%';
            document.getElementById('kGeneralBar').style.width = '0%';
            document.getElementById('kGeneralBar').setAttribute('aria-valuenow', '0');
            return;
        }
        var sum = 0;
        cards.forEach(function(card) {
            var pct = parseInt(card.textContent.replace('%', '')) || 0;
            sum += pct;
        });
        var avanceGeneral = Math.round(sum / cards.length);
        document.getElementById('kGeneralPct').textContent = avanceGeneral + '%';
        document.getElementById('kGeneralBar').style.width = avanceGeneral + '%';
        document.getElementById('kGeneralBar').setAttribute('aria-valuenow', String(avanceGeneral));
    }

    // ── Carga inicial ────────────────────────────────────────
    semanaActual();

    // ── Cargar Kanban ────────────────────────────────────────
    window.cargarKanban = function () {
        var desde = document.getElementById('kDesde').value;
        var hasta = document.getElementById('kHasta').value;
        if (!desde || !hasta) return;

        // Limpiar columnas
        ['pendiente', 'en_progreso', 'completada', 'cancelada'].forEach(function (e) {
            document.getElementById('cards-' + e).innerHTML = '';
            document.getElementById('count-' + e).textContent = '0';
        });
        document.getElementById('kGeneralPct').textContent = '0%';
        document.getElementById('kGeneralBar').style.width = '0%';
        document.getElementById('kGeneralBar').setAttribute('aria-valuenow', '0');
        document.getElementById('kResumen').textContent = 'Cargando…';

        $.post('ajax/kanban_tareas.php', { accion: 'list', desde: desde, hasta: hasta }, function (res) {
            if (!res.success) {
                document.getElementById('kResumen').textContent = 'Error: ' + res.mensaje;
                return;
            }
            var total = res.tareas.length;
            var counts = { pendiente: 0, en_progreso: 0, completada: 0, cancelada: 0 };
            var completadas = 0;

            res.tareas.forEach(function (t) {
                var card = buildCard(t);
                var col  = document.getElementById('cards-' + t.estado);
                if (col) col.appendChild(card);
                if (counts[t.estado] !== undefined) counts[t.estado]++;
                if (t.estado === 'completada') completadas++;
            });

            Object.keys(counts).forEach(function (e) {
                document.getElementById('count-' + e).textContent = counts[e];
            });
            document.getElementById('kResumen').textContent = total + ' tarea(s) encontrada(s)';
            actualizarIndicadorGeneral();
        }, 'json').fail(function () {
            document.getElementById('kResumen').textContent = 'Error de conexión';
        });
    };

    window.exportarKanbanExcel = function () {
        var desde = document.getElementById('kDesde').value;
        var hasta = document.getElementById('kHasta').value;

        if (!desde || !hasta) {
            alert('Debes seleccionar el rango de fechas');
            return;
        }

        var url = 'ajax/kanban_tareas.php?accion=export_excel'
            + '&desde=' + encodeURIComponent(desde)
            + '&hasta=' + encodeURIComponent(hasta);
        window.location.href = url;
    };

    window.exportarKanbanPDF = function () {
        var desde = document.getElementById('kDesde').value;
        var hasta = document.getElementById('kHasta').value;

        if (!desde || !hasta) {
            alert('Debes seleccionar el rango de fechas');
            return;
        }

        var url = 'ajax/kanban_tareas.php?accion=export_pdf'
            + '&desde=' + encodeURIComponent(desde)
            + '&hasta=' + encodeURIComponent(hasta);
        window.location.href = url;
    };

    // ── Construir tarjeta ────────────────────────────────────
    function buildCard(t) {
        var div = document.createElement('div');
        div.className = 'kanban-card prio-' + (t.prioridad || 'baja');
        div.setAttribute('draggable', 'true');
        div.dataset.id     = t.id;
        div.dataset.estado = t.estado;

        var prio = t.prioridad || 'baja';
        var badgeClass = 'badge-' + prio;
        var prioLabel  = { urgente: 'Urgente', alta: 'Alta', media: 'Media', baja: 'Baja' }[prio] || prio;

        var venc = t.fecha_vencimiento ? ' · vence: ' + t.fecha_vencimiento : '';
        var proj = t.proyecto_nombre   ? '<br><span class="card-meta">📁 ' + escK(t.proyecto_nombre) + '</span>' : '';
        var resp = t.responsable       ? '<br><span class="card-meta">👤 ' + escK(t.responsable) + '</span>' : '';
        var avance = Math.max(0, Math.min(100, parseInt(t.porcentaje_avance || 0, 10)));

        div.innerHTML =
            '<div class="d-flex justify-content-between align-items-start">' +
                '<span class="card-title">' + escK(t.nombre) + '</span>' +
                '<span class="badge-prio ' + badgeClass + ' ms-1">' + prioLabel + '</span>' +
            '</div>' +
            '<div class="card-meta">' + escK(t.tipo || 'prevista') + venc + '</div>' +
            proj + resp +
            '<span class="card-avance">' + avance + '%</span>';

        // Drag events
        div.addEventListener('dragstart', function (e) {
            e.dataTransfer.setData('text/plain', String(t.id));
            div.classList.add('dragging');
        });
        div.addEventListener('dragend', function () {
            div.classList.remove('dragging');
        });
        // Permitir soltar SOBRE una tarjeta existente
        div.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var col = getKanbanCol(div);
            if (col) col.classList.add('drag-over');
        });
        div.addEventListener('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var col = getKanbanCol(div);
            if (!col) return;
            col.classList.remove('drag-over');
            handleDrop(e.dataTransfer.getData('text/plain'), col.dataset.estado);
        });

        return div;
    }

    // ── Drag & Drop ──────────────────────────────────────────
    // Sube por el DOM hasta encontrar el .kanban-col padre
    function getKanbanCol(el) {
        while (el && !el.classList.contains('kanban-col')) el = el.parentElement;
        return el;
    }

    // Lógica compartida de drop (actualiza estado en servidor y mueve la tarjeta)
    function handleDrop(tareaId, nuevoEstado) {
        var card = document.querySelector('.kanban-card[data-id="' + tareaId + '"]');
        if (!card || card.dataset.estado === nuevoEstado) return;

        $.post('ajax/kanban_tareas.php', { accion: 'update_estado', id: tareaId, estado: nuevoEstado }, function (res) {
            if (res.success) {
                // Restar del contador origen
                var prev = card.dataset.estado;
                var cPrev = document.getElementById('count-' + prev);
                if (cPrev) cPrev.textContent = Math.max(0, parseInt(cPrev.textContent) - 1);

                // Mover tarjeta
                card.dataset.estado = nuevoEstado;
                document.getElementById('cards-' + nuevoEstado).appendChild(card);

                var spanAvance = card.querySelector('.card-avance');
                if (spanAvance && typeof res.porcentaje_avance !== 'undefined') {
                    spanAvance.textContent = String(res.porcentaje_avance) + '%';
                }

                // Sumar al contador destino
                var cDest = document.getElementById('count-' + nuevoEstado);
                if (cDest) cDest.textContent = parseInt(cDest.textContent) + 1;

                actualizarIndicadorGeneral();
            } else {
                alert('No se pudo actualizar: ' + res.mensaje);
            }
        }, 'json').fail(function () {
            alert('Error de conexión al actualizar estado');
        });
    }

    window.onDragOver = function (e) {
        e.preventDefault();
        var col = getKanbanCol(e.currentTarget);
        if (col) col.classList.add('drag-over');
    };
    window.onDragLeave = function (e) {
        var col = getKanbanCol(e.currentTarget);
        // Solo quitar highlight si el cursor salió realmente de la columna
        if (col && !col.contains(e.relatedTarget)) {
            col.classList.remove('drag-over');
        }
    };
    window.onDrop = function (e) {
        e.preventDefault();
        var col = getKanbanCol(e.currentTarget);
        if (!col) return;
        col.classList.remove('drag-over');
        handleDrop(e.dataTransfer.getData('text/plain'), col.dataset.estado);
    };

    // ── Escape HTML ──────────────────────────────────────────
    function escK(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ── Auto-carga al incluir la vista ───────────────────────
    $(document).ready(function () {
        cargarKanban();
    });
}());
</script>
