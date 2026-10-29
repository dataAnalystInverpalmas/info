<?php
//lamar conexion
include ('funciones/conexion.php');

//verificar los roles	
?>
<style>
/* ========================================================
   MODERN IMPORT & SYNCHRONIZATION PROGRESS SYSTEM
   Replaces static loader.gif with dynamic estimation UI
   ======================================================== */
.ip-loader-container {
    min-height: calc(100vh - 240px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
}
.ip-loader-card {
    background: #FFFFFF;
    border-radius: 20px;
    border: 1px solid #E2E8F0;
    box-shadow: 0 20px 45px -10px rgba(0, 121, 107, 0.12), 0 4px 15px rgba(0, 0, 0, 0.04);
    width: 100%;
    max-width: 660px;
    padding: 2.5rem 2.25rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    animation: ipFadeIn 0.35s ease-out;
}
@keyframes ipFadeIn {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}
.ip-loader-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #00796B, #10B981, #0284C7, #00796B);
    background-size: 200% 100%;
    animation: ipGlowBar 2.5s linear infinite;
}
@keyframes ipGlowBar {
    0% { background-position: 100% 0; }
    100% { background-position: -100% 0; }
}
/* Visual Orb */
.ip-loader-visual {
    position: relative;
    width: 110px;
    height: 110px;
    margin: 0 auto 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.ip-spinner-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 3.5px solid #E6F4F1;
    border-top-color: #00796B;
    border-right-color: #10B981;
    animation: ipSpin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
}
.ip-spinner-ring-inner {
    position: absolute;
    width: 75%;
    height: 75%;
    border-radius: 50%;
    border: 3px dashed #80CBC4;
    animation: ipSpinReverse 2.4s linear infinite;
}
@keyframes ipSpin {
    to { transform: rotate(360deg); }
}
@keyframes ipSpinReverse {
    to { transform: rotate(-360deg); }
}
.ip-loader-icon-center {
    width: 52px;
    height: 52px;
    background: linear-gradient(135deg, #00796B 0%, #004D40 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FFFFFF;
    box-shadow: 0 6px 16px rgba(0, 121, 107, 0.35);
    z-index: 2;
    animation: ipPulseIcon 2s ease-in-out infinite;
}
@keyframes ipPulseIcon {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.06); }
}

/* Progress bar */
.ip-progress-wrapper {
    background: #F1F5F9;
    border-radius: 999px;
    height: 12px;
    overflow: hidden;
    margin: 1.5rem 0 0.85rem;
    position: relative;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
}
.ip-progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #00796B, #10B981);
    border-radius: 999px;
    width: 6%;
    transition: width 0.3s ease, background 0.3s ease;
    position: relative;
    overflow: hidden;
}
.ip-progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.4) 50%, rgba(255,255,255,0) 100%);
    animation: ipShimmer 1.8s infinite;
}
@keyframes ipShimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Timers & Stats */
.ip-timer-grid {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.85rem;
    color: #64748B;
    margin-bottom: 1.25rem;
    font-weight: 600;
}
.ip-step-box {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 0.84rem;
    color: #334155;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    min-height: 44px;
}
.ip-step-dot {
    width: 8px;
    height: 8px;
    background: #00796B;
    border-radius: 50%;
    animation: ipPulseDot 1.4s infinite;
    flex-shrink: 0;
}
@keyframes ipPulseDot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.3); }
}
</style>

<div id="mostrar_loading" style="display:none;"></div>
<div id="mostrar_listo" style="display:none;"></div>
<div id="mostrar_error" style="display:none;"></div>
<script src="scripts/loadFiles.js?v=<?php echo time(); ?>"></script>
<?php
// Mostrar enlace al report programs en el menú si el rol tiene permiso
$dir_menu = "views/programs.php";
$stmt_menu = $conexion->prepare("SELECT dir FROM roles WHERE users_role = ? AND dir = ? LIMIT 1");
$role_menu = intval($_SESSION['role']);
$stmt_menu->bind_param("is", $role_menu, $dir_menu);
$stmt_menu->execute();
$rmenu = $stmt_menu->get_result();
$stmt_menu->close();
?>
<script>
    var ipImportInterval = null;

    function getTableMetaFromLink(link) {
        var str = String(link || '').toLowerCase();
        if (str.indexOf("plane.php") !== -1) {
            return {
                name: "Matriz de Siembras (Plantas Sembradas ld5000)",
                badge: "Plane • Siembras",
                estimated: 35
            };
        } else if (str.indexOf("varieties.php") !== -1) {
            return {
                name: "Catálogo de Variedades",
                badge: "Variedades • Catálogo",
                estimated: 10
            };
        } else if (str.indexOf("hplane.php") !== -1) {
            return {
                name: "Histórico de Planos de Siembra",
                badge: "HPlane • Histórico",
                estimated: 25
            };
        } else if (str.indexOf("program.php") !== -1) {
            return {
                name: "Presupuesto y Programa de Siembras",
                badge: "Presupuesto",
                estimated: 18
            };
        } else if (str.indexOf("festivos.php") !== -1) {
            return {
                name: "Calendario de Días Festivos",
                badge: "Festivos",
                estimated: 6
            };
        } else if (str.indexOf("laborssowing.php") !== -1) {
            return {
                name: "Labores de Siembra",
                badge: "Labores",
                estimated: 15
            };
        }
        return {
            name: "Sincronización de Matriz de Datos",
            badge: "Procesamiento",
            estimated: 25
        };
    }

    function renderLoaderCardHtml(meta) {
        return '<div class="ip-loader-container">' +
            '<div class="ip-loader-card">' +
                '<div class="ip-loader-visual">' +
                    '<div class="ip-spinner-ring"></div>' +
                    '<div class="ip-spinner-ring-inner"></div>' +
                    '<div class="ip-loader-icon-center">' +
                        '<span class="material-icons" style="font-size: 1.8rem;">table_chart</span>' +
                    '</div>' +
                '</div>' +
                '<span class="badge rounded-pill bg-light text-dark border text-uppercase fw-bold px-3 py-1 mb-2" style="color: #00796B; letter-spacing: 0.5px;">' +
                    '<span class="material-icons me-1 align-middle" style="font-size: 0.9rem;">sync</span>' + meta.badge +
                '</span>' +
                '<h4 class="fw-bold text-dark mb-1" style="font-size: 1.35rem;">Importando y Sincronizando Matriz</h4>' +
                '<p class="text-muted small mb-0">' + meta.name + '</p>' +
                '<div class="ip-progress-wrapper">' +
                    '<div class="ip-progress-bar-fill" id="ipProgressBar"></div>' +
                '</div>' +
                '<div class="ip-timer-grid">' +
                    '<span>⏱ Tiempo transcurrido: <strong id="ipTimerElapsed" class="text-dark">00:00</strong></span>' +
                    '<span>Progreso: <strong id="ipProgressPct" class="text-brand-green" style="color: #00796B;">6%</strong></span>' +
                    '<span>⏳ <span id="ipTimerRemaining">~' + meta.estimated + ' seg restantes</span></span>' +
                '</div>' +
                '<div class="ip-step-box">' +
                    '<span class="ip-step-dot"></span>' +
                    '<span id="ipStepText">Iniciando lectura y verificación del archivo Excel...</span>' +
                '</div>' +
                '<p class="text-muted small mb-0" style="font-size: 0.78rem;">' +
                    '<span class="material-icons align-middle text-muted me-1" style="font-size: 0.95rem;">info</span>' +
                    'Por favor espere. El servidor está actualizando los registros de forma segura.' +
                '</p>' +
            '</div>' +
        '</div>';
    }

    function renderSuccessCardHtml(meta, totalElapsed) {
        return '<div class="ip-loader-container">' +
            '<div class="ip-loader-card" style="border-top: 5px solid #10B981;">' +
                '<div style="width: 78px; height: 78px; background: #E6F4F1; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; border: 2px solid #80CBC4; box-shadow: 0 8px 20px rgba(0, 121, 107, 0.2);">' +
                    '<span class="material-icons" style="font-size: 3rem; color: #00796B;">check_circle</span>' +
                '</div>' +
                '<span class="badge rounded-pill bg-light text-dark border text-uppercase fw-bold px-3 py-1 mb-2" style="color: #00796B; letter-spacing: 0.5px;">' +
                    'Sincronización Exitosa' +
                '</span>' +
                '<h3 class="fw-bold text-dark mb-2">¡Matriz Sincronizada con Éxito!</h3>' +
                '<p class="text-muted small mb-4">' +
                    'La matriz <strong>' + meta.name + '</strong> fue procesada e integrada en la base de datos de Inverpalmas en <strong>' + totalElapsed + ' segundos</strong>.' +
                '</p>' +
                '<div class="d-flex justify-content-center flex-wrap" style="gap: 0.75rem;">' +
                    '<a href="index.php" class="btn btn-success px-4 py-2 fw-bold d-inline-flex align-items-center" style="background-color: #00796B; border-color: #00796B;">' +
                        '<span class="material-icons me-1" style="font-size: 1.1rem;">dashboard</span>' +
                        'Ir al Dashboard' +
                    '</a>' +
                    '<a href="index.php?report=cargar_archivos" class="btn btn-outline-secondary px-3 py-2 fw-bold d-inline-flex align-items-center">' +
                        '<span class="material-icons me-1" style="font-size: 1.1rem;">cloud_upload</span>' +
                        'Cargar Otro Archivo' +
                    '</a>' +
                    '<a href="index.php?report=plano_siembras" class="btn btn-outline-info px-3 py-2 fw-bold d-inline-flex align-items-center">' +
                        '<span class="material-icons me-1" style="font-size: 1.1rem;">table_chart</span>' +
                        'Ver Plano de Siembras' +
                    '</a>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    function renderErrorCardHtml(meta, link) {
        return '<div class="ip-loader-container">' +
            '<div class="ip-loader-card" style="border-top: 5px solid #DC2626;">' +
                '<div style="width: 78px; height: 78px; background: #FEE2E2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem; border: 2px solid #FCA5A5;">' +
                    '<span class="material-icons" style="font-size: 3rem; color: #DC2626;">error_outline</span>' +
                '</div>' +
                '<span class="badge rounded-pill bg-danger text-white fw-bold px-3 py-1 mb-2">Error en Procesamiento</span>' +
                '<h3 class="fw-bold text-dark mb-2">No se pudo completar la sincronización</h3>' +
                '<p class="text-muted small mb-4">' +
                    'Ocurrió un error al procesar <strong>' + meta.name + '</strong>. Verifique que las columnas del archivo coincidan con la plantilla requerida y vuelva a intentarlo.' +
                '</p>' +
                '<div class="d-flex justify-content-center flex-wrap" style="gap: 0.75rem;">' +
                    '<button type="button" class="btn btn-danger px-4 py-2 fw-bold d-inline-flex align-items-center" onclick="listar_tabla(\'' + link + '\');">' +
                        '<span class="material-icons me-1" style="font-size: 1.1rem;">refresh</span>' +
                        'Reintentar Importación' +
                    '</button>' +
                    '<a href="index.php?report=cargar_archivos" class="btn btn-outline-secondary px-3 py-2 fw-bold d-inline-flex align-items-center">' +
                        '<span class="material-icons me-1" style="font-size: 1.1rem;">arrow_back</span>' +
                        'Volver a Cargar Archivos' +
                    '</a>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    function listar_tabla(link) {
        if (ipImportInterval) {
            clearInterval(ipImportInterval);
        }

        var loadingEl = document.getElementById("mostrar_loading");
        var listoEl = document.getElementById("mostrar_listo");
        var errorEl = document.getElementById("mostrar_error");

        if (listoEl) listoEl.style.display = "none";
        if (errorEl) errorEl.style.display = "none";
        if (loadingEl) {
            loadingEl.style.display = "block";
            var meta = getTableMetaFromLink(link);
            loadingEl.innerHTML = renderLoaderCardHtml(meta);

            var startTime = Date.now();
            var progressBar = document.getElementById("ipProgressBar");
            var progressPctText = document.getElementById("ipProgressPct");
            var timerElapsed = document.getElementById("ipTimerElapsed");
            var timerRemaining = document.getElementById("ipTimerRemaining");
            var stepText = document.getElementById("ipStepText");

            function pad2(n) { return (n < 10 ? "0" : "") + n; }

            ipImportInterval = setInterval(function () {
                var elapsed = (Date.now() - startTime) / 1000;

                if (timerElapsed) {
                    var m = Math.floor(elapsed / 60);
                    var s = Math.floor(elapsed % 60);
                    timerElapsed.textContent = pad2(m) + ":" + pad2(s);
                }

                // Asymptotic progression toward 95%
                var pct = Math.min(94, Math.round((1 - Math.exp(-elapsed / (meta.estimated * 0.45))) * 100));
                if (pct < 6) pct = 6;

                if (progressBar) progressBar.style.width = pct + "%";
                if (progressPctText) progressPctText.textContent = pct + "%";

                if (timerRemaining) {
                    var rem = Math.max(1, Math.round(meta.estimated - elapsed));
                    if (elapsed < meta.estimated) {
                        timerRemaining.textContent = "~" + rem + " seg restantes";
                    } else {
                        timerRemaining.textContent = "Finalizando últimos registros...";
                    }
                }

                if (stepText) {
                    if (pct < 20) {
                        stepText.textContent = "Fase 1/4: Conectando con servidor y verificando estructura Excel...";
                    } else if (pct < 50) {
                        stepText.textContent = "Fase 2/4: Leyendo filas de la hoja de cálculo y mapeando columnas...";
                    } else if (pct < 80) {
                        stepText.textContent = "Fase 3/4: Vaciando registros anteriores e insertando camas en MySQL...";
                    } else {
                        stepText.textContent = "Fase 4/4: Validando integridad referencial y actualizando tablas maestras...";
                    }
                }
            }, 250);
        }

        $.ajax({
            url: link,
            type: 'GET',
            cache: false,
            success: function (response) {
                if (ipImportInterval) clearInterval(ipImportInterval);

                var progressBar = document.getElementById("ipProgressBar");
                var progressPctText = document.getElementById("ipProgressPct");
                var stepText = document.getElementById("ipStepText");

                if (progressBar) {
                    progressBar.style.width = "100%";
                    progressBar.style.background = "#10B981";
                }
                if (progressPctText) progressPctText.textContent = "100%";
                if (stepText) stepText.textContent = "✨ ¡Sincronización concluida exitosamente!";

                var totalElapsed = ((Date.now() - startTime) / 1000).toFixed(1);

                setTimeout(function () {
                    if (loadingEl) loadingEl.style.display = "none";
                    if (listoEl) {
                        listoEl.style.display = "block";
                        listoEl.innerHTML = renderSuccessCardHtml(meta, totalElapsed);
                    }
                }, 450);
            },
            error: function (response) {
                if (ipImportInterval) clearInterval(ipImportInterval);
                if (loadingEl) loadingEl.style.display = "none";
                if (errorEl) {
                    errorEl.style.display = "block";
                    errorEl.innerHTML = renderErrorCardHtml(meta, link);
                }
                console.error("Error en listar_tabla: ", response);
            }
        });
    }
</script>

<?php
$enruta = "../";

// ============================================================
// Función centralizada para verificar permisos por rol con sistema de caché de sesión
// Opitmiza drásticamente la velocidad de carga eliminando consultas repetitivas a la base de datos
// ============================================================
function tiene_permiso($conexion, $dir) {
	return \App\Helpers\Auth::can($dir, $conexion);
}

// ============================================================
// RUTAS DE TABLAS (table=X) — cargan vía AJAX con listar_tabla()
// ============================================================
$tablas = array(
	'1'                  => 'tables/plane.php',
	'2'                  => 'tables/varieties.php',
	'3'                  => 'tables/program.php',
	'4'                  => 'tables/seasons.php',
	'5'                  => 'tables/fusarium.php',
	'22'                 => 'tables/festivos.php',
	'7'                  => 'tables/companys.php',
	'8'                  => 'tables/farms.php',
	'9'                  => 'tables/products.php',
	'12'                 => 'tables/programf.php',
	'13'                 => 'tables/hplane.php',
	'14'                 => 'tables/viewReportsP.php',
	'16'                 => 'tables/laborsSowing.php',
	'17'                 => 'tables/program_add.php',
	'18'                 => 'tables/greenhouses.php',
	'19'                 => 'tables/program_add_pto.php',
	'loadEvaluations'    => 'tables/evaluations.php',
	'loadComments'       => 'tables/comments.php',
	'loadCurves'         => 'tables/curves.php',
	'loadFeatures'       => 'tables/features.php',
	'loadEmployees'      => 'tables/employees.php',
	'loadSupervisors'    => 'tables/supervisors.php',
	'loadAssistances'    => 'tables/assistances.php',
	'generateViewBudget' => 'tables/viewBudget.php',
);

// ============================================================
// RUTAS DE REPORTES (report=X) — cargan vía include
// ============================================================
$reportes = array(
	// --- Cultivo ---
	'hoja_bautizo'             => 'views/formBautizo.php',
	'bautizo'                  => 'views/formBautizo.php',
	'aplicaciones'             => 'views/applications.php',
	'aplicaciones_semana'      => 'views/applications.php',
	'evaluacion_cosechas'      => 'views/growplanting.php',
	'reporte_cosecha_lonas'    => 'views/reporte_cosecha_lonas.php',

	// --- Siembras y Propagación ---
	'cargar_archivos'          => 'views/loadfiles.php',
	'labores_siembra'          => 'views/labors_form.php',
	'crud_emv'                 => 'views/entrada_material_vegetal.php',
	'programs'                 => 'views/programs.php',
	'programsf'                => 'views/programsf.php',
	'plano_reemplazos'         => 'views/plano_reemplazos.php',
	'imprimir_siembras'        => 'views/print.php',
	'imprimir_ensartes'        => 'views/growroot.php',

	// --- Power BI ---
	'plano_siembras'           => 'views/report_pb_pto_vs_real.php',
	'informe_clavel_picos'     => 'views/report_pb_clavel.php',
	'proyecciones_curvas'      => 'views/report_pb_curvas.php',
	'causas_nacional'          => 'views/report_pb_nacional.php',
	'compara_produccion'       => 'views/report_pb_compara_prod.php',
	'reporte_trazabilidad'     => 'views/report_trazabilidad.php',
	'pb_demandas'              => 'views/report_pb_demandas.php',
	'pb_jl_proyecciones'       => 'views/report_pb_jl_proyecciones.php',
	'curvas'                   => 'views/report_pb_jl_curvas_clavel.php',

	// --- Reportes de Producción ---
	'dashboard'                => 'views/dashboard_proyecciones.php',
	'dashboard_proyecciones'   => 'views/dashboard_proyecciones.php',
	'reportes_produccion'      => 'views/reportes_dashboard.php',
	'reportes_real_vs_teorico' => 'views/reporte_comparativo.php',
	'curvas_clavel_produccion' => 'views/curvas_clavel_produccion.php',
	'seguimiento_produccion'   => 'views/seguimiento_produccion.php',
	'demanda_tika'             => 'views/demanda_tika.php',
	'compara_escenarios'       => 'views/compara_escenarios.php',

	// --- Floreros y Postcosecha ---
	'registra_floreros'        => 'views/flowervase.php',
	'floreros'                 => 'views/flowervase.php',
	'floreros_diario'          => 'views/florero_diario.php',
	'florero_perfil'           => 'views/florero_perfil.php',
	'reporte_floreros'         => 'views/report_fv.php',
	'orders'                   => 'views/evaluaciones_crud.php',
	'registra_evaluaciones'    => 'views/evaluaciones_crud.php',
	'reporte_evaluaciones'     => 'views/report_evaluations.php',
	'crud_fv_kinds'            => 'views/fv_catalogs_crud.php',
	'crud_fv_origens'          => 'views/fv_catalogs_crud.php',
	'crud_fv_causes'           => 'views/fv_catalogs_crud.php',
	'crud_fv_evaluations'      => 'views/fv_catalogs_crud.php',

	// --- Gestión Operativa PM ---
	'gestion_dashboard'        => 'views/gestion_dashboard.php',
	'proyectos'                => 'views/proyectos.php',
	'tareas'                   => 'views/tareas.php',
	'kanban'                   => 'views/kanban.php',
	'bitacora'                 => 'views/bitacora.php',
	'resumen_ejecutivo_mensual'=> 'views/resumen_ejecutivo_mensual.php',
	'solicitudes_extra'        => 'views/solicitudes_extra.php',

	// --- Tablas y Catálogos CRUD / Admin ---
	'tika_calendario'          => 'views/tika_calendario.php',
	'crud_tika_calendario'     => 'views/tika_calendario.php',
	'arrangements_crud'        => 'views/arrangements_crud.php',
	'arrangement_crud'         => 'views/arrangement_crud.php',
	'crud_breeders'            => 'views/breeders_crud.php',
	'crud_supplies'            => 'views/supplies_crud.php',
	'crud_varieties'           => 'views/varieties_crud.php',
	'crud_seasons'             => 'views/seasons_crud.php',
	'crud_greenhouses'         => 'views/greenhouses_crud.php',
	'invernaderos'             => 'views/greenhouses.php',
	'crud_users'               => 'views/users_crud.php',
	'crud_roles'               => 'views/roles_crud.php',
	'roles_matrix'             => 'views/roles_matrix.php',
	'login_logs'               => 'views/login_logs.php',

	// --- Retrocompatibilidad con rutas numéricas previas ---
	'1'                        => 'views/formBautizo.php',
	'2'                        => 'views/applications.php',
	'6'                        => 'views/print.php',
	'7'                        => 'views/growroot.php',
	'8'                        => 'views/growplanting.php',
	'12'                       => 'views/flowervase.php',
	'14'                       => 'views/labors_form.php',
	'52'                       => 'views/report_fv.php',
	'53'                       => 'views/report_evaluations.php',
	'102'                      => 'views/greenhouses.php',
	'103'                      => 'views/report_pb_clavel.php',
	'104'                      => 'views/report_pb_curvas.php',
	'105'                      => 'views/report_trazabilidad.php',
	'106'                      => 'views/report_pb_demandas.php',
	'107'                      => 'views/report_pb_compara_prod.php',
	'108'                      => 'views/report_pb_pto_vs_real.php',
	'109'                      => 'views/report_pb_jl_proyecciones.php',
	'110'                      => 'views/report_pb_nacional.php',
	'200'                      => 'views/proyectos.php',
	'201'                      => 'views/tareas.php',
	'202'                      => 'views/bitacora.php',
	'203'                      => 'views/kanban.php',
	'204'                      => 'views/resumen_ejecutivo_mensual.php',
	'205'                      => 'views/gestion_dashboard.php',
	'206'                      => 'views/solicitudes_extra.php',
	'1005'                     => 'views/loadfiles.php',
);

// ============================================================
// PROCESAR RUTA DE TABLA
// ============================================================
if (isset($_GET['table'])) {
	$key = strval($_GET['table']);
	if (isset($tablas[$key])) {
		$dir = $tablas[$key];
		$permitido = tiene_permiso($conexion, $dir);

		if ($permitido) {
			?>
			<script>
				var link = "<?php echo $enruta . $dir; ?>";
				listar_tabla(link);
			</script>
			<?php
		} else {
			?>
			<div class="row justify-content-center align-items-center" style="min-height: 50vh;">
				<div class="col-md-6 text-center">
					<div class="p-5 bg-white border border-danger-subtle rounded-lg shadow-sm" style="border-radius: 12px;">
						<span class="material-icons text-danger mb-3" style="font-size: 4rem;">block</span>
						<h3 class="fw-bold text-dark mb-2">Acceso Restringido</h3>
						<p class="text-secondary small mb-4">No tiene los permisos necesarios para visualizar esta tabla. Si considera que es un error, por favor contacte al administrador.</p>
						<a href="index.php" class="btn btn-outline-secondary btn-sm px-4">Volver al Inicio</a>
					</div>
				</div>
			</div>
			<?php
		}
	} else {
		include 'views/error_404.php';
	}
}

// ============================================================
// PROCESAR RUTA DE REPORTE
// ============================================================
if (isset($_GET['report'])) {
	$key = strval($_GET['report']);
	if (isset($reportes[$key])) {
		$dir = $reportes[$key];
		$permitido = tiene_permiso($conexion, $dir);
		if ($permitido) {
			if ($dir === 'views/formBautizo.php') {
				?>
				<div class="embed-responsive">
				<iframe src="views/formBautizo.php"
					style="position: fixed; top: 60px; left: 0; width: 100vw; height: calc(100vh - 70px); border: none;"
					frameborder="0"
					scrolling="auto">
				</iframe>
				</div>
				<?php
			} else {
				include "$dir";
			}
		} else {
			?>
			<div class="row justify-content-center align-items-center" style="min-height: 50vh;">
				<div class="col-md-6 text-center">
					<div class="p-5 bg-white border border-danger-subtle rounded-lg shadow-sm" style="border-radius: 12px;">
						<span class="material-icons text-danger mb-3" style="font-size: 4rem;">block</span>
						<h3 class="fw-bold text-dark mb-2">Acceso Restringido</h3>
						<p class="text-secondary small mb-4">No tiene los permisos necesarios para visualizar este reporte. Si considera que es un error, por favor contacte al administrador.</p>
						<a href="index.php" class="btn btn-outline-secondary btn-sm px-4">Volver al Inicio</a>
					</div>
				</div>
			</div>
			<?php
		}
	} else {
		include 'views/error_404.php';
	}
}
?>
