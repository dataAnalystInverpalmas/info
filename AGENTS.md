# Reglas y Estándares para Agentes de IA (Inverpalmas 2026)

Este repositorio utiliza una arquitectura MVC en PHP 8.2+ con PSR-4 sobre `src/`, complementada por Bootstrap 5.3 y jQuery 3.7.1. Todos los agentes de IA deben seguir estas directrices obligatoriamente:

## 1. Diseño y UI (Bootstrap 5.3.3)
- **NUNCA** usar clases obsoletas de Bootstrap 4 (`mr-*`, `ml-*`, `pr-*`, `pl-*`, `text-left`, `text-right`, `font-weight-bold`, `badge-pill`, `data-toggle`, `data-dismiss`).
- **SIEMPRE** usar la sintaxis moderna de Bootstrap 5:
  - Espaciado: `me-*`, `ms-*`, `pe-*`, `ps-*`, `gap-*`, `g-*`.
  - Tipografía: `fw-bold`, `fw-semibold`, `text-start`, `text-end`.
  - Badges: `badge rounded-pill bg-<color>`.
  - Modales / Offcanvas / Toasts: `data-bs-toggle="..."`, `data-bs-target="..."`, `data-bs-dismiss="..."`, `<button class="btn-close">`.
  - Toasts globales: llamar a `window.showAppToast(mensaje, tipo, titulo)`.

## 2. JavaScript y jQuery (3.7.1)
- La versión activa en `vendor/components/jquery/jquery.min.js` es **3.7.1**.
- Usar funciones modernas de jQuery y JavaScript ES6+ (`const`, `let`, `fetch`, async/await, arrow functions).
- Para inicializar DataTables, usar la integración oficial de Bootstrap 5 y no mezclar bibliotecas duplicadas.

## 3. Backend y Arquitectura MVC
- Nuevos controladores deben ubicarse en `src/Controllers/` y extender de `App\Controllers\BaseController`.
- Utilizar los helpers del `BaseController`:
  - `$this->render('Modulo/index', $data)`
  - `$this->jsonSuccess($data, $message, $code)`
  - `$this->jsonError($message, $code)`
  - `$this->get($key)`, `$this->post($key)`, `$this->jsonInput()`
  - `$this->requireAuth()`
- Nuevos modelos deben ubicarse en `src/Models/` y utilizar `App\Helpers\Database`:
  - `Database::fetchAll($sql, $params)`
  - `Database::fetchOne($sql, $params)`
  - `Database::execute($sql, $params)`
  - `Database::insert($tabla, $datos)`
  - **NUNCA** concatenar variables directamente en cadenas SQL; usar siempre marcadores `?` y Prepared Statements.

## 4. API RESTful (`api/v1/`)
- Endpoints modulares van en `api/endpoints/<nombre>.php`.
- Quedan accesibles automáticamente vía `GET/POST /api/v1/?endpoint=<nombre>`.
- Usar siempre `Response::paginate()`, `Response::success()`, o `Response::error()`.
- Autenticación obligatoria vía header `Authorization: Bearer <token>` gestionada automáticamente por `Auth::verify()`.

Para detalles completos y plantillas de código, consultar [docs/AI_FRAMEWORK_GUIDE.md](file:///c:/xampp/htdocs/info/docs/AI_FRAMEWORK_GUIDE.md).
