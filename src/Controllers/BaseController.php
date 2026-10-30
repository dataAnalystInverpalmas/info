<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Auth;
use App\Helpers\Database;
use mysqli;
use RuntimeException;

/**
 * Controlador Base para Inverpalmas MVC.
 * Compatible con PHP 7.4+ y PHP 8.2+.
 */
abstract class BaseController
{
    protected const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;

    /**
     * Retorna la conexión activa a la base de datos
     */
    protected function db(): mysqli
    {
        return Database::getConnection();
    }

    /**
     * Renderiza una vista PHP inyectando datos.
     *
     * @param string $viewPath Ruta de la vista relativa a 'src/Views/' (ej: 'CurvasClavelProduccion/index') o ruta absoluta.
     * @param array<string, mixed> $data Variables a extraer en el contexto de la vista.
     */
    protected function render(string $viewPath, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $resolved = $this->resolveViewPath($viewPath);
        if (!file_exists($resolved)) {
            throw new RuntimeException("Vista no encontrada: {$resolved}");
        }

        require $resolved;
    }

    /**
     * Envía una respuesta JSON pura con código de estado HTTP y finaliza la ejecución.
     *
     * @param mixed $data
     */
    protected function json($data, int $statusCode = 200): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
            header('X-Content-Type-Options: nosniff');
        }

        echo json_encode($data, self::JSON_FLAGS);
        exit;
    }

    /**
     * Envía una respuesta JSON estandarizada de éxito
     *
     * @param mixed $data
     */
    protected function jsonSuccess($data = null, string $message = 'Operación exitosa', int $statusCode = 200, array $meta = []): void
    {
        $payload = [
            'status'  => 'ok',
            'ok'      => true,
            'message' => $message,
            'data'    => $data,
        ];

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        $this->json($payload, $statusCode);
    }

    /**
     * Envía una respuesta JSON estandarizada de error
     *
     * @param mixed $details
     */
    protected function jsonError(string $message, int $statusCode = 400, $details = null): void
    {
        $payload = [
            'status'  => 'error',
            'ok'      => false,
            'message' => $message,
            'code'    => $statusCode,
        ];

        if ($details !== null) {
            $payload['details'] = $details;
        }

        $this->json($payload, $statusCode);
    }

    /**
     * Obtiene un parámetro de la query string ($_GET)
     *
     * @param mixed $default
     * @return mixed
     */
    protected function get(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Obtiene un parámetro de POST ($_POST)
     *
     * @param mixed $default
     * @return mixed
     */
    protected function post(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Obtiene un parámetro de $_GET o $_POST
     *
     * @param mixed $default
     * @return mixed
     */
    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Lee y decodifica el cuerpo de la petición en formato JSON (php://input)
     *
     * @return array<string, mixed>
     */
    protected function jsonInput(): array
    {
        $raw = file_get_contents('php://input');
        if (empty($raw)) {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Determina si la petición actual es de tipo POST
     */
    protected function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /**
     * Determina si la petición actual es de tipo GET
     */
    protected function isGet(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET';
    }

    /**
     * Determina si la petición se realizó vía AJAX / Fetch
     */
    protected function isAjax(): bool
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }

    /**
     * Retorna el ID del usuario autenticado actual o null
     */
    protected function userId(): ?int
    {
        return Auth::id();
    }

    /**
     * Retorna el nombre del usuario autenticado
     */
    protected function userName(): ?string
    {
        return Auth::user();
    }

    /**
     * Retorna el rol del usuario autenticado
     */
    protected function userRole(): ?int
    {
        Auth::start();
        return isset($_SESSION['role']) ? (int)$_SESSION['role'] : null;
    }

    /**
     * Verifica si el usuario tiene una sesión activa válida
     */
    protected function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Exige que el usuario esté autenticado; si no lo está, redirige a login o envía 401 si es AJAX
     */
    protected function requireAuth(): void
    {
        if (!$this->isAuthenticated()) {
            if ($this->isAjax()) {
                $this->jsonError('Sesión no válida o expirada', 401);
            }
            $this->redirect('index.php?error=timeout');
        }
    }

    /**
     * Redirige a otra URL y finaliza la ejecución
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Resuelve la ruta física del archivo de vista
     */
    private function resolveViewPath(string $viewPath): string
    {
        if (substr($viewPath, -4) === '.php' && file_exists($viewPath)) {
            return $viewPath;
        }

        $cleaned = ltrim($viewPath, '/\\');
        if (substr($cleaned, -4) !== '.php') {
            $cleaned .= '.php';
        }

        return dirname(__DIR__) . '/Views/' . $cleaned;
    }
}
