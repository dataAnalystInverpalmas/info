## Resumen rápido

Este repositorio es una aplicación PHP procedimental orientada a informes agrícolas. La mayor parte de la lógica de back-end está en archivos PHP sueltos (carpeta `ajax/`, `views/`, `PDF/`, `scripts/`) que consultan la base de datos `informes` y devuelven HTML o JSON para consumo por front-end DataTables y páginas tradicionales.

## Estructura y componentes clave
- `ajax/` — Endpoints AJAX que devuelven JSON (p.ej. `ajax/dataHeaderBautizos.php`).
- `bd/` — Implementaciones PDO clásicas (ej. `bd/conexion.php`).
- `funciones/` — Conexión y utilidades comunes (ej. `funciones/conexion.php` que crea `$conexion` con mysqli).
- `archivos/` — Excel y dumps de datos usados por el equipo (referencia: `phpoffice/phpspreadsheet` en `composer.json`).
- `vendor/` y `composer.json` — Dependencias: Carbon, PhpSpreadsheet, mPDF, Bootstrap, DataTables.

Observación importante: hay dos patrones de conexión a DB en el repo: `bd/conexion.php` (PDO, credenciales distintas) y `funciones/conexion.php` (mysqli, otras credenciales). Muchos scripts AJAX incluyen `funciones/conexion.php` y esperan un objeto mysqli `$conexion`.

## Convenciones de código que debes respetar
- Estilo procedural: evita reescribir en OOP salvo que hagas una refactorización controlada.
- Inclusiones relativas: los scripts usan `include("../funciones/conexion.php")` y rutas relativas. Mantén cuidado con la ruta de trabajo (cwd) al ejecutar scripts desde CLI.
- Salida de endpoints AJAX: usualmente `echo json_encode($data);`. Los agentes deben preservar este comportamiento y mantener UTF-8.
- Uso de consultas preparadas: ver `ajax/dataHeaderBautizos.php` — se usan `?` y `bind_param("sssss", ...)` con mysqli. Sigue ese patrón cuando modifiques endpoints.

## Flujo de datos típico (ejemplo concreto)
1. Frontend DataTable hace POST a `ajax/dataHeaderBautizos.php` con `{finca,bloque,variedad,temporada,tipo_siembra}`.
2. `ajax/dataHeaderBautizos.php` incluye `funciones/conexion.php` y prepara una consulta SQL compleja con `?` placeholders.
3. El endpoint ejecuta `$stmt->execute(); $result = $stmt->get_result();` y responde `json_encode($data)`.

## Comandos y flujo de desarrollo local
- Instalar dependencias: `composer install` (desde la raíz del repo). Esto creará `vendor/` para PhpSpreadsheet, mPDF, etc.
- Servir la app rápida (solo para desarrollo): `php -S 0.0.0.0:9258 -t .` desde la raíz. Muchas referencias URL internas esperan el host/puerto `172.10.18.128:9258` (ver `funciones/conexion.php`), así que al probar localmente replica el host/puerto o adapte `$_GLOBALS['src']` según tu entorno.
- Base de datos: la app espera una base MySQL llamada `informes`. Revisa credenciales en `funciones/conexion.php` y `bd/conexion.php` antes de ejecutar; confirma cuál usa el archivo que vas a modificar.

## Seguridad y secretos
- Credenciales aparecen en el repositorio (`bd/conexion.php`, `funciones/conexion.php`). No las expongas ni las subas a servicios públicos. Si automatizas tests o despliegues, externaliza credenciales en variables de entorno o archivos de configuración no versionados.

## Errores comunes y cómo depurarlos
- "Error en prepare()" en endpoints AJAX: normalmente significa que la conexión `$conexion` es null o no es mysqli; comprueba qué archivo de conexión se está incluyendo y si las credenciales/host son accesibles.
- Diferencias PDO vs mysqli: no mezcles llamadas (p.ej. no uses `->prepare` de PDO sobre un mysqli sin adaptar). Verifica el tipo de `$conexion`.

## Qué buscar cuando edits/añades código
- Si añades un nuevo endpoint en `ajax/`, sigue el patrón: validar `$_POST` (usa `$_POST['x'] ?? ''`), preparar consulta con `?`, `bind_param(...)`, `execute()`, `get_result()` y `json_encode`.
- Mantén SQL legible: muchas consultas usan joins con prefijos `informes.*`. Preserva el esquema `informes.` en consultas a menos que modifiques la configuración de BD.
- Si introduces composer/autoloaded classes, documenta la nueva dependencia en `composer.json` y ejecuta `composer install`.

## Archivos para inspeccionar primero (rápida lectura)
- `ajax/dataHeaderBautizos.php` — ejemplo completo de flujo AJAX/SQL.
- `funciones/conexion.php` y `bd/conexion.php` — contrastar para entender qué conexión usa cada script.
- `composer.json` — dependencias del proyecto.
- `index.php`, `home.php`, `login.php` — puntos de entrada web.

## Resultado esperado de los agentes
- Hacer cambios mínimos y coherentes con el estilo procedural.
- No eliminar o reescribir credenciales en masa; en su lugar, sugerir externalizarlos y documentarlo.
- Si propones refactor, añade una nota con riesgos (p.ej. cambiar mysqli→PDO puede romper muchos endpoints).

Si algo en estas notas está incompleto o quieres que añada ejemplos adicionales (p.ej. una plantilla para nuevos endpoints AJAX), dime cuál y lo añado.
