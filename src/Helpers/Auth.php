<?php
declare(strict_types=1);

namespace App\Helpers;

class Auth
{
    /**
     * Asegura que la sesión esté iniciada.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }
    }

    public static function check(): bool
    {
        self::start();
        if (empty($_SESSION['usuario']) || empty($_SESSION['id'])) {
            return false;
        }

        // Control de inactividad de sesión (60 minutos = 3600 segundos)
        $timeout = 3600;
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout)) {
            session_unset();
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            }
            return false;
        }
        $_SESSION['LAST_ACTIVITY'] = time();

        return true;
    }

    /**
     * Retorna el ID del usuario actual.
     */
    public static function id(): ?int
    {
        self::start();
        return isset($_SESSION['id']) ? (int)$_SESSION['id'] : null;
    }

    /**
     * Retorna el nombre del usuario actual.
     */
    public static function user(): ?string
    {
        self::start();
        return $_SESSION['usuario'] ?? null;
    }

    /**
     * Retorna el login/email del usuario actual.
     */
    public static function email(): ?string
    {
        self::start();
        return $_SESSION['email'] ?? null;
    }

    /**
     * Retorna el rol del usuario (1 = Admin, etc.).
     */
    public static function role(): int
    {
        self::start();
        return isset($_SESSION['role']) ? (int)$_SESSION['role'] : 0;
    }

    /**
     * Comprueba si el usuario tiene rol de Administrador (role = 1).
     */
    public static function isAdmin(): bool
    {
        return self::role() === 1;
    }

    /**
     * Comprueba si el usuario tiene alguno de los roles indicados.
     * @param int|int[] $roles
     */
    public static function hasRole($roles): bool
    {
        $current = self::role();
        if (is_array($roles)) {
            return in_array($current, $roles, true);
        }
        return $current === (int)$roles;
    }

    /**
     * Retorna el nombre legible del rol del usuario actual.
     */
    public static function roleName(?\mysqli $conexion = null): string
    {
        $roleId = self::role();
        if ($roleId === 0) {
            return 'Invitado';
        }

        if (isset($_SESSION['role_name']) && !empty($_SESSION['role_name'])) {
            return (string)$_SESSION['role_name'];
        }

        $names = [
            1 => 'Administrador',
            2 => 'Agronomía y Supervisión',
            3 => 'Operativo y Consultas',
            5 => 'Producción e Invernaderos',
            6 => 'Calidad y Floreros',
        ];

        if (isset($names[$roleId])) {
            $_SESSION['role_name'] = $names[$roleId];
            return $names[$roleId];
        }

        return "Rol #$roleId";
    }

    /**
     * Obtiene el catálogo completo de roles disponibles.
     */
    public static function getRolesCatalog(?\mysqli $conexion = null): array
    {
        if (!$conexion) {
            $conexion = $GLOBALS['conexion'] ?? null;
        }

        if ($conexion instanceof \mysqli) {
            $res = $conexion->query("SELECT id, nombre, slug, descripcion FROM roles_catalog WHERE activo = 1 ORDER BY id ASC");
            if ($res) {
                return $res->fetch_all(MYSQLI_ASSOC);
            }
        }

        return [
            ['id' => 1, 'nombre' => 'Administrador', 'slug' => 'admin'],
            ['id' => 2, 'nombre' => 'Agronomía y Supervisión', 'slug' => 'agronomia_supervision'],
            ['id' => 3, 'nombre' => 'Operativo y Consultas', 'slug' => 'operativo_consultas'],
            ['id' => 5, 'nombre' => 'Producción e Invernaderos', 'slug' => 'produccion_invernaderos'],
            ['id' => 6, 'nombre' => 'Calidad y Floreros', 'slug' => 'calidad_floreros'],
        ];
    }

    /**
     * Comprueba si el rol del usuario tiene permiso para acceder a una ruta o vista.
     */
    public static function can(string $dir, ?\mysqli $conexion = null): bool
    {
        if (!self::check()) {
            return false;
        }

        // El Administrador siempre tiene acceso total a todos los módulos
        if (self::isAdmin()) {
            return true;
        }

        // Vistas públicas para cualquier usuario autenticado
        $publicAuthDirs = [
            'views/florero_diario.php',
            'views/florero_perfil.php',
            'views/loadfiles.php',
            'views/report_pb_pto_vs_real.php',
            'views/resumen_ejecutivo_mensual.php',
            'views/compara_escenarios.php',
            'views/reporte_cosecha_lonas.php',
            'views/plano_consulta.php',
            'views/plano_reemplazos.php',
        ];
        if (in_array($dir, $publicAuthDirs, true)) {
            return true;
        }

        // Cargar permisos desde la base de datos si no están en sesión
        if (!isset($_SESSION['allowed_dirs']) || !is_array($_SESSION['allowed_dirs'])) {
            if (!$conexion) {
                if (isset($GLOBALS['conexion']) && $GLOBALS['conexion'] instanceof \mysqli) {
                    $conexion = $GLOBALS['conexion'];
                } else {
                    include_once dirname(__DIR__, 2) . '/funciones/conexion.php';
                    $conexion = $GLOBALS['conexion'] ?? null;
                }
            }

            if ($conexion instanceof \mysqli) {
                $_SESSION['allowed_dirs'] = [];
                $role = self::role();
                $stmt = $conexion->prepare("SELECT dir FROM roles WHERE users_role = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $role);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    while ($r = $res->fetch_assoc()) {
                        $_SESSION['allowed_dirs'][] = strval($r['dir']);
                    }
                    $stmt->close();
                }
            }
        }

        $allowed = $_SESSION['allowed_dirs'] ?? [];

        if (in_array($dir, $allowed, true)) {
            return true;
        }

        // Aliases y compatibilidad histórica de rutas refactorizadas
        $aliases = [
            'views/arrangements_crud.php' => 'tables/arrangements.php',
            'views/arrangement_crud.php'  => 'tables/arrangements.php',
            'views/evaluaciones_crud.php' => 'views/orders.php',
            'views/solicitudes_extra.php' => 'views/proyectos.php',
            'views/fv_catalogs_crud.php'  => 'views/flowervase.php',
        ];

        if (isset($aliases[$dir]) && in_array($aliases[$dir], $allowed, true)) {
            return true;
        }

        return false;
    }

    /**
     * Comprueba si el usuario tiene permiso para al menos una de las rutas dadas.
     * @param string[] $dirs
     */
    public static function canAny(array $dirs, ?\mysqli $conexion = null): bool
    {
        if (!self::check()) {
            return false;
        }
        if (self::isAdmin()) {
            return true;
        }
        foreach ($dirs as $d) {
            if (self::can($d, $conexion)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Exige que el usuario esté autenticado. Si no, redirige o responde error.
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            if (self::isAjaxOrJson()) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Sesión expirada o no autenticada']);
                exit;
            }
            header('Location: index.php?error=auth');
            exit;
        }
    }

    /**
     * Exige que el usuario sea Administrador. Si no, aborta con 403.
     */
    public static function requireAdmin(): void
    {
        self::requireAuth();
        if (!self::isAdmin()) {
            if (self::isAjaxOrJson()) {
                http_response_code(403);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Acceso denegado: se requieren permisos de Administrador']);
                exit;
            }
            http_response_code(403);
            die('<h3 style="color:#c62828;text-align:center;margin-top:2rem;">Acceso Restringido: Requiere rol de Administrador</h3>');
        }
    }

    /**
     * Detecta si la petición es AJAX o espera respuesta JSON.
     */
    private static function isAjaxOrJson(): bool
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return strpos($accept, 'application/json') !== false;
    }

    /**
     * Obtiene la dirección IP real del cliente considerando proxies.
     */
    public static function clientIp(): string
    {
        $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
        foreach ($headers as $h) {
            if (!empty($_SERVER[$h])) {
                $raw = trim((string)$_SERVER[$h]);
                if (strpos($raw, ',') !== false) {
                    $raw = trim(explode(',', $raw)[0]);
                }
                if (filter_var($raw, FILTER_VALIDATE_IP)) {
                    return $raw;
                }
                return substr($raw, 0, 45);
            }
        }
        return '127.0.0.1';
    }

    /**
     * Obtiene el User-Agent truncado a 255 caracteres.
     */
    public static function userAgent(): string
    {
        return substr($_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN', 0, 255);
    }

    /**
     * Registra un evento de inicio de sesión en la tabla login_logs.
     */
    public static function logLogin(?\mysqli $conexion, ?int $userId, string $username, string $status, string $details = ''): bool
    {
        if (!$conexion) {
            $conexion = $GLOBALS['conexion'] ?? null;
        }
        if (!($conexion instanceof \mysqli)) {
            return false;
        }

        $ip = self::clientIp();
        $agent = self::userAgent();
        $status = in_array($status, ['EXITOSO', 'FALLIDO', 'BLOQUEADO'], true) ? $status : 'FALLIDO';

        $stmt = $conexion->prepare("INSERT INTO login_logs (user_id, username, ip_address, user_agent, status, details, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("isssss", $userId, $username, $ip, $agent, $status, $details);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
