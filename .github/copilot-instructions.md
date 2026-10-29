# Instrucciones de Arquitectura para GitHub Copilot y Agentes de IA

> **Nota importante**: Para las reglas estrictas de código y plantillas completas, consulta siempre [`AGENTS.md`](../AGENTS.md) y [`docs/AI_FRAMEWORK_GUIDE.md`](../docs/AI_FRAMEWORK_GUIDE.md).

## 1. Resumen del Proyecto

Aplicación web de gestión agrícola e inteligencia de producción (**Inverpalmas**) en **PHP 8.2+** con arquitectura **MVC PSR-4** bajo `src/` (namespace `App\`), UI en **Bootstrap 5.3.3** + **jQuery 3.7.1**, API RESTful en `api/v1/` y microservicio analítico en `python_service/`.

---

## 2. Estructura de Directorios

```text
raíz/
├── src/                        ← Código MVC principal (namespace App\)
│   ├── autoload.php            ← Autoloader PSR-4 (App\ → src/)
│   ├── Helpers/
│   │   ├── Database.php        ← Capa unificada de BD (fetchAll, fetchOne, execute, insert, getConnection, getPdo)
│   │   ├── Auth.php            ← Control de acceso RBAC y auditoría de sesiones
│   │   ├── FechasReporte.php   ← Cálculos de semanas ISO y fechas agrícolas
│   │   └── PythonClient.php    ← Cliente HTTP para microservicio FastAPI
│   ├── Controllers/            ← Controladores (extienden de App\Controllers\BaseController)
│   ├── Models/                 ← Modelos con consultas preparadas vía App\Helpers\Database
│   └── Views/                  ← Vistas renderizadas por BaseController::render()
├── funciones/
│   └── conexion.php            ← Bootstrap único: carga .env, sesión segura, autoloaders y conexión MySQL
├── views/                      ← Despachadores delgados (instancian el Controller respectivo)
├── dist/
│   └── tables.php              ← Registro de rutas (?report= y ?table=) y validación de permisos
├── ajax/                       ← Endpoints AJAX JSON
├── api/                        ← API RESTful v1 (Bearer Token, endpoints en api/endpoints/)
├── tables/                     ← Scripts de importación de matrices Excel a MySQL
├── scripts/                    ← JS, CSS, fuentes locales y migraciones SQL
├── layouts/                    ← Plantillas base (layout.php, header.php, footer.php)
└── docs/                       ← Documentación de arquitectura y guías de módulos
```

---

## 3. Reglas Clave de Desarrollo

1. **Base de Datos Unificada (`App\Helpers\Database`)**:
   - **No crear archivos de conexión adicionales.** Toda la configuración vive en `.env` y se inicializa en `funciones/conexion.php`.
   - En los Modelos (`src/Models/`), usar siempre `Database::fetchAll($sql, $params)`, `Database::fetchOne($sql, $params)`, `Database::execute($sql, $params)` o `Database::insert($tabla, $datos)`.
   - Si algún flujo específico requiere PDO o MySQLi directo, usar `Database::getPdo()` o `Database::getConnection()`.
   - **Nunca** concatenar variables del usuario en SQL; usar siempre marcadores `?`.

2. **Controladores (`src/Controllers/`)**:
   - Extender siempre de `App\Controllers\BaseController`.
   - Usar `$this->requireAuth()`, `$this->render('Modulo/index', $data)`, `$this->jsonSuccess()`, `$this->jsonError()`.

3. **Frontend (Bootstrap 5.3.3 + jQuery 3.7.1)**:
   - Usar exclusivamente clases de Bootstrap 5 (`me-*`, `ms-*`, `pe-*`, `ps-*`, `fw-bold`, `text-start`, `text-end`, `data-bs-toggle`, `data-bs-dismiss`, `btn-close`).
   - No usar sintaxis obsoleta de Bootstrap 4 (`mr-*`, `ml-*`, `data-toggle`, `font-weight-bold`).
