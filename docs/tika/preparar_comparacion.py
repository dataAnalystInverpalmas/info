"""Copia a local las carpetas de los modelos de Tika y genera la página de comparación.

Uso: python preparar_comparacion.py ID [ID ...] [--actualizar]
Ej.: python preparar_comparacion.py 547 546 499

- Copia \\\\172.10.18.2\\Usuarios\\Hilder\\Data\\tika\\<ID> a modelos/<ID> (si ya existe local no la
  vuelve a copiar, salvo con --actualizar).
- Agrega esquejes_futuros, proy1-3, guarde y salvamento de cada modelo y los inserta en
  plantilla_comparacion.html -> comparacion_<IDs>.html (abre en el navegador, sin internet de datos).
"""
import json
import shutil
import sys
from pathlib import Path

import pandas as pd

RED = Path(r"\\172.10.18.2\Usuarios\Hilder\Data\tika")
AQUI = Path(__file__).parent
LOCAL = AQUI / "modelos"
PLANTILLA = AQUI / "plantilla_comparacion.html"
MARCA = "/*__DATOS__*/null"

args = [a for a in sys.argv[1:] if not a.startswith("--")]
ACTUALIZAR = "--actualizar" in sys.argv
if not args:
    sys.exit(__doc__)


def copiar(m):
    dst = LOCAL / m
    if dst.exists() and not ACTUALIZAR:
        print(f"{m}: ya está en local")
        return dst
    src = RED / m
    if not src.exists():
        sys.exit(f"No existe {src}")
    if dst.exists():
        shutil.rmtree(dst)
    shutil.copytree(src, dst)
    print(f"{m}: copiado de {src}")
    return dst


def num(s):
    # Las cantidades vienen como enteros con separador de miles "." o "," según el modelo.
    return pd.to_numeric(s.astype(str).str.replace(r"[.,]", "", regex=True), errors="coerce").fillna(0)


def leer(p):
    d = pd.read_csv(p, encoding="utf-8-sig", dtype=str)
    return d[d.iloc[:, 0].notna()]  # la última fila de algunos archivos es un total sin semana


def codigos(serie, lista):
    pos = {v: i for i, v in enumerate(lista)}
    return serie.map(pos).astype(int)


def modelo(m, info_var):
    c = copiar(m)
    esq = leer(c / "esquejes_futuros.csv")
    proy = pd.concat([pd.read_csv(c / f"proy{i}.csv", dtype=str) for i in (1, 2, 3)])
    gua = leer(c / "guarde.csv")
    sal = leer(c / "salvamento.csv")
    escenario = esq.Escenario.dropna().iloc[0]

    for d, col in ((esq, "Flor"), (proy, "Flor")):
        for v, f, co in d[["Variedad", col, "Color"]].drop_duplicates("Variedad").itertuples(index=False):
            info_var.setdefault(v, [f, co])

    vars_ = sorted(set(esq.Variedad) | set(proy.Variedad) | set(gua.Variedad) | set(sal.Variedad.dropna()))
    fincas = sorted(set(esq.Finca) | set(proy.Finca) | set(gua.Finca))
    subs = sorted(sal.Submercado.dropna().unique())
    nsg = sorted(gua.iloc[:, 3].dropna().unique())

    e = pd.DataFrame({"s": esq.Semana.astype(int), "v": codigos(esq.Variedad, vars_), "f": codigos(esq.Finca, fincas),
                      "c": esq.Ciclo.astype(int), "q": num(esq.Cantidad)})
    e = e.groupby(["s", "v", "f", "c"]).q.sum().reset_index()

    p = pd.DataFrame({"s": proy.SemanaProducion.astype(int), "v": codigos(proy.Variedad, vars_),
                      "f": codigos(proy.Finca, fincas), "n": (proy.Origen != "Planeado").astype(int),
                      "q": pd.to_numeric(proy.Cantidad).fillna(0)})
    p = p.groupby(["s", "v", "f", "n"]).q.sum().reset_index()

    g = pd.DataFrame({"s": gua.Semana.astype(int), "v": codigos(gua.Variedad, vars_), "f": codigos(gua.Finca, fincas),
                      "k": codigos(gua.iloc[:, 3], nsg), "q": num(gua.Cantidad), "x": num(gua.iloc[:, 6])})
    g = g.groupby(["s", "v", "f", "k"])[["q", "x"]].sum().reset_index()

    sal = sal[sal.Variedad.notna()]
    sv = pd.DataFrame({"s": sal.Semana.astype(int), "v": codigos(sal.Variedad, vars_),
                       "k": codigos(sal.Submercado.fillna(subs[0]), subs), "q": num(sal.Cantidad)})
    sv = sv.groupby(["s", "v", "k"]).q.sum().reset_index()

    filas = lambda d: d.astype("int64").values.tolist()
    print(f"{m}: {escenario}\n    esquejes {e.q.sum():,.0f} | producción {p.q.sum():,.0f} | "
          f"guarde {g.q.sum():,.0f} | salvamento {sv.q.sum():,.0f}")
    return {"id": m, "escenario": escenario, "vars": vars_, "fincas": fincas, "subs": subs, "nsg": nsg,
            "esq": filas(e), "prod": filas(p), "guarde": filas(g), "salv": filas(sv)}


info_var = {}
modelos = [modelo(m, info_var) for m in args]
datos = {"generado": pd.Timestamp.now().strftime("%Y-%m-%d %H:%M"), "modelos": modelos,
         "infoVar": {k: v for k, v in sorted(info_var.items())}}
html = PLANTILLA.read_text(encoding="utf-8")
assert MARCA in html
out = AQUI / f"comparacion_{'_'.join(args)}.html"
out.write_text(html.replace(MARCA, json.dumps(datos, ensure_ascii=False, separators=(",", ":"))), encoding="utf-8")
print(f"-> {out.name} ({out.stat().st_size / 1e6:.1f} MB)")
