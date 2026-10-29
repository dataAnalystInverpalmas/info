from typing import Literal
import numpy as np
from fastapi import APIRouter, Depends, HTTPException
from pydantic import BaseModel, Field
from app.database import verify_token, query_to_dataframe

router = APIRouter(
    prefix="/matrices",
    tags=["Matrices y Consultas Complejas"],
    dependencies=[Depends(verify_token)],
)


class MatrixOperationRequest(BaseModel):
    operation: Literal[
        "multiply",
        "add",
        "subtract",
        "transpose",
        "inverse",
        "determinant",
        "eigen",
        "solve_linear",
        "correlation",
        "covariance",
        "svd",
    ] = Field(..., description="Operación matricial a ejecutar con NumPy")
    matrix_a: list[list[float]] = Field(..., description="Matriz principal A (2D)")
    matrix_b: list[list[float]] | list[float] | None = Field(
        default=None,
        description="Matriz B (para multiply/add/subtract) o vector b (para solve_linear Ax = b)",
    )
    decimals: int = Field(default=6, ge=0, le=12, description="Decimales de redondeo")


class SqlCorrelationRequest(BaseModel):
    sql: str = Field(
        ...,
        description="Consulta SELECT de solo lectura cuyas columnas numéricas se analizarán",
    )
    params: dict | None = Field(default=None, description="Parámetros con nombre (:param)")
    columns: list[str] | None = Field(
        default=None,
        description="Columnas numéricas opcionales; si se omite toma todas las numéricas",
    )
    decimals: int = Field(default=4, ge=0, le=10)


@router.post("/operar")
def operar_matrices(payload: MatrixOperationRequest) -> dict:
    """Ejecuta operaciones de álgebra lineal y matrices usando NumPy."""
    try:
        a = np.array(payload.matrix_a, dtype=float)
        if a.ndim != 2 or a.size == 0:
            raise ValueError("matrix_a debe ser una matriz bidimensional no vacía.")

        op = payload.operation
        d = payload.decimals

        if op in {"multiply", "add", "subtract"}:
            if payload.matrix_b is None:
                raise ValueError(f"La operación '{op}' requiere 'matrix_b'.")
            b = np.array(payload.matrix_b, dtype=float)
            if op == "multiply":
                res = np.matmul(a, b)
            elif op == "add":
                res = np.add(a, b)
            else:
                res = np.subtract(a, b)
            return {
                "success": True,
                "operation": op,
                "shape": list(res.shape),
                "result": np.round(res, d).tolist(),
            }

        if op == "transpose":
            res = a.T
            return {
                "success": True,
                "operation": op,
                "shape": list(res.shape),
                "result": np.round(res, d).tolist(),
            }

        if op == "determinant":
            det = float(np.linalg.det(a))
            return {
                "success": True,
                "operation": op,
                "result": round(det, d),
            }

        if op == "inverse":
            inv = np.linalg.inv(a)
            return {
                "success": True,
                "operation": op,
                "shape": list(inv.shape),
                "result": np.round(inv, d).tolist(),
            }

        if op == "eigen":
            eigenvalues, eigenvectors = np.linalg.eig(a)
            return {
                "success": True,
                "operation": op,
                "eigenvalues": np.round(eigenvalues.real, d).tolist(),
                "eigenvectors": np.round(eigenvectors.real, d).tolist(),
            }

        if op == "solve_linear":
            if payload.matrix_b is None:
                raise ValueError("Para 'solve_linear' (Ax = b) debes enviar 'matrix_b'.")
            b = np.array(payload.matrix_b, dtype=float)
            x = np.linalg.solve(a, b)
            return {
                "success": True,
                "operation": op,
                "result": np.round(x, d).tolist(),
            }

        if op == "correlation":
            corr = np.corrcoef(a, rowvar=False)
            return {
                "success": True,
                "operation": op,
                "result": np.round(corr, d).tolist(),
            }

        if op == "covariance":
            cov = np.cov(a, rowvar=False)
            return {
                "success": True,
                "operation": op,
                "result": np.round(cov, d).tolist(),
            }

        if op == "svd":
            u, s, vh = np.linalg.svd(a, full_matrices=False)
            return {
                "success": True,
                "operation": op,
                "u": np.round(u, d).tolist(),
                "singular_values": np.round(s, d).tolist(),
                "vh": np.round(vh, d).tolist(),
            }

        raise ValueError(f"Operación no soportada: {op}")

    except (ValueError, np.linalg.LinAlgError) as exc:
        raise HTTPException(status_code=400, detail=str(exc)) from exc


@router.post("/analizar-consulta")
def analizar_consulta_sql(payload: SqlCorrelationRequest) -> dict:
    """
    Ejecuta una consulta SELECT en MySQL, construye un DataFrame de Pandas y devuelve:
    - Resumen estadístico (media, desviación, min, max, cuartiles)
    - Matriz de correlación
    - Matriz de covarianza
    """
    sql_clean = payload.sql.strip()
    if not sql_clean.lower().startswith("select"):
        raise HTTPException(
            status_code=400,
            detail="Solo se permiten consultas SELECT en este endpoint.",
        )

    try:
        df = query_to_dataframe(sql_clean, payload.params)
        if df.empty:
            return {
                "success": True,
                "rows_count": 0,
                "message": "La consulta no devolvió filas.",
            }

        if payload.columns:
            numeric_df = df[payload.columns].select_dtypes(include=[np.number])
        else:
            numeric_df = df.select_dtypes(include=[np.number])

        if numeric_df.empty:
            raise ValueError("No se encontraron columnas numéricas para operar matrices.")

        d = payload.decimals
        corr_df = numeric_df.corr().round(d).fillna(0.0)
        cov_df = numeric_df.cov().round(d).fillna(0.0)
        stats_df = numeric_df.describe().round(d).fillna(0.0)

        return {
            "success": True,
            "rows_count": int(len(df)),
            "columns": list(numeric_df.columns),
            "summary": stats_df.to_dict(),
            "correlation_matrix": corr_df.values.tolist(),
            "covariance_matrix": cov_df.values.tolist(),
        }
    except Exception as exc:
        raise HTTPException(status_code=400, detail=str(exc)) from exc

