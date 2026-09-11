-- ============================================================================
-- Migración: unificar program y programf via program_plan
-- Fase 1: solo esquema — no requiere ni un cambio en el código PHP.
-- Ver plan completo en el historial de conversación / documentación del proyecto.
--
-- Llave de identidad de un plan: variedad + ciclo + fecha_siembra + programa
-- (excluye casa_id/finca/bloque a propósito: varias casas/fincas comparten un plan).
--
-- Ejecutar en este orden. El paso 0 es de solo lectura: revisa el resultado
-- antes de continuar si te devuelve filas.
-- ============================================================================


-- ----------------------------------------------------------------------------
-- PASO 0 (diagnóstico, no modifica nada): ¿hay planes con la misma llave pero
-- valores distintos en columnas que deberían ser iguales para todas las casas?
-- ----------------------------------------------------------------------------
SELECT variedad, ciclo, fecha_siembra, programa,
       COUNT(DISTINCT temporada_obj) AS temporadas_distintas,
       COUNT(DISTINCT producto)      AS productos_distintos,
       COUNT(DISTINCT fecha_pico)    AS fechas_pico_distintas,
       COUNT(*)                      AS filas
FROM program
WHERE estado = 1
GROUP BY variedad, ciclo, fecha_siembra, programa
HAVING temporadas_distintas > 1 OR productos_distintos > 1 OR fechas_pico_distintas > 1;

-- Si esto devuelve filas: revísalas antes de seguir. El backfill del paso 2
-- usa MIN() como criterio de desempate (determinístico pero arbitrario) para
-- cualquier columna que difiera dentro de un mismo grupo.


-- ----------------------------------------------------------------------------
-- PASO 1: crear program_plan
-- ----------------------------------------------------------------------------
CREATE TABLE program_plan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    variedad VARCHAR(30) NOT NULL,
    ciclo INT DEFAULT NULL,
    fecha_siembra DATE DEFAULT NULL,
    programa INT DEFAULT NULL,
    producto VARCHAR(20) DEFAULT NULL,
    color VARCHAR(30) DEFAULT NULL,
    temporada_obj VARCHAR(30) DEFAULT NULL,
    tipo VARCHAR(20) DEFAULT NULL,
    fecha_temporada DATE DEFAULT NULL,
    fecha_ensarte DATE DEFAULT NULL,
    fecha_cosecha DATE DEFAULT NULL,
    fecha_pico DATE DEFAULT NULL,
    ferradica VARCHAR(10) DEFAULT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 1,
    creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_plan (variedad, ciclo, fecha_siembra, programa)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- ----------------------------------------------------------------------------
-- PASO 2: poblar program_plan desde program (estado=1), deduplicado por la
-- llave del plan. MIN() resuelve el desempate si el paso 0 mostró diferencias.
-- ----------------------------------------------------------------------------
INSERT INTO program_plan
    (variedad, ciclo, fecha_siembra, programa,
     producto, color, temporada_obj, tipo,
     fecha_temporada, fecha_ensarte, fecha_cosecha, fecha_pico,
     ferradica, estado)
SELECT
    variedad, ciclo, fecha_siembra, programa,
    MIN(producto), MIN(color), MIN(temporada_obj), MIN(tipo),
    MIN(fecha_temporada), MIN(fecha_ensarte), MIN(fecha_cosecha), MIN(fecha_pico),
    MIN(ferradica), 1
FROM program
WHERE estado = 1
GROUP BY variedad, ciclo, fecha_siembra, programa;


-- ----------------------------------------------------------------------------
-- PASO 3: agregar la columna FK (nullable) a program y programf.
-- Nullable + INSERTs existentes con lista de columnas explícita = no rompe nada.
-- ----------------------------------------------------------------------------
ALTER TABLE program  ADD COLUMN program_plan_id INT NULL AFTER id;
ALTER TABLE programf ADD COLUMN program_plan_id INT NULL AFTER id;

ALTER TABLE program  ADD CONSTRAINT fk_program_plan  FOREIGN KEY (program_plan_id) REFERENCES program_plan(id);
ALTER TABLE programf ADD CONSTRAINT fk_programf_plan FOREIGN KEY (program_plan_id) REFERENCES program_plan(id);


-- ----------------------------------------------------------------------------
-- PASO 4: backfill de program_plan_id en program y programf, uniendo por la
-- misma llave.
-- ----------------------------------------------------------------------------
UPDATE program p
JOIN program_plan pp
  ON pp.variedad = p.variedad AND pp.ciclo = p.ciclo
 AND pp.fecha_siembra = p.fecha_siembra AND pp.programa = p.programa
SET p.program_plan_id = pp.id
WHERE p.estado = 1;

UPDATE programf pf
JOIN program_plan pp
  ON pp.variedad = pf.variedad AND pp.ciclo = pf.ciclo
 AND pp.fecha_siembra = pf.fecha_siembra AND pp.programa = pf.programa
SET pf.program_plan_id = pp.id;


-- ----------------------------------------------------------------------------
-- PASO 5: tabla de log de auditoría (creada, lista para usarse; no se conecta
-- a ningún código todavía).
-- ----------------------------------------------------------------------------
CREATE TABLE program_plan_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_plan_id INT NOT NULL,
    campo VARCHAR(50) NOT NULL,
    valor_anterior TEXT,
    valor_nuevo TEXT,
    usuario VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (program_plan_id) REFERENCES program_plan(id) ON DELETE CASCADE,
    KEY idx_plan (program_plan_id),
    KEY idx_fecha (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ----------------------------------------------------------------------------
-- PASO 6 (verificación, solo lectura)
-- ----------------------------------------------------------------------------

-- Debería dar 0: toda fila activa de program debe haber quedado enlazada.
SELECT COUNT(*) AS program_sin_plan
FROM program
WHERE estado = 1 AND program_plan_id IS NULL;

-- Debería coincidir con el número de grupos del paso 2 (una fila de program_plan
-- por cada combinación única de variedad+ciclo+fecha_siembra+programa).
SELECT COUNT(*) AS total_planes FROM program_plan;

SELECT
    COUNT(*) AS programf_total,
    SUM(program_plan_id IS NOT NULL) AS programf_enlazadas,
    SUM(program_plan_id IS NULL) AS programf_sin_match
FROM programf;
