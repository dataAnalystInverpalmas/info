import os
from urllib.parse import quote_plus
import pandas as pd
from sqlalchemy import create_engine, text
from fastapi import Header, HTTPException, status

DB_HOST = os.getenv("DB_HOST", "db")
DB_PORT = os.getenv("DB_PORT", "3306")
DB_NAME = os.getenv("DB_NAME", "info")
DB_USER = os.getenv("DB_USER", "root")
DB_PASS = os.getenv("DB_PASS", "AdmSys2014")
API_SECRET_TOKEN = os.getenv(
    "API_SECRET_TOKEN",
    "9035232260ca3fc11faa82b6b32b20605fe55c242e6903ca2f7e0887c3e9be17",
)

DATABASE_URL = (
    f"mysql+pymysql://{quote_plus(DB_USER)}:{quote_plus(DB_PASS)}"
    f"@{DB_HOST}:{DB_PORT}/{DB_NAME}?charset=utf8mb4"
)

engine = create_engine(
    DATABASE_URL,
    pool_pre_ping=True,
    pool_recycle=1800,
    pool_size=5,
    max_overflow=10,
)


def verify_token(authorization: str | None = Header(default=None)) -> None:
    """Valida el Bearer token compartido con PHP (`API_SECRET_TOKEN`)."""
    if not authorization or not authorization.startswith("Bearer "):
        raise HTTPException(
            status_code=status.HTTP_401_UNAUTHORIZED,
            detail="Token de autorización requerido (Bearer <token>)",
        )
    token = authorization.removeprefix("Bearer ").strip()
    if token != API_SECRET_TOKEN:
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Token de autorización inválido",
        )


def query_to_dataframe(sql: str, params: dict | None = None) -> pd.DataFrame:
    """Ejecuta una consulta SQL parametrizada y retorna un DataFrame de Pandas."""
    with engine.connect() as conn:
        return pd.read_sql_query(text(sql), conn, params=params or {})


def execute_sql(sql: str, params: dict | None = None) -> int:
    """Ejecuta una sentencia SQL (INSERT/UPDATE/CREATE) dentro de una transacción."""
    with engine.begin() as conn:
        result = conn.execute(text(sql), params or {})
        return result.rowcount

