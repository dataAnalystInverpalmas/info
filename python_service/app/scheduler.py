import logging
from datetime import datetime
import numpy as np
from apscheduler.schedulers.background import BackgroundScheduler
from apscheduler.triggers.cron import CronTrigger
from apscheduler.triggers.interval import IntervalTrigger
from app.database import execute_sql

logger = logging.getLogger("python_service.scheduler")
scheduler = BackgroundScheduler(timezone="America/Bogota")

# Registro en memoria de la última ejecución de tareas programadas
job_history: dict[str, dict] = {}


def ensure_analytics_cache_table() -> None:
    """Crea (si no existe) una tabla auxiliar en MySQL para guardar resultados de tareas programadas."""
    sql = """
    CREATE TABLE IF NOT EXISTS `python_analytics_cache` (
        `job_key` VARCHAR(100) NOT NULL,
        `payload_json` JSON NOT NULL,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`job_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    """
    execute_sql(sql)


def tarea_resumen_matricial_programada() -> dict:
    """
    Tarea automática de ejemplo:
    Calcula indicadores matriciales y guarda el resultado precalculado en MySQL
    para que PHP lo consulte instantáneamente sin esperar cálculo.
    """
    import json

    try:
        ensure_analytics_cache_table()

        # Ejemplo de operación matricial (reemplazable por consulta real con query_to_dataframe)
        matriz_base = np.array([
            [12.5, 8.0, 15.2],
            [9.1, 14.3, 11.8],
            [16.0, 10.5, 13.4],
        ], dtype=float)

        covarianza = np.cov(matriz_base).round(4).tolist()
        correlacion = np.corrcoef(matriz_base).round(4).tolist()
        autovalores = np.linalg.eigvals(matriz_base).real.round(4).tolist()

        payload = {
            "status": "ok",
            "executed_at": datetime.now().isoformat(),
            "covarianza": covarianza,
            "correlacion": correlacion,
            "autovalores_reales": autovalores,
        }

        sql = """
        INSERT INTO `python_analytics_cache` (`job_key`, `payload_json`, `updated_at`)
        VALUES (:job_key, :payload_json, NOW())
        ON DUPLICATE KEY UPDATE
            `payload_json` = VALUES(`payload_json`),
            `updated_at` = NOW()
        """
        execute_sql(sql, {
            "job_key": "resumen_matricial_diario",
            "payload_json": json.dumps(payload),
        })

        job_history["resumen_matricial_diario"] = payload
        logger.info("Tarea programada 'resumen_matricial_diario' ejecutada correctamente.")
        return payload
    except Exception as exc:
        error_info = {
            "status": "error",
            "executed_at": datetime.now().isoformat(),
            "error": str(exc),
        }
        job_history["resumen_matricial_diario"] = error_info
        logger.exception("Error ejecutando tarea programada: %s", exc)
        return error_info


def start_scheduler() -> None:
    """Registra las tareas programadas e inicia el scheduler en segundo plano."""
    if scheduler.running:
        return

    # 1. Ejemplo por intervalo: cada 60 minutos
    scheduler.add_job(
        tarea_resumen_matricial_programada,
        trigger=IntervalTrigger(minutes=60),
        id="resumen_matricial_intervalo",
        name="Cálculo matricial automático cada 60 min",
        replace_existing=True,
    )

    # 2. Ejemplo estilo Cron: todos los días a las 02:00 AM (hora Bogotá)
    scheduler.add_job(
        tarea_resumen_matricial_programada,
        trigger=CronTrigger(hour=2, minute=0),
        id="resumen_matricial_nocturno",
        name="Cálculo matricial nocturno (02:00 AM)",
        replace_existing=True,
    )

    scheduler.start()
    logger.info("APScheduler iniciado con %d tareas registradas.", len(scheduler.get_jobs()))


def stop_scheduler() -> None:
    if scheduler.running:
        scheduler.shutdown(wait=False)

