<?php
require_once dirname(__DIR__) . '/funciones/conexion.php';
\App\Helpers\Auth::requireAdmin();

header('Content-Type: application/json; charset=utf-8');

$action = trim($_REQUEST['action'] ?? 'get');

// Definición canónica de módulos del sistema agrupados
$modules = [
    'Tablas y Catálogos' => [
        ['dir' => 'views/arrangements_crud.php', 'name' => 'CRUD Arrangements', 'alias' => 'tables/arrangements.php'],
        ['dir' => 'views/arrangement_crud.php',  'name' => 'CRUD Arrangement', 'alias' => 'tables/arrangements.php'],
        ['dir' => 'views/breeders_crud.php',     'name' => 'CRUD Breeders'],
        ['dir' => 'views/supplies_crud.php',     'name' => 'CRUD Supplies'],
        ['dir' => 'views/varieties_crud.php',    'name' => 'CRUD Varieties'],
        ['dir' => 'views/seasons_crud.php',      'name' => 'CRUD Seasons'],
        ['dir' => 'views/greenhouses_crud.php',  'name' => 'CRUD Greenhouses'],
        ['dir' => 'views/tika_calendario.php',   'name' => 'CRUD Calendario Tika'],
        ['dir' => 'views/fv_catalogs_crud.php',  'name' => 'CRUD Catálogos Floreros (Tipos, Causas, etc.)'],
    ],
    'Cultivo' => [
        ['dir' => 'views/formBautizo.php',          'name' => 'Hoja de Bautizo Clavel'],
        ['dir' => 'views/applications.php',         'name' => 'Aplicaciones de insumos por Semana'],
        ['dir' => 'views/reporte_cosecha_lonas.php','name' => 'Reporte Lonas Semanal'],
        ['dir' => 'views/program.php',              'name' => 'Presupuesto de Siembras'],
        ['dir' => 'views/programView.php',          'name' => 'Presupuesto con Asignación de Área'],
        ['dir' => 'views/growplanting.php',         'name' => 'Evaluación de Cosechas'],
    ],
    'Siembras y Propagación' => [
        ['dir' => 'views/loadfiles.php',                'name' => 'Cargar Archivos'],
        ['dir' => 'views/labors_form.php',              'name' => 'Registra Labores de Siembra'],
        ['dir' => 'views/entrada_material_vegetal.php', 'name' => 'Entrada Material Vegetal'],
        ['dir' => 'views/programs.php',                 'name' => 'Editar Programa Siembras'],
        ['dir' => 'views/programsf.php',                'name' => 'Editar Programa por Finca'],
        ['dir' => 'views/plano_consulta.php',           'name' => 'Plano Consulta'],
        ['dir' => 'tables/viewReportsP.php',            'name' => 'Informes de Propagación y Siembras'],
        ['dir' => 'views/print.php',                    'name' => 'Programa de Siembras (Imprimir)'],
        ['dir' => 'views/growroot.php',                 'name' => 'Programa de Ensartes (Imprimir)'],
    ],
    'Power BI' => [
        ['dir' => 'views/report_pb_pto_vs_real.php', 'name' => 'Plano de Siembras'],
        ['dir' => 'views/report_pb_clavel.php',      'name' => 'Informe de Clavel por Picos'],
        ['dir' => 'views/report_pb_curvas.php',      'name' => 'Proyecciones Real - Quipus - Inver'],
        ['dir' => 'views/report_pb_nacional.php',    'name' => 'Causas Nacional'],
        ['dir' => 'views/report_pb_compara_prod.php','name' => 'Compara Producción'],
    ],
    'Reportes de Producción' => [
        ['dir' => 'views/reportes_dashboard.php',      'name' => 'Reporte Sembrado'],
        ['dir' => 'views/reporte_comparativo.php',      'name' => 'Teórico vs Real Siembras'],
        ['dir' => 'views/curvas_clavel_produccion.php', 'name' => 'Curvas Producción Clavel'],
        ['dir' => 'views/seguimiento_produccion.php',   'name' => 'Seguimiento de Producción'],
        ['dir' => 'views/demanda_tika.php',             'name' => 'Demanda Semanal'],
        ['dir' => 'views/compara_escenarios.php',       'name' => 'Comparar Escenarios'],
    ],
    'Floreros y Postcosecha' => [
        ['dir' => 'views/flowervase.php',         'name' => 'Registra Floreros'],
        ['dir' => 'views/florero_diario.php',     'name' => 'Revisión Diaria Móvil'],
        ['dir' => 'views/florero_perfil.php',     'name' => 'Perfil de Variedades'],
        ['dir' => 'views/report_fv.php',          'name' => 'Reporte Floreros Tabular'],
        ['dir' => 'views/evaluaciones_crud.php',  'name' => 'Registra Evaluaciones', 'alias' => 'views/orders.php'],
        ['dir' => 'views/report_evaluations.php', 'name' => 'Reporte Evaluaciones'],
    ],
    'Gestión Operativa PM' => [
        ['dir' => 'views/gestion_dashboard.php', 'name' => 'Centro de Gestión'],
        ['dir' => 'views/proyectos.php',         'name' => 'Proyectos y Frentes', 'alias' => 'views/solicitudes_extra.php'],
        ['dir' => 'views/tareas.php',            'name' => 'Tareas Operativas'],
        ['dir' => 'views/kanban.php',            'name' => 'Kanban de Ejecución'],
        ['dir' => 'views/bitacora.php',          'name' => 'Bitácora y Seguimiento'],
    ],
];

// 1. OBTENER ESTADO ACTUAL DE LA MATRIZ
if ($action === 'get') {
    // Obtener catálogo de roles activos
    $rolesCatalog = \App\Helpers\Auth::getRolesCatalog($conexion);

    // Obtener todos los permisos asignados en la BD
    $assigned = [];
    $res = $conexion->query("SELECT users_role, dir FROM roles");
    if ($res) {
        while ($r = $res->fetch_assoc()) {
            $assigned[(int)$r['users_role']][$r['dir']] = true;
        }
    }

    echo json_encode([
        'success'  => true,
        'roles'    => $rolesCatalog,
        'modules'  => $modules,
        'assigned' => $assigned,
    ]);
    exit;
}

// 2. TOGGLE / ALTERNAR UN PERMISO ESPECÍFICO
if ($action === 'toggle') {
    $roleId = (int)($_POST['role_id'] ?? 0);
    $dir    = trim($_POST['dir'] ?? '');
    $state  = (int)($_POST['state'] ?? 0); // 1 = conceder, 0 = revocar

    if ($roleId <= 0 || $dir === '') {
        echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
        exit;
    }

    // Rol 1 (Admin) no se puede modificar porque tiene permiso absoluto por diseño
    if ($roleId === 1) {
        echo json_encode(['success' => false, 'message' => 'El rol Administrador siempre tiene acceso total']);
        exit;
    }

    if ($state === 1) {
        // Conceder: insertar si no existe
        $stmt = $conexion->prepare("INSERT IGNORE INTO roles (users_role, dir) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("is", $roleId, $dir);
            $stmt->execute();
            $stmt->close();
        }
    } else {
        // Revocar: eliminar de roles (y posibles aliases)
        $stmt = $conexion->prepare("DELETE FROM roles WHERE users_role = ? AND dir = ?");
        if ($stmt) {
            $stmt->bind_param("is", $roleId, $dir);
            $stmt->execute();
            $stmt->close();
        }
    }

    unset($_SESSION['allowed_dirs']); // Invalidar caché de sesión actual

    echo json_encode(['success' => true]);
    exit;
}

// 3. GUARDADO MASIVO DE UN ROL
if ($action === 'save_role') {
    $roleId = (int)($_POST['role_id'] ?? 0);
    $dirs   = $_POST['dirs'] ?? [];

    if ($roleId <= 0 || !is_array($dirs)) {
        echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
        exit;
    }

    if ($roleId === 1) {
        echo json_encode(['success' => false, 'message' => 'El rol Administrador siempre tiene acceso total']);
        exit;
    }

    $conexion->begin_transaction();
    try {
        // Borrar permisos actuales del rol para los módulos canónicos
        $del = $conexion->prepare("DELETE FROM roles WHERE users_role = ?");
        $del->bind_param("i", $roleId);
        $del->execute();
        $del->close();

        // Insertar los nuevos seleccionados
        if (!empty($dirs)) {
            $ins = $conexion->prepare("INSERT INTO roles (users_role, dir) VALUES (?, ?)");
            foreach ($dirs as $d) {
                $d = trim((string)$d);
                if ($d !== '') {
                    $ins->bind_param("is", $roleId, $d);
                    $ins->execute();
                }
            }
            $ins->close();
        }

        $conexion->commit();
        unset($_SESSION['allowed_dirs']);
        echo json_encode(['success' => true, 'message' => 'Permisos actualizados correctamente']);
    } catch (\Throwable $e) {
        $conexion->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
