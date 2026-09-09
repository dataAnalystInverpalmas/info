# Databricks notebook source
# MAGIC %md
# MAGIC # 📡 API Informes Agrícolas → Databricks
# MAGIC
# MAGIC Notebook para importar en **Databricks** (Python). Lee los 6 endpoints de `http://172.10.18.128:9258/api/v1/index.php`, maneja la autenticación por header `Authorization: Bearer` (la API mantiene el fallback `api_key` por retrocompatibilidad) y la paginación automática, y opcionalmente persiste los datos en tablas **Delta**.
# MAGIC
# MAGIC | Endpoint | Descripción | Filas aprox. |
# MAGIC |---|---|---|
# MAGIC | `plano` | Plano de siembra por finca/bloque/tabla | 10.7 K |
# MAGIC | `variedades` | Catálogo de variedades | 470 |
# MAGIC | `colores` | Catálogo de colores | 51 |
# MAGIC | `curvas_rosas` | Curvas paramétricas por variedad (JSON) | 27 |
# MAGIC | `proyecciones` | Proyección de producción diaria | 599 K |
# MAGIC | `lonas` | Producción por lona (tallos/plantas) | 1.75 M |
# MAGIC
# MAGIC ---
# MAGIC
# MAGIC ### ⚠️ Requisitos
# MAGIC - El cluster debe poder alcanzar `172.10.18.128:9258` (misma red/VNet, o firewall abierto). Prueba con `curl` desde una terminal del nodo de control si tienes dudas.
# MAGIC - Librerías: `requests` (incluida en el Databricks Runtime), `pandas`, PySpark (ya incluidas).
# MAGIC - Token del API: usar **Databricks Secrets** (recomendado) o definirlo en la celda de configuración.

# COMMAND ----------

# MAGIC %md
# MAGIC ## 1. Configuración

# COMMAND ----------

API_BASE = "http://172.10.18.128:9258/api/v1/index.php"

# ── Token del API ──────────────────────────────────────────────────────────────
# Opción A (recomendada): Databricks Secrets
#   1) Crea el scope:
#        databricks secrets create-scope informes_api
#   2) Guarda el token:
#        databricks secrets put-secret informes_api api_key --string-value "<TU_TOKEN>"
try:
    API_KEY = dbutils.secrets.get(scope="informes_api", key="api_key")
    print("API_KEY leída desde secrets 'informes_api'.")
except Exception:
    # Opción B (solo para pruebas locales / no versiones este notebook con el token real):
    API_KEY = "REEMPLAZA_CON_TU_TOKEN"
    print("No se encontró el secreto 'informes_api.api_key'. Usa la constante de respaldo o configura secrets.")

if API_KEY == "" or API_KEY.startswith("REEMPLAZA"):
    raise RuntimeError(
        "Configura el token: o crea el secret 'informes_api/api_key' o edita API_KEY en esta celda."
    )

# ── Delta ──────────────────────────────────────────────────────────────────────
DELTA_SCHEMA   = "default"            # catálogo/esquema destino (Unity Catalog o metastore)
WRITE_TO_DELTA = False                 # cambia a True para persistir en tablas Delta

# ── Modo de ejecución ──────────────────────────────────────────────────────────
# 'test'  → prueba rápida con un límite de páginas por endpoint
# 'full'  → descarga completa de todos los endpoints
MODE = "test"  # 'test' | 'full'

# ── Guards ─────────────────────────────────────────────────────────────────────
MAX_PAGES_TEST = 2       # páginas a leer por endpoint en modo test
HTTP_TIMEOUT   = 120     # segundos por request
HTTP_RETRIES   = 3       # reintentos por request (con backoff)

print(f"API_BASE={API_BASE}")
print(f"MODE={MODE} | WRITE_TO_DELTA={WRITE_TO_DELTA} | DELTA_SCHEMA={DELTA_SCHEMA}")

# COMMAND ----------

# MAGIC %md
# MAGIC ## 2. Cliente HTTP con paginación automática
# MAGIC
# MAGIC La API responde `{"status", "data", "total", "page", "pageSize", "filters", "generated_at"}`.
# MAGIC Cada endpoint tiene un límite de `pageSize` (500–5000). Este cliente itera todas las páginas hasta completar `total`.

# COMMAND ----------

import time
import requests
import pandas as pd
from pyspark.sql import SparkSession

# 'spark' ya existe en Databricks, pero dejamos la referencia explícita por claridad.
spark = SparkSession.builder.getOrCreate()

# Header estándar: Authorization: Bearer <token>. Evita exponer el token en la URL.
HEADERS = {"Authorization": f"Bearer {API_KEY}"}


def fetch_endpoint(endpoint, page_size=5000, filters=None, max_pages=None, retries=HTTP_RETRIES):
    """
    Descarga todas las páginas de un endpoint y devuelve (rows, total, pages_leidas).

    endpoint  : nombre del endpoint ('plano', 'variedades', 'colores',
               'curvas_rosas', 'proyecciones', 'lonas')
    page_size : filas por página (respetando el tope del servidor)
    filters   : dict opcional de filtros GET, ej. {"fecha_inicio": "2026-01-01"}
    max_pages : tope de páginas (para pruebas). None = todas.
    """
    params = {"endpoint": endpoint, "pageSize": page_size}
    if filters:
        params.update(filters)

    rows, page = [], 1
    while True:
        params["page"] = page
        payload = None
        for attempt in range(retries):
            try:
                resp = requests.get(API_BASE, params=params, headers=HEADERS, timeout=HTTP_TIMEOUT)
                resp.raise_for_status()
                payload = resp.json()
                break
            except Exception as exc:
                if attempt == retries - 1:
                    raise RuntimeError(f"GET {endpoint} (page={page}) falló: {exc}") from exc
                time.sleep(2 ** attempt)

        if not payload or payload.get("status") != "ok":
            raise RuntimeError(f"La API respondió un error en {endpoint} (page={page}): {payload}")

        data = payload.get("data") or []
        rows.extend(data)
        total = payload.get("total", len(rows))

        if len(rows) >= total or not data:
            return rows, total, page
        if max_pages and page >= max_pages:
            print(f"  * {endpoint}: detenido por max_pages={max_pages} ({len(rows)}/{total} filas)")
            return rows, total, page
        page += 1


def to_spark_df(rows, name):
    """Convierte la lista de dicts a DataFrame de Spark infiriendo el esquema."""
    if not rows:
        print(f"  ! {name}: sin datos (0 filas). DataFrame vacío.")
        return spark.createDataFrame([], "id long")
    pdf = pd.DataFrame(rows).fillna(value=pd.NA).fillna("")
    return spark.createDataFrame(pdf).replace("", None).dropDuplicates()


# COMMAND ----------

# MAGIC %md
# MAGIC ## 3. Configuración de endpoints
# MAGIC
# MAGIC Ajusta `FACTORES_POR_ENDPOINT` si quieres acotar volúmenes (ej. filtrar `lonas` por rango de fecha en modo `full`).

# COMMAND ----------

# page_size debe respetar el tope del servidor por endpoint.
ENDPOINTS = {
    "plano":         {"page_size": 5000, "filters": None},
    "variedades":    {"page_size": 2000, "filters": None},
    "colores":       {"page_size": 500,  "filters": None},
    "curvas_rosas":  {"page_size": 1000, "filters": None},
    "proyecciones":  {"page_size": 5000, "filters": None},
    # Para producción, recomienda acotar por fecha, ej. {"fecha_inicio": "2026-01-01"}
    "lonas":         {"page_size": 5000, "filters": None},
}

# En modo test se lee una fracción de cada endpoint.
if MODE == "test":
    FECTH_MAX_PAGES = MAX_PAGES_TEST
else:
    FECTH_MAX_PAGES = None

print("Endpoints registrados:", ", ".join(ENDPOINTS.keys()))

# COMMAND ----------

# MAGIC %md
# MAGIC ## 4. Descarga de datos a DataFrames
# MAGIC
# MAGIC Correr todas las celdas de esta sección en modo `full` puede tardar varios minutos (`lonas` ≈ 1.75 M filas → ~350 requests).

# COMMAND ----------

dataframes = {}   # endpoint → (DataFrame, total, páginas)
dimensions = ["variedades", "colores", "curvas_rosas"]   # catálogos pequeños
facts      = ["plano", "proyecciones", "lonas"]          # tablas grandes

order = dimensions + facts
for ep in order:
    cfg = ENDPOINTS[ep]
    página_start = time.time()
    print(f"[{ep}] descargando (pageSize={cfg['page_size']}) ...")
    rows, total, pages = fetch_endpoint(
        ep,
        page_size=cfg["page_size"],
        filters=cfg["filters"],
        max_pages=FECTH_MAX_PAGES,
    )
    df = to_spark_df(rows, ep)
    dataframes[ep] = (df, total, pages)
    print(f"  ✓ {ep}: {df.count()} filas en DataFrame | total servidor={total} | páginas={pages} | {time.time()-página_start:.1f}s")
    display(df.limit(5))

# COMMAND ----------

# MAGIC %md
# MAGIC ## 5. Persistencia en Delta
# MAGIC
# MAGIC Con `WRITE_TO_DELTA = True` guarda cada endpoint en una tabla Delta. El modo `overwrite` reemplaza la tabla completa (útil para refresco integral); `append` acumula (útil para cargas incrementales por fecha).
# MAGIC
# MAGIC Los campos tipo `curva`, `s1`, `s2`, `s3` llegan como **strings JSON** (ej. `[0.04, 0.11, ...]`). Si el proyecto los necesita como arreglos, se pueden `from_json` en una celda posterior.

# COMMAND ----------

if WRITE_TO_DELTA:
    WRITE_MODE = "overwrite"  # 'overwrite' | 'append' | 'merge'
    for ep, (df, total, pages) in dataframes.items():
        table = f"{DELTA_SCHEMA}.informes_{ep}"
        df.write.format("delta").mode(WRITE_MODE).saveAsTable(table)
        print(f"  ✓ {table}: {df.count()} filas guardadas")
else:
    print("WRITE_TO_DELTA=False → no se guardó nada. Cambia la variable en la celda de configuración y vuelve a ejecutar.")

# COMMAND ----------

# MAGIC %md
# MAGIC ## 6. Lectura posterior (recomendado en el proyecto de ciencia de datos)
# MAGIC
# MAGIC Ejemplos de cómo consultar los datos ya persistidos desde otros notebooks.

# COMMAND ----------

# Ejemplo: leer directamente de Delta (si ya persististe)
# longitudes = spark.sql(f"SELECT * FROM {DELTA_SCHEMA}.informes_plano")

# Ejemplo: muestra de los DataFrames en memoria sin persistir
for ep in ["variedades", "colores", "curvas_rosas"]:
    df, total, pages = dataframes[ep]
    print(f"--- {ep} ({df.count()} filas) ---")
    display(df)

# COMMAND ----------

# MAGIC %md
# MAGIC ## 7. Trabajo de ciencia de datos (mínimo viable)
# MAGIC
# MAGIC - **Explode los JSON de curvas** si necesitas una fila por punto de la curva.
# MAGIC - **Join** `proyecciones` con `plano` y `variedades` por `variedad`/`finca`/`bloque`.
# MAGIC - Crea **tablas Delta** a partir de `dataframes` y deja el trabajo de modelado en otro notebook que solo lea Delta (evita golpear el API repetidamente).

# COMMAND ----------

# Ejemplo: curvas como filas (variedad → curva en lista)
from pyspark.sql import functions as F

curvas = dataframes["curvas_rosas"][0]
if curvas.count() > 0:
    curvas_exploded = (
        curvas
        .withColumn("curva_array", F.from_json(F.col("curva"), "array<double>"))
        .withColumn("punto", F.posexplode("curva_array").alias("edad", "fmata"))
        .select("variedad", "ciclo", "edad", "fmata", "porcentaje_ciegos")
    )
    display(curvas_exploded)
# MAGIC %md
# MAGIC ---
# MAGIC **Nota**: el token NO debe quedar versionado. Si tu scope se llama distinto, ajusta la celda de configuración. Para refrescos programados, envuelve las celdas de las secciones 3–5 en un `notebook.run()` o un job de Databricks.