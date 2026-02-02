<?php
//llamar conexion
include ('../funciones/conexion.php');
//funciones personalizadas
require_once '../dist/Barcode39.php';
//carbon
require "../vendor/autoload.php";
use Carbon\Carbon;

$di = Carbon::now();
$de = Carbon::now();
$dateEnd = new Carbon();
$dateIni = new Carbon();

// Semana ISO: lunes a domingo
$dateIni = $di->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
$dateEnd = $de->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');

// Variables de filtro
$tipo = $_POST['xtipo'] ?? '';
$finca = $_POST['xfinca'] ?? '';

if(isset($_POST['buscar'])){
    $dateIni = $_POST['dateIni'] ?? $dateIni;
    $dateEnd = $_POST['dateEnd'] ?? $dateEnd;
}

// Consultas para combos
$sqlTIPO = "SELECT DISTINCT tipo FROM arrangements";
$resT = $conexion->query($sqlTIPO);

$sqlFINCA = "SELECT DISTINCT finca FROM plane";
$resF = $conexion->query($sqlFINCA);

// Prefijo para AJAX:
// - Si se abre directo /views/test.php -> '../ajax/...'
// - Si se incluye desde /home.php -> 'ajax/...'
$ajaxPrefix = (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/views/') !== false) ? '../' : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Aplicaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        @media screen, print {
            td, tr, th {
                border: 1px solid black;
                padding: 4px;
                font-size: 12px;
            }
            .summary-table th {
                background-color: #f8f9fa;
                font-weight: bold;
            }
        }
        @media print {
            /* No imprimir filtros/botones */
            .no-print {
                display: none !important;
            }
            .card-header {
                display: block !important;
            }
            .card-header h4,
            .card-header h5,
            .card-header h6,
            .card-header p {
                margin: 5px 0;
            }
        }
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .table-container {
            max-height: 600px;
            overflow-y: auto;
        }
        .saltoDePagina {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-3">
        <!-- Formulario de Filtros -->
        <div class="card no-print">
            <div class="card-header">
                <form id="filtroForm" class="form-inline" method="post" enctype="multipart/form-data">
                    <div class="row g-3 align-items-center">
                        <!-- Finca -->
                        <div class="col-auto">
                            <select name="xfinca" id="xfinca" class="form-control select2" style="min-width: 200px;">
                                <option value="">Todas las Fincas</option>
                                <?php while($rf = $resF->fetch_object()): ?>
                                    <option value="<?= $rf->finca ?>" <?= ($rf->finca == $finca) ? 'selected' : '' ?>>
                                        <?= $rf->finca ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Fecha Inicial -->
                        <div class="col-auto">
                            <input type="date" name="dateIni" id="dateIni" value="<?= $dateIni ?>" class="form-control">
                        </div>
                        
                        <!-- Fecha Final -->
                        <div class="col-auto">
                            <input type="date" name="dateEnd" id="dateEnd" value="<?= $dateEnd ?>" class="form-control">
                        </div>
                        
                        <!-- Tipo de Aplicación -->
                        <div class="col-auto">
                            <select name="xtipo" id="xtipo" class="form-control select2" style="min-width: 200px;">
                                <option value="">Todos los Tipos</option>
                                <?php while($rp = $resT->fetch_object()): ?>
                                    <option value="<?= $rp->tipo ?>" <?= ($rp->tipo == $tipo) ? 'selected' : '' ?>>
                                        <?= $rp->tipo ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Botones -->
                        <div class="col-auto">
                            <button type="button" id="btnBuscar" class="btn btn-primary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                        
                        <div class="col-auto">
                            <button type="button" id="btnImprimir" class="btn btn-secondary" onclick="window.print();">
                                <i class="fas fa-print"></i> Imprimir
                            </button>
                        </div>
                        
                        <div class="col-auto">
                            <button type="button" id="btnExportar" class="btn btn-success">
                                <i class="fas fa-file-excel"></i> Exportar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <br>
        
        <!-- Loading -->
        <div id="loading" class="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando datos de aplicaciones...</p>
        </div>
        
        <!-- Resultados -->
        <div id="resultados">
            <!-- Los resultados se cargarán aquí dinámicamente -->
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Inicializar Select2
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });
        
        // Cargar datos iniciales
        cargarDatos();
        
        // Evento del botón buscar
        $('#btnBuscar').click(function() {
            cargarDatos();
        });
        
        // Evento para buscar con Enter en los campos
        $('#dateIni, #dateEnd').keypress(function(e) {
            if (e.which == 13) {
                cargarDatos();
            }
        });
        
        // Evento para exportar a Excel
        $('#btnExportar').click(function() {
            exportarExcel();
        });
    });
    
    function cargarDatos() {
        // Mostrar loading
        $('#loading').show();
        $('#resultados').html('');
        
        // Obtener valores de filtros
        const finca = $('#xfinca').val();
        const tipo = $('#xtipo').val();
        const dateIni = $('#dateIni').val();
        const dateEnd = $('#dateEnd').val();
        
        // Crear objeto de datos para enviar (solo filtros necesarios)
        const datos = {
            finca: finca,
            tipo_filtro: tipo,
            fecha_inicio: dateIni,
            fecha_fin: dateEnd
        };
        
        // Hacer la petición AJAX
        $.ajax({
            url: '<?= $ajaxPrefix ?>ajax/consultar_aplicaciones.php',
            type: 'POST',
            data: datos,
            dataType: 'text',
            success: function(responseText) {
                $('#loading').hide();
                
                let response;
                try {
                    response = JSON.parse(responseText);
                } catch (e) {
                    console.error('Respuesta NO es JSON. Primeros 800 chars:', responseText.slice(0, 800));
                    $('#resultados').html(`
                        <div class="alert alert-danger">
                            <h4>Error al cargar los datos</h4>
                            <p>El servidor devolvió HTML/texto en vez de JSON.</p>
                            <pre style="white-space: pre-wrap; max-height: 240px; overflow:auto;">${$('<div/>').text(responseText.slice(0, 800)).html()}</pre>
                        </div>
                    `);
                    return;
                }

                // Normalizar respuesta a un arreglo siempre
                let filas = [];
                if (Array.isArray(response)) {
                    filas = response;
                } else if (response && Array.isArray(response.data)) {
                    filas = response.data;
                } else if (response && Array.isArray(response.rows)) {
                    filas = response.rows;
                } else {
                    console.error('Respuesta inesperada del servidor:', response);
                }
                
                mostrarResultados(filas, datos);
            },
            error: function(xhr, status, error) {
                $('#loading').hide();
                $('#resultados').html(`
                    <div class="alert alert-danger">
                        <h4>Error al cargar los datos</h4>
                        <p>${error}</p>
                        <p>Por favor, intente nuevamente.</p>
                    </div>
                `);
                console.error('Error AJAX:', error, 'responseText:', (xhr && xhr.responseText ? xhr.responseText.slice(0, 800) : ''));
            }
        });
    }
    
    // ---- Utilidades (modular) ----
    function parseLocalYmd(ymd) {
        if (!ymd || typeof ymd !== 'string') return null;
        const parts = ymd.split('-').map(Number);
        if (parts.length !== 3) return null;
        const [y, m, d] = parts;
        if (!y || !m || !d) return null;
        return new Date(y, m - 1, d);
    }

    function formatDateEs(ymd) {
        const d = parseLocalYmd(ymd);
        return d ? d.toLocaleDateString('es-ES') : '';
    }

    function renderHeader(filtros) {
        const fechaIni = formatDateEs(filtros.fecha_inicio);
        const fechaFin = formatDateEs(filtros.fecha_fin);

        return `
            <div class="card mt-3">
                <div class="card-header">
                    <h4>${filtros.finca || 'Todas las Fincas'}</h4>
                    <h5>Reporte Semanal de Aplicaciones</h5>
                    <h6>${filtros.tipo_filtro || 'Todos los Tipos'}</h6>
                    <p class="mb-0">Entre el: ${fechaIni} y ${fechaFin}</p>
                </div>
            </div>
        `;
    }

    function renderFirstTable(rows) {
        let html = `
            <div class="row mt-3">
                <div class="col-12">
                    <div class="table-container">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Finca</th>
                                    <th>Bloque</th>
                                    <th>Variedad</th>
                                    <th>Temporada</th>
                                    <th>Aplicación</th>
                                    <th>Tipo</th>
                                    <th>Camas</th>
                                    <th>Camas Real</th>
                                    <th>Realizado</th>
                                    <th>Semana</th>
                                </tr>
                            </thead>
                            <tbody>
        `;

        rows.forEach(r => {
            html += `
                <tr>
                    <td>${r.finca ?? ''}</td>
                    <td>${r.bloque ?? ''}</td>
                    <td>${r.variedad ?? ''}</td>
                    <td>${r.temporada ?? ''}</td>
                    <td>${r.aplicar ?? ''}</td>
                    <td>${r.tipo ?? ''}</td>
                    <td>${formatNumber((Number(r.camas) || 0).toFixed(1))}</td>
                    <td>${formatNumber(Math.round(Number(r.camas_real) || 0))}</td>
                    <td></td>
                    <td>${r.semana_anio ?? ''}</td>
                </tr>
            `;
        });

        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    function renderEmpty() {
        return `
            <div class="alert alert-warning mt-3">
                <h4>No se encontraron resultados</h4>
                <p>No hay aplicaciones programadas para los filtros seleccionados.</p>
            </div>
        `;
    }

    // ---- Vista (solo primera tabla por ahora) ----
    function mostrarResultados(data, filtros) {
        if (!Array.isArray(data) || data.length === 0) {
            $('#resultados').html(renderHeader(filtros) + renderEmpty());
            return;
        }

        // Orden estable (todo string)
        const rows = [...data].sort((a, b) => {
            const fincaA = (a.finca ?? '').toString();
            const fincaB = (b.finca ?? '').toString();
            const bloqueA = (a.bloque ?? '').toString();
            const bloqueB = (b.bloque ?? '').toString();
            const aplicarA = (a.aplicar ?? '').toString();
            const aplicarB = (b.aplicar ?? '').toString();
            const variedadA = (a.variedad ?? '').toString();
            const variedadB = (b.variedad ?? '').toString();
            const temporadaA = (a.temporada ?? '').toString();
            const temporadaB = (b.temporada ?? '').toString();

            return fincaA.localeCompare(fincaB) ||
                bloqueA.localeCompare(bloqueB) ||
                aplicarA.localeCompare(aplicarB) ||
                variedadA.localeCompare(variedadB) ||
                temporadaA.localeCompare(temporadaB);
        });

        const html = renderHeader(filtros) + renderFirstTable(rows);
        $('#resultados').html(html);
    }
    
    function cargarInsumos(filtros) {
        // Esta función cargaría los insumos desde otro servicio
        // Por ahora, mostraremos un mensaje de ejemplo
        setTimeout(() => {
            $('#cargandoInsumos').html('Datos de insumos no disponibles en este momento.');
        }, 1000);
    }
    
    function exportarExcel() {
        const wb = XLSX.utils.book_new();
        
        // Crear hoja de datos
        const ws_data = [];
        
        // Encabezados
        ws_data.push([
            'Finca', 'Bloque', 'Variedad', 'Temporada', 
            'Aplicación', 'Tipo', 'Camas', 'Camas Real', 
            'Fecha Aplicación', 'Semana'
        ]);
        
        // Agregar datos (esto es un ejemplo, necesitarías los datos actuales)
        // En una implementación real, deberías obtener los datos actuales de la tabla
        
        const ws = XLSX.utils.aoa_to_sheet(ws_data);
        XLSX.utils.book_append_sheet(wb, ws, "Aplicaciones");
        
        // Descargar archivo
        const fecha = new Date().toISOString().split('T')[0];
        XLSX.writeFile(wb, `aplicaciones_${fecha}.xlsx`);
    }
    
    function formatNumber(number) {
        if (!number) return '0';
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    </script>
</body>
</html>