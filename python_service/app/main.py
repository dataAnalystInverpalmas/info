from contextlib import asynccontextmanager
from fastapi import Depends, FastAPI
from app.database import verify_token
from app.routers.matrices import router as matrices_router
from app.scheduler import (
    job_history,
    scheduler,
    start_scheduler,
    stop_scheduler,
    tarea_resumen_matricial_programada,
)


@asynccontextmanager
async def lifespan(app: FastAPI):
    start_scheduler()
    yield
    stop_scheduler()


app = FastAPI(
    title="Inverpalmas Python Analytics & Matrix Service",
    version="1.0.0",
    description="Microservicio FastAPI para operaciones matriciales (NumPy/Pandas), consultas complejas y tareas programadas.",
    lifespan=lifespan,
)

app.include_router(matrices_router)


@app.get("/health")
def health_check() -> dict:
    """Endpoint público para verificar estado del contenedor y del programador de tareas."""
    return {
        "status": "ok",
        "service": "python_api",
        "scheduler_running": scheduler.running,
        "scheduled_jobs_count": len(scheduler.get_jobs()),
    }


@app.get("/jobs", dependencies=[Depends(verify_token)])
def listar_tareas_programadas() -> dict:
    """Lista todas las tareas programadas activas y su próxima fecha de ejecución."""
    jobs = [
        {
            "id": job.id,
            "name": job.name,
            "next_run_time": job.next_run_time.isoformat() if job.next_run_time else None,
            "trigger": str(job.trigger),
        }
        for job in scheduler.get_jobs()
    ]
    return {
        "success": True,
        "jobs": jobs,
        "last_executions": job_history,
    }


@app.post("/jobs/ejecutar-ahora", dependencies=[Depends(verify_token)])
def ejecutar_tarea_manual() -> dict:
    """Dispara inmediatamente la tarea matricial programada bajo demanda desde PHP."""
    resultado = tarea_resumen_matricial_programada()
    return {
        "success": resultado.get("status") == "ok",
        "result": resultado,
    }

