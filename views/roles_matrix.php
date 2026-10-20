<?php
\App\Helpers\Auth::requireAdmin();
?>
<div class="container-fluid py-4" style="max-width: 1400px;">
    <!-- Encabezado de Página -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 pb-2 border-bottom">
        <div>
            <div class="d-flex align-items-center mb-1">
                <span class="material-icons text-primary mr-2" style="font-size: 2rem; color: #00796B !important;">grid_view</span>
                <h2 class="h4 font-weight-bold text-dark mb-0">Matriz de Permisos por Rol</h2>
            </div>
            <p class="text-muted small mb-0">Active o desactive el acceso a cada módulo del sistema de forma interactiva con guardado automático.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex align-items-center">
            <div id="saveFeedback" class="mr-3 small font-weight-bold text-muted d-flex align-items-center" style="opacity: 0; transition: opacity 0.3s ease;">
                <span class="material-icons text-success mr-1" style="font-size: 1.1rem;">check_circle</span>
                <span id="saveFeedbackText">Guardado</span>
            </div>
            <a href="index.php?report=crud_roles" class="btn btn-outline-secondary btn-sm d-flex align-items-center mr-2">
                <span class="material-icons mr-1" style="font-size: 1.1rem;">list</span> Vista Clásica
            </a>
            <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center" id="btnReloadMatrix" style="color: #00796B; border-color: #00796B;">
                <span class="material-icons mr-1" style="font-size: 1.1rem;">refresh</span> Recargar
            </button>
        </div>
    </div>

    <!-- Barra de Búsqueda Rápida -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white border-right-0" style="border-radius: 8px 0 0 8px;">
                        <span class="material-icons text-muted" style="font-size: 1.2rem;">search</span>
                    </span>
                </div>
                <input type="text" id="filtroModulo" class="form-control border-left-0" placeholder="Buscar módulo por nombre o ruta técnica (ej. 'Floreros', 'Lonas', 'Bautizo', 'plano')..." style="border-radius: 0 8px 8px 0;">
            </div>
        </div>
    </div>

    <!-- Contenedor de la Matriz -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="matrixTable">
                <thead class="thead-light" style="background-color: #F8FAFC; border-bottom: 2px solid #E2E8F0;">
                    <tr id="matrixHeaderRow">
                        <th style="min-width: 320px; font-weight: 600; color: #4A5568;">Módulo / Vista</th>
                        <!-- Columnas de roles dinámicas -->
                    </tr>
                </thead>
                <tbody id="matrixBody">
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <div class="spinner-border text-primary mb-2" role="status" style="width: 2rem; height: 2rem; color: #00796B !important;"></div>
                            <div>Cargando matriz de permisos...</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.perm-check-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
}
.perm-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: #00796B;
}
.perm-checkbox:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}
.group-header-tr {
    background-color: #F1F5F9 !important;
    border-top: 2px solid #CBD5E1;
}
.group-header-td {
    font-weight: 700;
    color: #1E293B;
    text-transform: uppercase;
    font-size: 0.78rem;
    letter-spacing: 0.5px;
    padding: 0.75rem 1rem !important;
}
.module-name {
    font-weight: 600;
    color: #2D3748;
    font-size: 0.9rem;
}
.module-dir {
    font-size: 0.75rem;
    color: #718096;
    font-family: monospace;
    background: #EDF2F7;
    padding: 2px 6px;
    border-radius: 4px;
    display: inline-block;
    margin-top: 2px;
}
</style>

<script>
$(document).ready(function() {
    var matrixData = null;

    function showFeedback(text, isError) {
        var el = $('#saveFeedback');
        var txt = $('#saveFeedbackText');
        txt.text(text);
        if (isError) {
            el.find('.material-icons').text('error_outline').removeClass('text-success').addClass('text-danger');
        } else {
            el.find('.material-icons').text('check_circle').removeClass('text-danger').addClass('text-success');
        }
        el.css('opacity', '1');
        setTimeout(function() {
            el.css('opacity', '0');
        }, 2000);
    }

    function renderMatrix() {
        if (!matrixData) return;

        var roles = matrixData.roles;
        var modules = matrixData.modules;
        var assigned = matrixData.assigned || {};

        // 1. Renderizar encabezados de roles
        var headerTr = $('#matrixHeaderRow');
        headerTr.empty();
        headerTr.append('<th style="min-width: 320px; font-weight: 600; color: #4A5568;">Módulo / Funcionalidad</th>');

        roles.forEach(function(role) {
            var badgeAdmin = role.id === 1 ? '<br><span class="badge badge-success px-2 py-1 mt-1" style="font-size: 0.68rem; font-weight: 500; background-color: #10B981;">Total</span>' : '';
            var th = $('<th class="text-center" style="min-width: 130px; font-weight: 600; color: #4A5568;">' +
                       '<span>' + role.nombre + '</span>' +
                       badgeAdmin +
                       '</th>');
            headerTr.append(th);
        });

        // 2. Renderizar filas de módulos agrupados
        var tbody = $('#matrixBody');
        tbody.empty();

        Object.keys(modules).forEach(function(groupName) {
            var groupItems = modules[groupName];
            var colCount = roles.length + 1;

            // Encabezado de grupo
            var grpRow = $('<tr class="group-header-tr">' +
                           '<td colspan="' + colCount + '" class="group-header-td">' +
                           '<span class="material-icons mr-1 align-middle" style="font-size: 1.1rem; color: #00796B;">folder</span> ' +
                           groupName + ' (' + groupItems.length + ')' +
                           '</td>' +
                           '</tr>');
            tbody.append(grpRow);

            // Filas de cada módulo
            groupItems.forEach(function(mod) {
                var tr = $('<tr class="module-row" data-name="' + mod.name.toLowerCase() + '" data-dir="' + mod.dir.toLowerCase() + '"></tr>');
                
                // Celda de nombre y ruta
                var infoTd = $('<td>' +
                               '<div class="module-name">' + mod.name + '</div>' +
                               '<span class="module-dir">' + mod.dir + '</span>' +
                               '</td>');
                tr.append(infoTd);

                // Checkbox por cada rol
                roles.forEach(function(role) {
                    var isChecked = false;
                    var isDisabled = (role.id === 1); // Admin siempre tiene acceso

                    if (role.id === 1) {
                        isChecked = true;
                    } else {
                        var rolePerms = assigned[role.id] || {};
                        isChecked = !!rolePerms[mod.dir];
                        // Compatibilidad con alias
                        if (!isChecked && mod.alias && rolePerms[mod.alias]) {
                            isChecked = true;
                        }
                    }

                    var checkTd = $('<td class="text-center">' +
                                    '<div class="perm-check-wrapper">' +
                                    '<input type="checkbox" class="perm-checkbox" ' +
                                    'data-role="' + role.id + '" ' +
                                    'data-dir="' + mod.dir + '" ' +
                                    (isChecked ? 'checked ' : '') +
                                    (isDisabled ? 'disabled title="Acceso total por ser Administrador"' : '') +
                                    '>' +
                                    '</div>' +
                                    '</td>');
                    tr.append(checkTd);
                });

                tbody.append(tr);
            });
        });
    }

    function loadMatrix() {
        $.getJSON('ajax/roles_matrix_handler.php', { action: 'get' }, function(resp) {
            if (resp && resp.success) {
                matrixData = resp;
                renderMatrix();
            } else {
                $('#matrixBody').html('<tr><td colspan="6" class="text-center text-danger py-4">Error al cargar matriz.</td></tr>');
            }
        }).fail(function() {
            $('#matrixBody').html('<tr><td colspan="6" class="text-center text-danger py-4">Fallo de conexión con el servidor.</td></tr>');
        });
    }

    // Toggle interactivo con guardado en tiempo real
    $(document).on('change', '.perm-checkbox', function() {
        var chk = $(this);
        var roleId = chk.data('role');
        var dir = chk.data('dir');
        var state = chk.is(':checked') ? 1 : 0;

        chk.prop('disabled', true);

        $.post('ajax/roles_matrix_handler.php', {
            action: 'toggle',
            role_id: roleId,
            dir: dir,
            state: state
        }, function(resp) {
            chk.prop('disabled', false);
            if (resp && resp.success) {
                // Actualizar estado en memoria
                if (!matrixData.assigned[roleId]) matrixData.assigned[roleId] = {};
                if (state === 1) {
                    matrixData.assigned[roleId][dir] = true;
                } else {
                    delete matrixData.assigned[roleId][dir];
                }
                showFeedback('Permiso ' + (state === 1 ? 'concedido' : 'revocado'), false);
            } else {
                chk.prop('checked', !chk.is(':checked'));
                showFeedback(resp.message || 'Error al guardar', true);
            }
        }, 'json').fail(function() {
            chk.prop('disabled', false);
            chk.prop('checked', !chk.is(':checked'));
            showFeedback('Error de comunicación', true);
        });
    });

    // Filtro instantáneo
    $('#filtroModulo').on('input', function() {
        var query = $(this).val().toLowerCase().trim();
        if (query === '') {
            $('.module-row').show();
            $('.group-header-tr').show();
            return;
        }

        $('.module-row').each(function() {
            var row = $(this);
            var name = row.data('name') || '';
            var dir = row.data('dir') || '';
            if (name.indexOf(query) !== -1 || dir.indexOf(query) !== -1) {
                row.show();
            } else {
                row.hide();
            }
        });

        // Ocultar cabeceras de grupos vacíos
        $('.group-header-tr').each(function() {
            var header = $(this);
            var visibleRows = header.nextUntil('.group-header-tr', '.module-row:visible');
            if (visibleRows.length > 0) {
                header.show();
            } else {
                header.hide();
            }
        });
    });

    $('#btnReloadMatrix').on('click', function() {
        loadMatrix();
    });

    // Iniciar carga
    loadMatrix();
});
</script>
