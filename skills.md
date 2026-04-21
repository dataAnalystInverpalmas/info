# Skills & Patrones del Proyecto — Inverpalmas

## Stack tecnológico

| Capa | Tecnología | Versión |
|---|---|---|
| Backend | PHP (procedural + MVC parcial) | 7.x / 8.x |
| Base de datos | MySQL (mysqli + PDO) | — |
| Frontend UI | Bootstrap | 4.3.1 |
| Tablas interactivas | DataTables + FixedColumns + Buttons + SearchBuilder | 1.10–1.11 |
| JS base | jQuery | 3.6.0 |
| Gráficas | Chart.js + chartjs-plugin-datalabels | 2.8.0 |
| PDF | mPDF | ^8.0 |
| Excel | PhpSpreadsheet | ^1.8 |
| Fechas | Carbon | ^1.39 |
| Iconos | FontAwesome | local `/fontawesome/` |
| Wizard UI | SmartWizard | 5 |
| Barcodes | JsBarcode | local `/scripts/JsBarcode.all.min.js` |
| Autoloader | PSR-4 (`App\` → `src/`) vía `src/autoload.php` | — |

---

## Arquitectura

### Patrón principal: PHP procedural

La mayoría del código sigue un estilo **PHP procedural** con inclusión directa de archivos:

```
index.php / home.php
  └─ layouts/layout.php          (plantilla HTML completa)
       ├─ layouts/header.php     (navbar Bootstrap, menús por rol)
       ├─ routing.php            (switch por $_GET para include de vistas)
       └─ layouts/footer.php
```

### Patrón secundario: MVC ligero (módulos Proyectos / Tareas / Bitácora / Greenhouse)

```
src/
  autoload.php          ← PSR-4 manual (sin composer dump-autoload)
  Controllers/          ← ProyectoController, TareaController, etc.
  Models/               ← Proyecto, Tarea, Bitacora, Greenhouse (mysqli)
  Helpers/Database.php  ← Singleton que expone $conexion global
  Views/
    Proyectos/index.php
    Tareas/index.php
    Bitacora/
    Greenhouse/
```

---

## Conexiones a base de datos

### funciones/conexion.php — mysqli (uso mayoritario)
```php
$conexion = new mysqli($host, $username, $password, $database, (int)$port);
$conexion->set_charset("utf8");
```
- Variables de entorno: `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`, `DB_PORT`
- Fallback: `db` / `inverpalmas` / `Inver2020!` / `informes` / `3306`
- Toda la capa `src/` la usa vía `App\Helpers\Database::getConnection()` (que expone el global `$conexion`).

### bd/conexion.php — PDO (uso puntual, credenciales distintas)
```php
$conexion = new PDO("mysql:host={$host};port={$port};dbname={$dbname}", $user, $pass, $opciones);
```
- Fallback: `172.10.18.128` / `root` / `AdmSys2014`
- **No mezclar**: PDO y mysqli no son intercambiables.

---

## Endpoints AJAX — patrón estándar

Ubicación: `ajax/*.php`  
Todos siguen esta estructura:

```php
<?php
include("../funciones/conexion.php");   // siempre con ruta relativa

$param = $_POST['param'] ?? '';         // validar con null-coalescing

$sql = "SELECT ... FROM informes.tabla WHERE columna = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $param);         // s=string, i=int, d=double
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);                // siempre json_encode, sin HTML extra
```

**Reglas:**
- Usar `?` como placeholder, nunca concatenar variables en SQL.
- Usar `$_POST['x'] ?? ''` para evitar undefined index.
- Preservar `informes.` como prefijo de esquema en JOINs.
- Respuesta siempre UTF-8; `$conexion->set_charset("utf8")` está en `funciones/conexion.php`.

### CRUD dinámico (`ajax/_crud_dynamic_common.php`)
Funciones reutilizables para CRUD genérico:
- `crud_json($payload)` — serializa y termina ejecución.
- `crud_bind_and_execute($stmt, $types, $params)` — bind dinámico con refs.
- `crud_get_table_meta($conexion, $table)` — lee `information_schema.COLUMNS`.

---

## Vistas (views/)

- `views/*.php` — vistas tradicionales incluidas por `routing.php`.
- `views/Proyectos/` y `views/Tareas/` — vistas del módulo MVC.
- Cada vista genera HTML con Bootstrap 4, modales y tablas DataTables.
- Los modales de Proyectos/Tareas usan clase `.modal-half`.
- Campos clave: `pDesc` (descripción proyecto), `tDesc` (descripción tarea).

---

## DataTables — patrones de uso

```javascript
$('#miTabla').DataTable({
    processing: true,
    serverSide: false,          // carga completa en cliente (patrón habitual)
    ajax: {
        url: 'ajax/endpoint.php',
        type: 'POST',
        data: { param1: val1 }
    },
    columns: [ { data: 'campo' }, ... ],
    fixedColumns: { leftColumns: 1 },   // cuando hay FixedColumns
    dom: 'Bfrtip',
    buttons: ['excel', 'pdf', 'copy']
});
```

- CSS DataTables: CDN `jquery.dataTables.min.css` + `dataTables.bootstrap4.min.css`
- JS DataTables: CDN `jquery.dataTables.min.js` + extensiones FixedColumns, Buttons, SearchBuilder

---

## Bootstrap 4 — patrones frecuentes

- Navbar fija en top con `fixed-top` y padding `padding-top: 5rem` en body.
- Layout de columnas: `container-fluid` > `row` > `col-sm-*`.
- Modales: `modal`, `modal-dialog`, `modal-content`, `modal-header/body/footer`.
- Botones: `btn btn-outline-success`, `btn btn-primary`, `btn btn-sm`.
- Dropdowns del menú: `btn-group` + `dropdown-menu` + `dropdown-item`.
- Roles de usuario: `$_SESSION['role']` controla visibilidad de menús.

---

## JavaScript — archivos por módulo

| Archivo | Módulo |
|---|---|
| `scripts/proyectos.js` | Proyectos |
| `scripts/tareas.js` / `tarea_panel.js` | Tareas |
| `scripts/arrangements_crud.js` | Arrangements |
| `scripts/catalog_crud.js` | Catálogos genéricos |
| `scripts/programs.js` / `programsf.js` | Programas |
| `scripts/filtros_bautizos.js` | Filtros bautizos |
| `scripts/main.js` / `mainSP.js` | Scripts generales |
| `scripts/scriptsProy.js` | Scripts Proyectos adicionales |

---

## Rutas y URL base

```php
$_GLOBALS['src'] = getenv('APP_SRC') ?: 'http://172.10.18.128:9258';
```

- Las vistas usan `$directorio` (alias de `$_GLOBALS['src']`) para construir URLs absolutas.
- Al desarrollar localmente replicar host/puerto o redefinir `APP_SRC` como variable de entorno.

---

## Servidor de desarrollo

```bash
php -S 0.0.0.0:9258 -t .
```

Dependencias:
```bash
composer install
```

---

## Convenciones de código

1. **Estilo procedural por defecto**; MVC solo en módulos bajo `src/`.
2. Rutas relativas para includes: `include("../funciones/conexion.php")`.
3. SQL con prefijo `informes.` en consultas multi-tabla.
4. Sin concatenación de variables en SQL — siempre `?` + `bind_param`.
5. Salida AJAX: solo `echo json_encode($data)`, sin HTML ni `var_dump`.
6. Autoloader `src/autoload.php` registra `App\` → `src/`; no requiere `composer dump-autoload`.

---

## Seguridad (recordatorio OWASP)

- Nunca concatenar `$_POST`/`$_GET` directo en SQL.
- Credenciales deben externalizarse en variables de entorno (`.env` no versionado).
- Validar y sanitizar entrada en todos los endpoints AJAX.
- Sesiones iniciadas con `session_start()` dentro de `funciones/conexion.php`.

---

## Dependencias composer.json

```json
{
  "require": {
    "nesbot/carbon": "^1.39",
    "phpoffice/phpspreadsheet": "^1.8",
    "mpdf/mpdf": "^8.0",
    "twbs/bootstrap": "4.3.1",
    "datatables/datatables": "^1.10",
    "components/jqueryui": "^1.12"
  },
  "autoload": {
    "psr-4": { "App\\": "src/" }
  }
}
```
