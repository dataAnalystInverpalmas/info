# Sistema Integral Inverpalmas (Arquitectura MVC 2026)

Plataforma web de gestión agrícola, proyecciones de producción, postcosecha (floreros/evaluaciones), planos de siembra y gestión operativa (Proyectos, Tareas, Kanban y Bitácora) con microservicio analítico en Python.

---

## 1. Mapa de Directorios del Proyecto

```text
c:\xampp\htdocs\info\
├── src/                        # Núcleo MVC (Namespace PSR-4: App\)
│   ├── autoload.php            # Autoloader PSR-4 nativo para App\
│   ├── Controllers/            # Controladores MVC (extienden de BaseController)
│   ├── Models/                 # Modelos de negocio y consultas SQL (usan Database helper)
│   ├── Views/                  # Vistas HTML/Bootstrap 5.3 organizadas por módulo
│   └── Helpers/
│       ├── Database.php        # Capa unificada de BD (fetchAll, fetchOne, execute, insert, getConnection, getPdo)
│       ├── Auth.php            # Permisos RBAC por rol, caché de sesión y auditoría de login
│       ├── FechasReporte.php   # Utilidades de calendario agrícola y semanas ISO
│       └── PythonClient.php    # Cliente HTTP hacia el microservicio Python (FastAPI)
├── funciones/
│   └── conexion.php            # Bootstrap central: carga .env, sesiones seguras, headers, autoloaders y conexión MySQL
├── views/                      # Launchers delgados de rutas web (instancian el Controller correspondiente)
├── ajax/                       # Endpoints AJAX para componentes dinámicos y DataTables
├── api/                        # API RESTful JSON (v1) con autenticación Bearer Token
│   ├── Auth.php                # Verificación de tokens API
│   ├── Response.php            # Respuestas JSON estandarizadas y paginación
│   ├── endpoints/              # Endpoints modulares (/api/v1/?endpoint=<nombre>)
│   └── v1/                     # Router de la API v1 y cargas masivas
├── layouts/                    # Plantilla maestra (layout.php, header.php con menú lateral, footer.php)
├── dist/
│   ├── tables.php              # Enrutador interno (?report=<clave> y ?table=<clave>) + control de permisos
│   └── server.php              # Receptor de subida de archivos Excel
├── tables/                     # Procesadores de importación y sincronización de matrices Excel a MySQL
├── scripts/                    # Assets estáticos: JS de módulos, hojas CSS, fuentes locales y migraciones SQL
├── python_service/             # Microservicio FastAPI (cálculos pesados, analítica e ingesta)
├── archivos/                   # Almacenamiento temporal de plantillas e importaciones Excel
├── docs/                       # Documentación técnica y guías para desarrolladores y agentes IA
├── AGENTS.md                   # Reglas obligatorias para Agentes de IA
└── index.php                   # Punto de entrada principal de la aplicación
```

---

## 2. Conexión a Base de Datos (Unificada)

El proyecto cuenta con **una única configuración y capa de conexión**:

1. **[`funciones/conexion.php`](funciones/conexion.php)**:
   - Es el **Bootstrap central** del sistema.
   - Lee automáticamente el archivo `.env` de la raíz (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, `APP_ENV`, `APP_SRC`).
   - Inicializa la sesión segura, cabeceras HTTP, `vendor/autoload.php` (Composer) y `src/autoload.php` (PSR-4).
   - Expone `$conexion` (`mysqli`) para retrocompatibilidad con scripts legacy.

2. **[`src/Helpers/Database.php`](src/Helpers/Database.php)** (`App\Helpers\Database`):
   - Es la **interfaz estándar para todos los Modelos y Controladores**.
   - Métodos principales con sentencias preparadas (`?`) automáticas:
     - `Database::fetchAll(string $sql, array $params = []): array`
     - `Database::fetchOne(string $sql, array $params = []): ?array`
     - `Database::execute(string $sql, array $params = []): int`
     - `Database::insert(string $table, array $data): int`
     - `Database::beginTransaction()`, `Database::commit()`, `Database::rollback()`
   - También expone `Database::getConnection()` (`mysqli`) y `Database::getPdo()` (`PDO` singleton) usando exactamente las mismas credenciales de `.env`.

---

## 3. Flujo de una Petición Web

1. **`index.php`** inicia sesión, carga `funciones/conexion.php` y renderiza `layouts/layout.php`.
2. **`layouts/layout.php`** carga `routing.php`.
3. **`routing.php`** muestra el login si no hay sesión activa o delega en `dist/tables.php`.
4. **`dist/tables.php`** valida permisos con `App\Helpers\Auth::can()` e incluye el launcher en `views/<modulo>.php`.
5. **`views/<modulo>.php`** instancia `App\Controllers\<Modulo>Controller` y ejecuta `$controller->index()`.
6. El **Controlador** consulta uno o varios Modelos en `src/Models/` y renderiza la vista ubicada en `src/Views/<Modulo>/index.php`.

---

## 4. Documentación Adicional

- **[`AGENTS.md`](AGENTS.md)**: Reglas rápidas obligatorias para asistentes de IA.
- **[`docs/AI_FRAMEWORK_GUIDE.md`](docs/AI_FRAMEWORK_GUIDE.md)**: Guía completa con plantillas Copy & Paste para crear Controladores, Modelos, Vistas Bootstrap 5.3 y Endpoints API REST.
- **[`docs/API_PROYECTOS_TAREAS_BITACORA.md`](docs/API_PROYECTOS_TAREAS_BITACORA.md)**: Documentación de endpoints del módulo de Gestión Operativa (PM).

