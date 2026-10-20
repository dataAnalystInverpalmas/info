/**
 * Tika Calendario CRUD - Interacciones y DataTables
 */
$(document).ready(function () {
    var tabla = null;

    // 1. Inicializar DataTable
    function initDataTable() {
        tabla = $('#tablaTikaCalendario').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: 'ajax/tika_calendario.php',
                type: 'GET',
                data: function (d) {
                    d.action = 'listar';
                    d.anio = $('#filtroAnio').val();
                    d.modelo_tika_id = $('#filtroEstadoModelo').val();
                },
                dataSrc: 'data'
            },
            columns: [
                {
                    data: 'semana',
                    render: function (val) {
                        return '<span class="badge-semana">' + val + '</span>';
                    }
                },
                {
                    data: 'anio',
                    render: function (val) {
                        return '<strong class="text-dark">' + val + '</strong>';
                    }
                },
                {
                    data: 'numero_semana',
                    render: function (val) {
                        return '<span class="badge badge-light border text-muted px-2 py-1">Sem ' + val + '</span>';
                    }
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        var ini = row.fecha_inicio ? row.fecha_inicio : '—';
                        var fin = row.fecha_fin ? row.fecha_fin : '—';
                        if (!row.fecha_inicio && !row.fecha_fin) {
                            return '<span class="text-muted small">Sin fechas calculadas</span>';
                        }
                        return '<span class="text-secondary small font-weight-bold">' + ini + '</span>' +
                               '<span class="text-muted mx-1">al</span>' +
                               '<span class="text-secondary small font-weight-bold">' + fin + '</span>';
                    }
                },
                {
                    data: 'modelo_tika_id',
                    render: function (val, type, row) {
                        if (val !== null && val !== '' && val !== undefined) {
                            return '<span class="badge-modelo">' +
                                   '<span class="material-icons" style="font-size:14px;">check_circle</span> Modelo #' + val +
                                   '</span>';
                        }
                        return '<span class="badge-sin-modelo">Sin modelo</span>';
                    }
                },
                {
                    data: 'observaciones',
                    render: function (val) {
                        if (!val) return '<span class="text-muted small">—</span>';
                        return '<span class="text-dark small" title="' + val + '">' + val + '</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        return '<div class="btn-group btn-group-sm" role="group">' +
                               '<button type="button" class="btn btn-outline-primary action-btn btn-edit-sem" title="Editar">' +
                               '<span class="material-icons" style="font-size:16px;">edit</span>' +
                               '</button>' +
                               '<button type="button" class="btn btn-outline-danger action-btn btn-del-sem" title="Eliminar">' +
                               '<span class="material-icons" style="font-size:16px;">delete</span>' +
                               '</button>' +
                               '</div>';
                    }
                }
            ],
            order: [[0, 'asc']],
            pageLength: 25,
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ semanas",
                info: "Mostrando _START_ a _END_ de _TOTAL_ semanas",
                infoEmpty: "Mostrando 0 semanas",
                infoFiltered: "(filtrado de _MAX_ registros en total)",
                zeroRecords: "No se encontraron semanas que coincidan con la búsqueda",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });
    }

    initDataTable();

    // 2. Filtros dinámicos
    $('#filtroAnio, #filtroEstadoModelo').on('change', function () {
        if (tabla) tabla.ajax.reload();
    });

    // 3. Actualizar KPIs y listas de años
    function recargarKpisYAnios() {
        $.getJSON('ajax/tika_calendario.php', { action: 'kpis' }, function (res) {
            if (res && res.success) {
                var k = res.kpis;
                $('#kpiTotalSemanas').text(Number(k.total_semanas).toLocaleString());
                $('#kpiTotalAnios').text(k.total_anios);
                $('#kpiConModelo').text(Number(k.con_modelo).toLocaleString());
                $('#kpiSinModelo').text(Number(k.sin_modelo).toLocaleString());

                // Actualizar selects de años si hay nuevos años
                var anioActualFiltro = $('#filtroAnio').val();
                var $filtroAnio = $('#filtroAnio');
                var $asigAnio = $('#asigAnio');

                $filtroAnio.empty().append('<option value="">Todos los años</option>');
                $asigAnio.empty().append('<option value="">Seleccione un año...</option>');

                res.anios.forEach(function (a) {
                    $filtroAnio.append('<option value="' + a + '">' + a + '</option>');
                    $asigAnio.append('<option value="' + a + '">Año ' + a + '</option>');
                });

                $filtroAnio.val(anioActualFiltro);
            }
        });
    }

    // 4. Modal Generar Año - Ayudante interactivo de prefijo y rango
    function actualizarAyudaAnio() {
        var anioVal = $('#genAnio').val();
        if (anioVal && anioVal.length === 4) {
            var yy = anioVal.substring(2);
            $('#genAnioHelp').html('Prefijo YY resultante: <strong class="text-success">' + yy + '</strong>');
            $('#lbl52Range').text('(ej. ' + yy + '01 a ' + yy + '52)');
            $('#lbl53Range').text('(ej. ' + yy + '01 a ' + yy + '53)');
        } else {
            $('#genAnioHelp').text('Prefijo YY resultante: --');
        }
    }

    $('#genAnio').on('input change', actualizarAyudaAnio);

    // 5. Abrir Modal Generar Año
    $('#btnNuevoAnio').on('click', function () {
        $('#formGenerarAnio')[0].reset();
        var siguienteAnio = new Date().getFullYear() + 1;
        $('#genAnio').val(siguienteAnio);
        actualizarAyudaAnio();
        $('#modalGenerarAnio').modal('show');
    });

    // 6. Submit Generar Año
    $('#formGenerarAnio').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#btnSubmitGenerarAnio');
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Generando...');

        $.post('ajax/tika_calendario.php', $(this).serialize() + '&action=generar_anio', function (res) {
            $btn.prop('disabled', false).html('<span class="material-icons mr-1 align-middle" style="font-size: 1.1rem;">save</span> Generar Semanas');
            if (res && res.success) {
                $('#modalGenerarAnio').modal('hide');
                alert('¡Éxito! Se generaron/procesaron ' + res.total + ' semanas para el año ' + res.anio + ' (Nuevas: ' + res.insertados + ', Actualizadas: ' + res.actualizados + ').');
                tabla.ajax.reload();
                recargarKpisYAnios();
            } else {
                alert('Error al generar año: ' + (res.error || 'Ocurrió un error inesperado'));
            }
        }, 'json').fail(function () {
            $btn.prop('disabled', false).html('<span class="material-icons mr-1 align-middle" style="font-size: 1.1rem;">save</span> Generar Semanas');
            alert('Error de conexión con el servidor al generar año.');
        });
    });

    // 7. Abrir Modal Nueva Semana
    $('#btnNuevaSemana').on('click', function () {
        $('#formSemana')[0].reset();
        $('#semId').val('');
        $('#modalSemanaTitulo').text('Crear Nueva Semana');
        $('#semSemana').prop('readonly', false);
        $('#modalSemana').modal('show');
    });

    // 8. Editar Semana
    $('#tablaTikaCalendario tbody').on('click', '.btn-edit-sem', function () {
        var rowData = tabla.row($(this).closest('tr')).data();
        if (!rowData) return;

        $('#formSemana')[0].reset();
        $('#semId').val(rowData.id);
        $('#semSemana').val(rowData.semana).prop('readonly', true);
        $('#semAnio').val(rowData.anio);
        $('#semModeloId').val(rowData.modelo_tika_id !== null ? rowData.modelo_tika_id : '');
        $('#semFechaInicio').val(rowData.fecha_inicio || '');
        $('#semFechaFin').val(rowData.fecha_fin || '');
        $('#semObservaciones').val(rowData.observaciones || '');

        $('#modalSemanaTitulo').text('Editar Semana ' + rowData.semana);
        $('#modalSemana').modal('show');
    });

    // 9. Guardar Semana
    $('#formSemana').on('submit', function (e) {
        e.preventDefault();
        $.post('ajax/tika_calendario.php', $(this).serialize() + '&action=guardar', function (res) {
            if (res && res.success) {
                $('#modalSemana').modal('hide');
                tabla.ajax.reload(null, false);
                recargarKpisYAnios();
            } else {
                alert('Error al guardar: ' + (res.error || 'No fue posible guardar'));
            }
        }, 'json').fail(function () {
            alert('Error de conexión con el servidor.');
        });
    });

    // 10. Eliminar Semana
    $('#tablaTikaCalendario tbody').on('click', '.btn-del-sem', function () {
        var rowData = tabla.row($(this).closest('tr')).data();
        if (!rowData) return;

        if (confirm('¿Está seguro de eliminar la semana ' + rowData.semana + '?')) {
            $.post('ajax/tika_calendario.php', { action: 'eliminar_semana', id: rowData.id }, function (res) {
                if (res && res.success) {
                    tabla.ajax.reload(null, false);
                    recargarKpisYAnios();
                } else {
                    alert('Error al eliminar semana: ' + (res.error || 'No se pudo eliminar'));
                }
            }, 'json');
        }
    });

    // 11. Modal Asignar Modelo por Año
    $('#btnAsignarAnio').on('click', function () {
        $('#formAsignarAnio')[0].reset();
        var filtroActual = $('#filtroAnio').val();
        if (filtroActual) {
            $('#asigAnio').val(filtroActual);
        }
        $('#modalAsignarAnio').modal('show');
    });

    // 12. Submit Asignar Modelo por Año
    $('#formAsignarAnio').on('submit', function (e) {
        e.preventDefault();
        var anio = $('#asigAnio').val();
        var modelo = $('#asigModeloId').val();
        var textoMod = modelo ? 'Modelo #' + modelo : 'SIN MODELO';

        if (!confirm('¿Confirma asignar ' + textoMod + ' a TODAS las semanas del año ' + anio + '?')) {
            return;
        }

        $.post('ajax/tika_calendario.php', $(this).serialize() + '&action=asignar_por_anio', function (res) {
            if (res && res.success) {
                $('#modalAsignarAnio').modal('hide');
                alert('¡Listo! Se actualizaron ' + res.afectadas + ' semanas del año ' + anio + '.');
                tabla.ajax.reload();
                recargarKpisYAnios();
            } else {
                alert('Error al asignar: ' + (res.error || 'No se pudo actualizar'));
            }
        }, 'json');
    });

    // 13. Sincronizar desde Fechas (dates)
    $('#btnSyncDates').on('click', function () {
        if (!confirm('¿Desea sincronizar / importar semanas desde la tabla dates (rango 2401 a 2952)?')) {
            return;
        }
        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-1"></span> Sincronizando...');

        $.post('ajax/tika_calendario.php', { action: 'sincronizar_dates', desde: 2401, hasta: 2952 }, function (res) {
            $btn.prop('disabled', false).html('<span class="material-icons mr-1" style="font-size: 1rem;">sync</span> Sincronizar dates');
            if (res && res.success) {
                alert('Sincronización completa. Semanas procesadas: ' + res.filas_afectadas);
                tabla.ajax.reload();
                recargarKpisYAnios();
            } else {
                alert('Error al sincronizar: ' + (res.error || 'Fallo'));
            }
        }, 'json').fail(function () {
            $btn.prop('disabled', false).html('<span class="material-icons mr-1" style="font-size: 1rem;">sync</span> Sincronizar dates');
            alert('Error de conexión con el servidor.');
        });
    });
});
