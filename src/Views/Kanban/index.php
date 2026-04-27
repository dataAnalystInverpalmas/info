<!-- Vista Kanban de Tareas -->
<style>
    #kanbanBoard {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        align-items: stretch;
        height: calc(100dvh - 220px);
        min-height: 420px;
        padding-bottom: 0.25rem;
    }
    .kanban-col {
        flex: 0 0 260px;
        background: #f4f6fb;
        border-radius: 8px;
        padding: 0.75rem 0.6rem;
        min-height: 200px;
        display: flex;
        flex-direction: column;
    }
    .kanban-col-header {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.6rem;
        padding: 0.35rem 0.6rem;
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .col-pendiente   .kanban-col-header { background: #fdecea; color: #c0392b; }
    .col-en_progreso .kanban-col-header { background: #e3f7fc; color: #0984a8; }
    .col-completada  .kanban-col-header { background: #eafaf1; color: #1a7a44; }
    .col-cancelada   .kanban-col-header { background: #f5f5f5; color: #7f8c8d; }

    .kanban-cards {
        min-height: 60px;
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding-right: 0.2rem;
    }

    @media (max-width: 767px) {
        #kanbanBoard {
            height: calc(100dvh - 250px);
            min-height: 360px;
        }
    }

    .kanban-card {
        background: #fff;
        border-radius: 6px;
        padding: 0.55rem 0.65rem;
        margin-bottom: 0.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.10);
        cursor: grab;
        border-left: 4px solid #ccc;
        transition: box-shadow .15s;
        font-size: 0.82rem;
        -webkit-user-select: none;
        user-select: none;
    }
    .kanban-card:active { cursor: grabbing; }
    .kanban-card.dragging { opacity: .45; }
    .kanban-card.drag-over-target { box-shadow: 0 0 0 2px #0d6efd; }

    .kanban-col.drag-over { background: #e8eeff; }

    /* Prioridad → borde izquierdo */
    .prio-urgente  { border-left-color: #dc3545; }
    .prio-alta     { border-left-color: #fd7e14; }
    .prio-media    { border-left-color: #ffc107; }
    .prio-baja     { border-left-color: #6c757d; }

    .kanban-card .card-title { font-weight: 600; margin-bottom: 2px; }
    .kanban-card .card-meta  { color: #6c757d; font-size: 0.75rem; }

    .badge-prio {
        font-size: 0.68rem;
        padding: 2px 5px;
        border-radius: 3px;
    }
    .badge-urgente { background:#dc3545; color:#fff; }
    .badge-alta    { background:#fd7e14; color:#fff; }
    .badge-media   { background:#ffc107; color:#000; }
    .badge-baja    { background:#6c757d; color:#fff; }

    .count-badge {
        background: #fff !important;
        color: #2c3e50 !important;
        border: 1px solid #d8dee8;
    }
</style>

<div class="container-fluid">
    <!-- Barra de filtros -->
    <div class="row align-items-center mb-3 g-2">
        <div class="col-auto">
            <label class="small text-muted mb-0">Desde</label>
            <input type="date" id="kDesde" class="form-control form-control-sm">
        </div>
        <div class="col-auto">
            <label class="small text-muted mb-0">Hasta</label>
            <input type="date" id="kHasta" class="form-control form-control-sm">
        </div>
        <div class="col-auto mt-3">
            <button class="btn btn-sm btn-primary" onclick="cargarKanban()">Aplicar</button>
            <button class="btn btn-sm btn-outline-secondary ms-1" onclick="semanaActual(); cargarKanban();">Esta semana</button>
        </div>
        <div class="col-auto mt-3 ms-auto">
            <span id="kResumen" class="small text-muted"></span>
        </div>
    </div>

    <!-- Tablero -->
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
        document.getElementById('kResumen').textContent = 'Cargando…';

        $.post('ajax/kanban_tareas.php', { accion: 'list', desde: desde, hasta: hasta }, function (res) {
            if (!res.success) {
                document.getElementById('kResumen').textContent = 'Error: ' + res.mensaje;
                return;
            }
            var total = res.tareas.length;
            var counts = { pendiente: 0, en_progreso: 0, completada: 0, cancelada: 0 };

            res.tareas.forEach(function (t) {
                var card = buildCard(t);
                var col  = document.getElementById('cards-' + t.estado);
                if (col) col.appendChild(card);
                if (counts[t.estado] !== undefined) counts[t.estado]++;
            });

            Object.keys(counts).forEach(function (e) {
                document.getElementById('count-' + e).textContent = counts[e];
            });
            document.getElementById('kResumen').textContent = total + ' tarea(s) encontrada(s)';
        }, 'json').fail(function () {
            document.getElementById('kResumen').textContent = 'Error de conexión';
        });
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

        div.innerHTML =
            '<div class="d-flex justify-content-between align-items-start">' +
                '<span class="card-title">' + escK(t.nombre) + '</span>' +
                '<span class="badge-prio ' + badgeClass + ' ms-1">' + prioLabel + '</span>' +
            '</div>' +
            '<div class="card-meta">' + escK(t.tipo || 'prevista') + venc + '</div>' +
            proj + resp;

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

                // Sumar al contador destino
                var cDest = document.getElementById('count-' + nuevoEstado);
                if (cDest) cDest.textContent = parseInt(cDest.textContent) + 1;
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
