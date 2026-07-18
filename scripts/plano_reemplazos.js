$(document).ready(function () {
    var dt = $('#tablaPlanoReemplazos').DataTable({
        pageLength: 25,
        order: [[1, 'asc']],
        scrollX: true,
        autoWidth: false,
        processing: true,
        serverSide: false
    });

    // Obtener parámetros de filtro actuales
    function getFilterParams() {
        return {
            finca: $('#pr_filter_finca').val() || '',
            bloque: $('#pr_filter_bloque').val() || '',
            tabla: $('#pr_filter_tabla').val() || '',
            nave: $('#pr_filter_nave').val() || '',
            tipo_siembra: $('#pr_filter_tipo_siembra').val() || '',
            variedad: $('#pr_filter_variedad').val() || '',
            cosecha: $('#pr_filter_cosecha').val() || '',
            semana_siembra: $('#pr_filter_semana_siembra').val() || ''
        };
    }

    // Recargar DataTable con filtros
    function reloadTableWithFilters() {
        var filters = getFilterParams();
        var params = new URLSearchParams();
        if (filters.finca) params.append('finca', filters.finca);
        if (filters.bloque) params.append('bloque', filters.bloque);
        if (filters.tabla) params.append('tabla', filters.tabla);
        if (filters.nave) params.append('nave', filters.nave);
        if (filters.tipo_siembra) params.append('tipo_siembra', filters.tipo_siembra);
        if (filters.variedad) params.append('variedad', filters.variedad);
        if (filters.cosecha) params.append('cosecha', filters.cosecha);
        if (filters.semana_siembra) params.append('semana_siembra', filters.semana_siembra);

        var url = 'ajax/plano_reemplazos.php?accion=listar';
        if (params.toString()) {
            url += '&' + params.toString();
        }

        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res && res.data) {
                    dt.clear();
                    if (Array.isArray(res.data)) {
                        res.data.forEach(function (row) {
                                dt.row.add([
                                    htmlEscape(row.finca || ''),
                                    htmlEscape(row.bloque || ''),
                                    htmlEscape(row.tabla || ''),
                                    htmlEscape(row.nave || ''),
                                    htmlEscape(row.cama || ''),
                                    htmlEscape(row.fecha_siembra || ''),
                                    htmlEscape(row.semana_siembra || ''),
                                    htmlEscape(row.origen || ''),
                                    htmlEscape(row.tipo_siembra || ''),
                                    htmlEscape(row.variedad_original || ''),
                                    htmlEscape(row.cosecha_original || ''),
                                    htmlEscape(row.variedad_reem || ''),
                                    htmlEscape(row.cosecha_reem || ''),
                                    htmlEscape(row.plantas || '')
                                ]);
                        });
                    }
                    dt.draw();
                }
            }
        });
    }

    // Función auxiliar para escapar HTML
    function htmlEscape(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Event handlers para los filtros
    $('#pr_filter_finca, #pr_filter_bloque, #pr_filter_tabla, #pr_filter_nave, #pr_filter_tipo_siembra, #pr_filter_variedad, #pr_filter_cosecha, #pr_filter_semana_siembra').on('change', function () {
        reloadTableWithFilters();
    });

    // Limpiar filtros
    $('#pr_btn_clear_filters').on('click', function () {
        $('#pr_filter_finca, #pr_filter_bloque, #pr_filter_tabla, #pr_filter_nave, #pr_filter_tipo_siembra, #pr_filter_variedad, #pr_filter_cosecha, #pr_filter_semana_siembra').val('');
        reloadTableWithFilters();
    });
});
