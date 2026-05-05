## Resumen del proyecto

Aplicación PHP de informes agrícolas en proceso de migración a arquitectura MVC orientada a objetos. El código refactorizado vive en `src/` (namespace `App\`). Las vistas de entrada (`views/`) actúan como despachadores delgados que instancian el controlador correspondiente. Los endpoints AJAX legacy en `ajax/` coexisten con los nuevos que enrutan a métodos estáticos de los controllers. La BD es MySQL (`informes`); la conexión principal es mysqli vía `funciones/conexion.php`.

---

## Estructura de directorios

```
raíz/
├── src/                        ← código MVC principal (namespace App\)
│   ├── autoload.php            ← PSR-4 manual (App\ → src/)
│   ├── Helpers/Database.php    ← envuelve la $conexion global para los modelos
│   ├── Controllers/            ← 25 controladores (App\Controllers\*)
│   ├── Models/                 ← 8 modelos activos (App\Models\*)
│   └── Views/                  ← 24+ carpetas de vistas (renderizadas por require_once)
├── views/                      ← despachadores de página (include conexion + new Controller)
├── ajax/                       ← endpoints AJAX (procedurales legacy y nuevos REST)
├── tables/                     ← scripts PHP/SQL para operaciones manuales sobre BD (no hay ruta web)
├── scripts/                    ← JS, CSS, migraciones SQL/PHP (se ejecutan manualmente si se necesitan)
├── funciones/
│   ├── conexion.php            ← bootstrap principal: mysqli $conexion, autoloaders, APP_SRC
│   └── funciones.php           ← utilidades generales
├── bd/conexion.php             ← clase Conexion (PDO) — usada solo por scripts legacy
├── config/config.php           ← zona horaria, APP_DEBUG, logging (log_evento())
├── layouts/                    ← plantillas HTML comunes (header, sidebar, footer)
├── archivos/                   ← Excel y dumps de datos (PhpSpreadsheet)
├── composer.json               ← define App\ → src/ (PSR-4); deps: Carbon, PhpSpreadsheet, mPDF
└── vendor/                     ← dependencias Composer
```

---

## Arquitectura MVC activa

### Patrón de flujo para páginas renderizadas

```
views/modulo.php
  └─ include funciones/conexion.php        // bootstrap + autoload
  └─ (new App\Controllers\ModuloController)->index()
       └─ App\Models\Modelo::getAll()      // llama Database::getConnection() → $conexion (mysqli)
       └─ require src/Views/Modulo/index.php
```

### Patrón de flujo para endpoints AJAX REST

```
ajax/modulo.php
  └─ require funciones/conexion.php
  └─ switch($_SERVER['REQUEST_METHOD'])
       └─ App\Controllers\ModuloController::crear() / listar() / actualizar() / eliminar()
```

### Patrón de flujo para endpoints AJAX legacy (aún en uso)

```
ajax/modulo_crud.php
  └─ include('../funciones/conexion.php')   // obtiene $conexion (mysqli)
  └─ switch($accion) { case 'create': ... } // preparar + bind_param + execute
```

---

## Capas del sistema

### `src/Helpers/Database.php`
Retorna la conexión global `$conexion` (mysqli). Todos los modelos deben usarlo:
```php
$conexion = Database::getConnection();
```
No mezclar con `bd/conexion.php` (PDO); son incompatibles.

### `src/Models/` — modelos disponibles
| Modelo | Tabla principal |
|---|---|
| Application | aplicaciones |
| Bitacora | bitacora |
| Flowervase | florvaso |
| Greenhouse | invernaderos |
| Program | programas |
| Programf | programasf |
| Proyecto | proyectos |
| Tarea | tareas |

Patrón estándar de modelo:
- Métodos estáticos: `getAll($usuario_id)`, `getById($id)`
- Consultas preparadas con `?` y `bind_param`
- Acceso multiusuario: `WHERE usuario_id = ? OR usuario_id IS NULL` (proyectos y tareas globales)

### `src/Controllers/` — controladores disponibles
ApplicationController, BitacoraController, CatalogCrudController, EntradaMaterialVegetalController, EvaluacionesCrudController, FlowervaseController, FormBautizoController, GreenhouseController, GrowplantingController, GrowrootController, KanbanController, LaborsFormController, LoadFilesController, PlantillaBautizoController, PowerBIController, PrintController, ProgramController, ProgramfController, ProyectoController, ReportEvaluationsController, ReportFvController, ReportProgramController, ReportProgramViewController, TareaController, TrazabilidadController

### `src/Views/` — vistas correspondientes
Cada carpeta tiene un `index.php` que recibe variables via `extract()` desde el controlador.

---

## Conexiones a base de datos

| Archivo | Tipo | Uso |
|---|---|---|
| `funciones/conexion.php` | mysqli global `$conexion` | **Principal.** Usado por todos los modelos OOP y endpoints nuevos |
| `bd/conexion.php` | clase `Conexion` (PDO) | Legacy. Solo scripts antiguos que aún no han sido migrados |
| `src/Helpers/Database.php` | Wrapper de `$conexion` | Punto de acceso desde modelos OOP |

Credenciales configurables por variables de entorno: `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`, `DB_PORT`. Si no están definidas se usan los valores por defecto en cada archivo.

---

## `tables/` — scripts de operaciones manuales
Contienen scripts PHP y SQL para operaciones sobre la BD que se ejecutan manualmente cuando se necesitan (migraciones, inserciones masivas, consultas ad-hoc). No tienen ruta web. Referencia antes de crear una migración para no duplicar trabajo.

## `scripts/` — assets y herramientas de desarrollo
JS del frontend (`proyectos.js`, `tareas.js`, `programs.js`, etc.), CSS, SQL de creación de tablas y PHP de migración. Los PHP se ejecutan manualmente desde CLI o navegador según necesidad.

---

## Convenciones que debes seguir

### Al agregar un nuevo módulo (patrón completo)
1. Crear `src/Models/NuevoModelo.php` con namespace `App\Models` y métodos estáticos.
2. Crear `src/Controllers/NuevoController.php` con namespace `App\Controllers`.
3. Crear `src/Views/NuevoModulo/index.php` recibiendo variables via `extract()`.
4. Crear `views/nuevo_modulo.php` como despachador delgado.
5. Si necesita AJAX REST: crear `ajax/nuevo_modulo.php` que enrute al controller estático.

### Consultas preparadas (mysqli)
```php
$conexion = Database::getConnection();
$stmt = $conexion->prepare("SELECT ... WHERE campo = ?");
$stmt->bind_param("s", $valor);
$stmt->execute();
$result = $stmt->get_result();
```

### Acceso multiusuario (proyectos/tareas globales)
```sql
WHERE (usuario_id = ? OR usuario_id IS NULL)
```

### Endpoints AJAX
- Salida siempre: `header('Content-Type: application/json; charset=utf-8'); echo json_encode($data);`
- Validar entrada: `$_POST['x'] ?? ''` o `$_GET['x'] ?? null`
- Nunca mezclar PDO y mysqli en el mismo flujo

---

## Bootstrap de la aplicación

`funciones/conexion.php` es el punto de arranque; hace:
1. Crea `$conexion` (mysqli) con credenciales de env o defaults
2. Carga `vendor/autoload.php` (Composer: Carbon, PhpSpreadsheet, mPDF…)
3. Carga `src/autoload.php` (PSR-4 manual para `App\`)
4. Define `$GLOBALS['src']` (URL base, configurable con `APP_SRC`)

Todos los despachadores (`views/*.php`) y endpoints AJAX deben incluir este archivo primero.

---

## Dependencias (composer.json)

| Paquete | Uso |
|---|---|
| `nesbot/carbon` | Fechas/horas |
| `phpoffice/phpspreadsheet` | Exportar/importar Excel |
| `mpdf/mpdf` | Generación de PDF |
| `twbs/bootstrap` | CSS/JS frontend |
| `datatables/datatables` | Tablas interactivas |

---

## Desarrollo local

```bash
composer install                     # instalar dependencias
php -S 0.0.0.0:9258 -t .            # servidor de desarrollo
```

URL base histórica: `http://172.10.18.128:9258`. Configurar `APP_SRC` en entorno local si difiere.

---

## Seguridad

- Credenciales en repositorio: externalizarlas en variables de entorno (`.env`). Archivo `.env.example` disponible como referencia.
- Nunca concatenar input del usuario en SQL; usar siempre sentencias preparadas.
- Validar `$_SESSION['id']` antes de usarlo como `usuario_id`.

---

## Errores frecuentes

| Síntoma | Causa probable |
|---|---|
| `prepare()` devuelve false | `$conexion` es null o es PDO en lugar de mysqli |
| Proyecto existe pero no aparece en tabla | Query filtra solo `usuario_id = ?`, ignorar registros con `usuario_id IS NULL` |
| Error 500 en vista | `extract()` en controller no pasó la variable que la vista espera |
| Clase no encontrada | No se incluyó `funciones/conexion.php` (que carga los autoloaders) |

---

## Archivos clave para inspección rápida

- `funciones/conexion.php` — bootstrap completo del sistema
- `src/autoload.php` — cómo se resuelven los namespaces
- `src/Helpers/Database.php` — puente entre modelos OOP y conexión global
- `src/Models/Proyecto.php` — modelo de referencia con patrón completo
- `src/Controllers/ProyectoController.php` — controller de referencia
- `views/proyectos.php` — despachador de referencia
- `ajax/proyecto_crud.php` — endpoint AJAX legacy de referencia
- `ajax/proyectos.php` — endpoint AJAX REST de referencia
- `ajax/_crud_dynamic_common.php` — helpers reutilizables para CRUD genérico
