$(document).ready(function () {

    /* ---------------------------------------------------------------
       Fechas por defecto (01-enero del año actual → hoy)
    --------------------------------------------------------------- */
    var hoy  = new Date();
    var mes  = String(hoy.getMonth() + 1).padStart(2, '0');
    var dia  = String(hoy.getDate()).padStart(2, '0');
    var ano  = hoy.getFullYear();
    $('#emv_fecha_ini').val(ano + '-01-01');
    $('#emv_fecha_fin').val(ano + '-' + mes + '-' + dia);

    /* ---------------------------------------------------------------
       Poblar selects de proveedores y destino (breeders)
    --------------------------------------------------------------- */
    $.post('ajax/crud_emv.php', { opcion: 'breeders' }, function (data) {
        var $selP = $('#emv_proveedor');
        var $selD = $('#emv_destino');
        $selP.empty().append('<option value="">— seleccione —</option>');
        $selD.empty().append('<option value="">— seleccione —</option>');
        $.each(data, function (i, item) {
            $selP.append($('<option>').val(item.id).text(item.nombre));
            $selD.append($('<option>').val(item.id).text(item.nombre));
        });
    }, 'json');

    /* ---------------------------------------------------------------
       Poblar select de variedades con filtro por flor y texto
    --------------------------------------------------------------- */
    var todasVariedades = [];

    function aplicarFiltroVariedad() {
        var flor  = $('#det_filtro_flor').val().toLowerCase();
        var texto = $('#det_variedad_busca').val().toLowerCase();
        var $sel  = $('#det_variedad');
        var valorActual = $sel.val();
        $sel.empty().append('<option value="">— seleccione —</option>');
        $.each(todasVariedades, function (i, item) {
            var coincideFlor  = !flor  || (item.codflor  || '').toLowerCase() === flor;
            var coincideTexto = !texto || item.nombre.toLowerCase().indexOf(texto) !== -1
                                       || (item.codigo || '').toLowerCase().indexOf(texto) !== -1;
            if (coincideFlor && coincideTexto) {
                $sel.append($('<option>').val(item.codigo).text(item.nombre));
            }
        });
        $sel.val(valorActual);
    }

    $.post('ajax/crud_emv.php', { opcion: 'variedades' }, function (data) {
        todasVariedades = data;
        // Poblar filtro de flores (valores únicos)
        var flores = {};
        $.each(data, function (i, item) {
            var cf = item.codflor || '';
            if (cf && !flores[cf]) {
                flores[cf] = true;
                $('#det_filtro_flor').append($('<option>').val(cf).text(cf));
            }
        });
        aplicarFiltroVariedad();
    }, 'json');

    $(document).on('change', '#det_filtro_flor', aplicarFiltroVariedad);
    $(document).on('input',  '#det_variedad_busca', aplicarFiltroVariedad);
    $(document).on('change', '#det_variedad', function () {
        $(this).removeClass('is-invalid is-valid');
    });

    /* ---------------------------------------------------------------
       Inicializar DataTable principal
    --------------------------------------------------------------- */
    var tableEmv = $('#tableEmv').DataTable({
        processing: true,
        ajax: {
            url: 'ajax/crud_emv.php',
            type: 'POST',
            data: function () {
                return {
                    opcion: '4',
                    fecha_ini: $('#emv_fecha_ini').val(),
                    fecha_fin: $('#emv_fecha_fin').val()
                };
            },
            dataSrc: ''
        },
        columns: [
            { data: 'id',        title: 'Id' },
            { data: 'fecha',     title: 'Fecha' },
            { data: 'maquila',   title: 'Maquila' },
            { data: 'proveedor', title: 'Proveedor' },
            { data: 'remision',  title: 'Remisión' },
            { data: 'destino',   title: 'Destino' },
            { data: 'material',  title: 'Material' },
            {
                data: null,
                title: 'Acciones',
                orderable: false,
                render: function (d, t, row) {
                    return '<button class="btn btn-sm btn-info btnEmvDetalles mr-1" data-id="' + row.id + '" title="Detalles">'
                         + '<i class="material-icons" style="font-size:16px;vertical-align:middle">list_alt</i></button>'
                         + '<button class="btn btn-sm btn-warning btnEmvEditar mr-1" data-id="' + row.id + '" title="Editar">'
                         + '<i class="material-icons" style="font-size:16px;vertical-align:middle">edit</i></button>'
                         + '<button class="btn btn-sm btn-secondary btnEmvImprimir mr-1" data-id="' + row.id + '" title="Imprimir">'
                         + '<i class="material-icons" style="font-size:16px;vertical-align:middle">print</i></button>'
                         + '<button class="btn btn-sm btn-danger btnEmvBorrar" data-id="' + row.id + '" title="Borrar">'
                         + '<i class="material-icons" style="font-size:16px;vertical-align:middle">delete</i></button>';
                }
            }
        ]
    });

    /* ---------------------------------------------------------------
       Función pública para recargar la tabla principal (usada por el botón Consultar)
    --------------------------------------------------------------- */
    window.emvListar = function () {
        tableEmv.ajax.reload(null, false);
    };

    /* ---------------------------------------------------------------
       Estado del formulario cabecera
    --------------------------------------------------------------- */
    var opcionEmv = '1';
    var nuevoEntradaId = null;

    function abrirModalCRUD(titulo, colorHeader) {
        $('#modalEmvCRUDTitle').text(titulo);
        $('#modalEmvCRUD .modal-header')
            .removeClass('bg-info bg-primary bg-warning')
            .addClass(colorHeader);
        $('#btnEmvVerDetalles').hide();
        nuevoEntradaId = null;
        $('#modalEmvCRUD').modal('show');
    }

    /* Nuevo */
    $('#btnEmvNuevo').on('click', function () {
        opcionEmv = '1';
        $('#formEmv')[0].reset();
        $('#emv_id').val('');
        abrirModalCRUD('Nueva Entrada de Material Vegetal', 'bg-info');
    });

    /* Guardar (insert / update) */
    $('#formEmv').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'ajax/crud_emv.php',
            type: 'POST',
            dataType: 'json',
            data: {
                opcion:     opcionEmv,
                id:         $('#emv_id').val(),
                fecha:      $('#emv_fecha').val(),
                maquila:    $('#emv_maquila').val(),
                proveedor:  $('#emv_proveedor').val(),
                remision:   $('#emv_remision').val(),
                destino:    $('#emv_destino').val(),
                material:   $('#emv_material').val()
            },
            success: function (data) {
                tableEmv.ajax.reload(null, false);
                if (opcionEmv === '1' && data.id) {
                    nuevoEntradaId = data.id;
                    $('#btnEmvVerDetalles')
                        .data('id', data.id)
                        .show();
                    $('#modalEmvCRUDTitle').text('Entrada #' + data.id + ' guardada');
                } else {
                    $('#modalEmvCRUD').modal('hide');
                }
            },
            error: function () {
                alert('Error al guardar. Revise la consola del servidor.');
            }
        });
    });

    /* Botón "Ir a Detalles" (aparece tras crear) */
    $('#btnEmvVerDetalles').on('click', function () {
        var id = $(this).data('id');
        $('#modalEmvCRUD').modal('hide');
        // pequeño retardo para que el modal se cierre primero
        setTimeout(function () { abrirDetalles(id); }, 350);
    });

    /* Editar */
    $(document).on('click', '.btnEmvEditar', function () {
        opcionEmv = '2';
        var tr  = $(this).closest('tr');
        var row = tableEmv.row(tr).data();
        $('#emv_id').val(row.id);
        $('#emv_fecha').val(row.fecha);
        $('#emv_maquila').val(row.maquila);
        $('#emv_proveedor').val(row.proveedor_id);
        $('#emv_remision').val(row.remision);
        $('#emv_destino').val(row.destino_id);
        $('#emv_material').val(row.material);
        abrirModalCRUD('Editar Entrada #' + row.id, 'bg-warning');
    });

    /* Borrar */
    $(document).on('click', '.btnEmvBorrar', function () {
        var id = $(this).data('id');
        if (!confirm('¿Eliminar la entrada #' + id + ' y todos sus detalles?')) return;
        $.post('ajax/crud_emv.php', { opcion: '3', id: id }, function () {
            tableEmv.ajax.reload(null, false);
        }, 'json');
    });

    /* Abrir modal de detalles */
    $(document).on('click', '.btnEmvDetalles', function () {
        abrirDetalles($(this).data('id'));
    });

    /* ---------------------------------------------------------------
       Imprimir entrada (media carta)
    --------------------------------------------------------------- */
    function emvEscaparHtml(valor) {
        return String(valor === null || valor === undefined ? '' : valor)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function emvFormatearFecha(fechaIso) {
        if (!fechaIso) return '';
        var partes = String(fechaIso).split('-');
        if (partes.length !== 3) return fechaIso;
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }

    function emvConsecutivo(id) {
        return String(id || '').padStart(6, '0');
    }

    function emvNumero(valor) {
        var n = parseInt(valor, 10);
        return isNaN(n) ? 0 : n;
    }

    function emvConstruirFilasDetalle(detalles) {
        if (!detalles || detalles.length === 0) {
            return '<tr><td colspan="7" style="text-align:center;color:#6b7280;">Sin detalles registrados</td></tr>';
        }

        var html = '';
        $.each(detalles, function (_, d) {
            html += '<tr>'
                 + '<td>' + emvEscaparHtml(d.variedad || '') + '</td>'
                 + '<td class="num">' + emvNumero(d.cantidad_recibida) + '</td>'
                 + '<td class="num">' + emvNumero(d.facturado) + '</td>'
                  + '<td class="num">' + emvNumero(d.excedente) + '</td>'
                 + '<td class="num">' + emvNumero(d.obsequio) + '</td>'
                 + '<td class="num">' + emvNumero(d.adicional) + '</td>'
                 + '<td>' + (String(d.raiz) === '1' ? 'Si' : 'No') + '</td>'
                 + '</tr>';
        });
        return html;
    }

    function emvTotales(detalles) {
        var t = {
            cantidad_recibida: 0,
            facturado: 0,
            reposicion: 0,
            excedente: 0,
            obsequio: 0,
            adicional: 0
        };

        $.each(detalles || [], function (_, d) {
            t.cantidad_recibida += emvNumero(d.cantidad_recibida);
            t.facturado += emvNumero(d.facturado);
            t.reposicion += emvNumero(d.reposicion);
            t.excedente += emvNumero(d.excedente);
            t.obsequio += emvNumero(d.obsequio);
            t.adicional += emvNumero(d.adicional);
        });

        return t;
    }

    function emvHtmlImpresion(cabecera, detalles) {
        var tot = emvTotales(detalles);
        return '<!doctype html>'
            + '<html><head><meta charset="utf-8"><title>Entrada Material Vegetal #' + emvEscaparHtml(cabecera.id) + '</title>'
            + '<style>'
            + '@page{size:5.5in 8.5in portrait;margin:0.35in;}'
            + 'html,body{margin:0;padding:0;font-family:Arial,sans-serif;color:#111827;}'
            + '.sheet{width:100%;height:100%;}'
            + '.head{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid #0f766e;padding-bottom:8px;margin-bottom:10px;}'
            + '.title{font-size:18px;font-weight:700;letter-spacing:.3px;color:#0f766e;}'
            + '.subtitle{font-size:11px;color:#475569;margin-top:2px;}'
            + '.consecutivo{border:2px solid #0f766e;border-radius:8px;padding:6px 10px;text-align:center;min-width:140px;}'
            + '.consecutivo .lbl{font-size:10px;color:#334155;text-transform:uppercase;letter-spacing:.6px;}'
            + '.consecutivo .val{font-size:22px;font-weight:800;color:#0f172a;line-height:1.1;}'
            + '.grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;margin-bottom:10px;}'
            + '.field{border:1px solid #cbd5e1;border-radius:6px;padding:6px;background:#f8fafc;}'
            + '.field .k{font-size:9px;text-transform:uppercase;letter-spacing:.5px;color:#64748b;}'
            + '.field .v{font-size:12px;font-weight:600;color:#0f172a;margin-top:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}'
            + 'table{width:100%;border-collapse:collapse;font-size:10px;}'
            + 'th,td{border:1px solid #cbd5e1;padding:4px 5px;}'
            + 'th{background:#ecfeff;color:#155e75;font-weight:700;text-transform:uppercase;font-size:9px;}'
            + 'td.num{text-align:right;font-variant-numeric:tabular-nums;}'
            + 'tfoot td{font-weight:700;background:#f1f5f9;}'
            + '.footer{margin-top:20px;display:flex;justify-content:flex-end;align-items:flex-end;min-height:72px;}'
            + '.signature{min-width:260px;text-align:center;color:#334155;font-size:10px;}'
            + '.signature-line{border-top:1px solid #64748b;height:1px;margin:0 8px 8px;}'
            + '.signature-label{font-size:9px;letter-spacing:.3px;text-transform:uppercase;}'
            + '</style></head><body>'
            + '<div class="sheet">'
            + '<div class="head">'
            + '<div><div class="title">Entrada de Material Vegetal</div><div class="subtitle">Formato de recepcion para control interno</div></div>'
            + '<div class="consecutivo"><div class="lbl">Numero de Entrada</div><div class="val">' + emvEscaparHtml(emvConsecutivo(cabecera.id)) + '</div></div>'
            + '</div>'
            + '<div class="grid">'
            + '<div class="field"><div class="k">Fecha</div><div class="v">' + emvEscaparHtml(emvFormatearFecha(cabecera.fecha)) + '</div></div>'
            + '<div class="field"><div class="k">Maquila</div><div class="v">' + emvEscaparHtml(cabecera.maquila || '') + '</div></div>'
            + '<div class="field"><div class="k">Proveedor</div><div class="v">' + emvEscaparHtml(cabecera.proveedor || '') + '</div></div>'
            + '<div class="field"><div class="k">Destino</div><div class="v">' + emvEscaparHtml(cabecera.destino || '') + '</div></div>'
            + '<div class="field"><div class="k">Remision</div><div class="v">' + emvEscaparHtml(cabecera.remision || '') + '</div></div>'
            + '<div class="field"><div class="k">Material</div><div class="v">' + emvEscaparHtml(cabecera.material || '') + '</div></div>'
            + '<div class="field"><div class="k">Total Variedades</div><div class="v">' + (detalles ? detalles.length : 0) + '</div></div>'
            + '<div class="field"><div class="k">Total Recibido</div><div class="v">' + tot.cantidad_recibida + '</div></div>'
            + '</div>'
            + '<table>'
            + '<thead><tr><th>Variedad</th><th>Cant. Recibida</th><th>Facturado</th><th>Excedente</th><th>Obsequio</th><th>Adicional</th><th>Raiz</th></tr></thead>'
            + '<tbody>' + emvConstruirFilasDetalle(detalles) + '</tbody>'
            + '<tfoot><tr>'
            + '<td style="text-align:right;">Totales</td>'
            + '<td class="num">' + tot.cantidad_recibida + '</td>'
            + '<td class="num">' + tot.facturado + '</td>'
            + '<td class="num">' + tot.excedente + '</td>'
            + '<td class="num">' + tot.obsequio + '</td>'
            + '<td class="num">' + tot.adicional + '</td>'
            + '<td></td>'
            + '</tr></tfoot>'
            + '</table>'
            + '<div class="footer"><div class="signature"><div class="signature-line"></div><div class="signature-label">Firma y nombre de quien recibe</div></div></div>'
            + '</div></body></html>';
    }

    function emvImprimir(cabecera, detalles) {
        var html = emvHtmlImpresion(cabecera, detalles);
        var win = window.open('', '_blank', 'width=1100,height=700');
        if (!win) {
            alert('No fue posible abrir la ventana de impresion. Verifique el bloqueador de ventanas emergentes.');
            return;
        }
        win.document.open();
        win.document.write(html);
        win.document.close();
        win.focus();
        setTimeout(function () {
            win.print();
            win.close();
        }, 350);
    }

    $(document).on('click', '.btnEmvImprimir', function () {
        var row = tableEmv.row($(this).closest('tr')).data();
        if (!row) {
            row = tableEmv.row($(this).parents('tr')).data();
        }
        if (!row || !row.id) {
            alert('No fue posible obtener la entrada a imprimir.');
            return;
        }

        $.ajax({
            url: 'ajax/crud_emv.php',
            type: 'POST',
            dataType: 'json',
            data: { opcion: '5', entrada_id: row.id },
            success: function (detalles) {
                emvImprimir(row, detalles || []);
            },
            error: function () {
                alert('Error al consultar el detalle para impresion.');
            }
        });
    });

    /* ---------------------------------------------------------------
       DataTable de detalles (se inicializa la primera vez)
    --------------------------------------------------------------- */
    var tableDetalles = null;

    function abrirDetalles(id) {
        $('#det_entrada_id').val(id);
        $('#modalEmvDetallesTitle').text('Detalles — Entrada #' + id);
        $('#formEmvDetalle')[0].reset();
        resetSumaCampos();

        if (tableDetalles && $.fn.DataTable.isDataTable('#tableEmvDetalles')) {
            tableDetalles.ajax.reload(null, false);
        } else {
            tableDetalles = $('#tableEmvDetalles').DataTable({
                processing: true,
                ajax: {
                    url: 'ajax/crud_emv.php',
                    type: 'POST',
                    data: function () {
                        return { opcion: '5', entrada_id: $('#det_entrada_id').val() };
                    },
                    dataSrc: ''
                },
                columns: [
                    { data: 'id',                title: 'Id' },
                    { data: 'variedad',          title: 'Variedad' },
                    { data: 'cantidad_recibida', title: 'Cant. Recibida' },
                    { data: 'facturado',         title: 'Facturado' },
                    { data: 'reposicion',        title: 'Reposición' },
                    { data: 'excedente',         title: 'Excedente' },
                    { data: 'obsequio',          title: 'Obsequio' },
                    { data: 'adicional',         title: 'Adicional' },
                    {
                        data: 'raiz', title: 'Raíz',
                        render: function (v) { return v == 1 ? 'Con raíz' : 'Sin raíz'; }
                    },
                    { data: 'observacion', title: 'Observación', defaultContent: '' },
                    {
                        data: null, title: 'Acción', orderable: false,
                        render: function (d, t, row) {
                            return '<button class="btn btn-sm btn-danger btnEmvBorrarDet" data-id="' + row.id + '" title="Eliminar">'
                                 + '<i class="material-icons" style="font-size:16px;vertical-align:middle">delete</i></button>';
                        }
                    }
                ]
            });
        }

        $('#modalEmvDetalles').modal('show');
    }

    /* ---------------------------------------------------------------
       Auto-cálculo de cantidad_recibida
    --------------------------------------------------------------- */
    function calcularTotal() {
        var total = (parseInt($('#det_facturado').val())  || 0)
                  + (parseInt($('#det_reposicion').val()) || 0)
                  + (parseInt($('#det_excedente').val())  || 0)
                  + (parseInt($('#det_obsequio').val())   || 0)
                  + (parseInt($('#det_adicional').val())  || 0);
        $('#det_cantidad_recibida').val(total);
    }

    function resetSumaCampos() {
        $('#det_facturado, #det_reposicion, #det_excedente, #det_obsequio, #det_adicional').val(0);
        $('#det_cantidad_recibida').val(0);
        $('#det_raiz').prop('checked', false);
        $('#det_filtro_flor').val('');
        $('#det_variedad_busca').val('');
        aplicarFiltroVariedad();
    }

    $(document).on('input', '.emv-suma', calcularTotal);

    /* ---------------------------------------------------------------
       Guardar nuevo detalle
    --------------------------------------------------------------- */
    $('#formEmvDetalle').on('submit', function (e) {
        e.preventDefault();

        var facturado  = parseInt($('#det_facturado').val())  || 0;
        var reposicion = parseInt($('#det_reposicion').val()) || 0;
        var excedente  = parseInt($('#det_excedente').val())  || 0;
        var obsequio   = parseInt($('#det_obsequio').val())   || 0;
        var adicional  = parseInt($('#det_adicional').val())  || 0;
        var suma       = facturado + reposicion + excedente + obsequio + adicional;
        var recibida   = parseInt($('#det_cantidad_recibida').val()) || 0;

        if (recibida !== suma) {
            alert('La cantidad recibida (' + recibida + ') no coincide con la suma (' + suma + ').\n'
                + 'Revise los valores de facturado, reposición, excedente, obsequio y adicional.');
            return;
        }

        var codigoVariedad = $('#det_variedad').val();
        var variedadValida  = codigoVariedad !== '' &&
            todasVariedades.some(function (v) { return v.codigo === codigoVariedad; });

        if (!variedadValida) {
            $('#det_variedad').addClass('is-invalid').focus();
            return;
        }
        $('#det_variedad').removeClass('is-invalid').addClass('is-valid');

        $.ajax({
            url: 'ajax/crud_emv.php',
            type: 'POST',
            dataType: 'json',
            data: {
                opcion:     '7',
                entrada_id: $('#det_entrada_id').val(),
                variedad:   codigoVariedad,
                facturado:  facturado,
                reposicion: reposicion,
                excedente:  excedente,
                obsequio:   obsequio,
                adicional:  adicional,
                raiz:       $('#det_raiz').is(':checked') ? 1 : 0,
                observacion: $('#det_observacion').val()
            },
            success: function () {
                if (tableDetalles) tableDetalles.ajax.reload(null, false);
                $('#formEmvDetalle')[0].reset();
                resetSumaCampos();
                $('#det_variedad').removeClass('is-valid is-invalid');
                $('#det_variedad').focus();
            },
            error: function () {
                alert('Error al guardar el detalle.');
            }
        });
    });

    /* ---------------------------------------------------------------
       Borrar detalle
    --------------------------------------------------------------- */
    $(document).on('click', '.btnEmvBorrarDet', function () {
        var id = $(this).data('id');
        if (!confirm('¿Eliminar el detalle #' + id + '?')) return;
        $.post('ajax/crud_emv.php', { opcion: '6', id: id }, function () {
            if (tableDetalles) tableDetalles.ajax.reload(null, false);
        }, 'json');
    });

});
