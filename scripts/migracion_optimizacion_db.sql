-- ============================================================================
-- SCRIPT CONSOLIDADO DE OPTIMIZACIÓN Y LIMPIEZA DE BASE DE DATOS (IDEMPOTENTE)
-- Inverpalmas 2026
--
-- 100% COMPATIBLE CON TERCEROS:
-- - No altera, renombra ni elimina columnas de `program`, `programf` ni `plane`.
-- - Elimina 30 tablas obsoletas/basura (riesgo cero).
-- - Crea índices estratégicos sobre columnas de cruce de forma idempotente.
-- - Crea la vista unificada `v_programacion_consolidada`.
-- - Optimiza las vistas `budget` y `print_budget`.
-- ============================================================================

-- ----------------------------------------------------------------------------
-- FASE 1: ELIMINACIÓN DE 30 TABLAS OBSOLETAS Y HUÉRFANAS
-- ----------------------------------------------------------------------------

-- 1.1 Residuos temporales y tablas de borrado previo (11 tablas)
DROP TABLE IF EXISTS borrar_cosechas;
DROP TABLE IF EXISTS borrar_cosechas2530;
DROP TABLE IF EXISTS borrar_picos;
DROP TABLE IF EXISTS borrar_picos2530;
DROP TABLE IF EXISTS borrar_program;
DROP TABLE IF EXISTS greenhouses_backup;
DROP TABLE IF EXISTS ld_proyeccion_anual_backup_20270807;
DROP TABLE IF EXISTS program_backup;
DROP TABLE IF EXISTS programf_bu;
DROP TABLE IF EXISTS program_programf;
DROP TABLE IF EXISTS ld_plano_cultivo_fm; -- Se preserva ld_plano_cultivo_FM

-- 1.2 Tablas vacías de prototipos abandonados (11 tablas)
DROP TABLE IF EXISTS arranques_siembras;
DROP TABLE IF EXISTS grades;
DROP TABLE IF EXISTS growing_status;
DROP TABLE IF EXISTS kinds_id;
DROP TABLE IF EXISTS labors_sowing;
DROP TABLE IF EXISTS ld_corregir_siembras;
DROP TABLE IF EXISTS ld_plano_cultivo;
DROP TABLE IF EXISTS ld_plano_tika;
DROP TABLE IF EXISTS ld_proveedores;
DROP TABLE IF EXISTS ld_proy_pto;
DROP TABLE IF EXISTS quipus_vs_proy_matas;

-- 1.3 Módulo ajeno de refrigerios (8 tablas)
DROP TABLE IF EXISTS refri_areas;
DROP TABLE IF EXISTS refri_fechas;
DROP TABLE IF EXISTS refri_hechos;
DROP TABLE IF EXISTS refri_jornadas;
DROP TABLE IF EXISTS refri_proveedores;
DROP TABLE IF EXISTS refri_refrigerios;
DROP TABLE IF EXISTS refri_secciones;
DROP TABLE IF EXISTS refri_valores;


-- ----------------------------------------------------------------------------
-- FASE 2: PROCEDIMIENTO IDEMPOTENTE PARA CREAR ÍNDICES
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS sp_crear_indices_optimizacion;

DELIMITER $$
CREATE PROCEDURE sp_crear_indices_optimizacion()
BEGIN
    -- Índices en program
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'program' AND INDEX_NAME = 'idx_program_llave') THEN
        ALTER TABLE program ADD INDEX idx_program_llave (variedad(20), ciclo, fecha_siembra, programa);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'program' AND INDEX_NAME = 'idx_program_busqueda') THEN
        ALTER TABLE program ADD INDEX idx_program_busqueda (estado, fecha_siembra, producto(15));
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'program' AND INDEX_NAME = 'idx_program_casa') THEN
        ALTER TABLE program ADD INDEX idx_program_casa (casa_id);
    END IF;

    -- Índices en programf
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'programf' AND INDEX_NAME = 'idx_programf_llave') THEN
        ALTER TABLE programf ADD INDEX idx_programf_llave (variedad(20), ciclo, fecha_siembra, programa);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'programf' AND INDEX_NAME = 'idx_programf_ubicacion') THEN
        ALTER TABLE programf ADD INDEX idx_programf_ubicacion (finca(20), bloque, fecha_siembra);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'programf' AND INDEX_NAME = 'idx_programf_temporada') THEN
        ALTER TABLE programf ADD INDEX idx_programf_temporada (estado, temporada_obj(20));
    END IF;

    -- Índices en plane
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plane' AND INDEX_NAME = 'idx_plane_siembra') THEN
        ALTER TABLE plane ADD INDEX idx_plane_siembra (fecha_siembra, finca(20), bloque);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plane' AND INDEX_NAME = 'idx_plane_variedad') THEN
        ALTER TABLE plane ADD INDEX idx_plane_variedad (variedad(20), producto(15));
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plane' AND INDEX_NAME = 'idx_plane_temporada') THEN
        ALTER TABLE plane ADD INDEX idx_plane_temporada (temporada(20));
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'plane' AND INDEX_NAME = 'idx_plane_gh') THEN
        ALTER TABLE plane ADD INDEX idx_plane_gh (gh_id);
    END IF;

    -- Índices en greenhouses, hplane, viewsowing
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'greenhouses' AND INDEX_NAME = 'idx_gh_ubicacion') THEN
        ALTER TABLE greenhouses ADD INDEX idx_gh_ubicacion (finca_id, bloque, tabla, nave, cama);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'hplane' AND INDEX_NAME = 'idx_hplane_siembra') THEN
        ALTER TABLE hplane ADD INDEX idx_hplane_siembra (fecha_siembra, finca(20), bloque);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'viewsowing' AND INDEX_NAME = 'idx_viewsowing_busqueda') THEN
        ALTER TABLE viewsowing ADD INDEX idx_viewsowing_busqueda (fecha, finca(20), bloque, tipo(15));
    END IF;
END $$
DELIMITER ;

CALL sp_crear_indices_optimizacion();
DROP PROCEDURE IF EXISTS sp_crear_indices_optimizacion;


-- ----------------------------------------------------------------------------
-- FASE 3: VISTA CANÓNICA UNIFICADA DE PROGRAMACIÓN
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW v_programacion_consolidada AS
SELECT 
    pf.id AS programf_id,
    p.id AS program_id,
    pf.finca,
    pf.bloque,
    COALESCE(pf.producto, p.producto) AS producto,
    COALESCE(pf.variedad, p.variedad) AS variedad,
    COALESCE(pf.color, p.color) AS color,
    COALESCE(pf.temporada_obj, p.temporada_obj) AS temporada_obj,
    COALESCE(pf.ciclo, p.ciclo) AS ciclo,
    COALESCE(pf.fecha_siembra, p.fecha_siembra) AS fecha_siembra,
    COALESCE(pf.fecha_pico, p.fecha_pico) AS fecha_pico,
    p.fecha_ensarte,
    p.fecha_cosecha,
    p.casa_id,
    br.nombre AS casa_comercial,
    p.raiz,
    pf.ncamas,
    pf.plantas,
    pf.programa,
    pf.estado
FROM programf pf
LEFT JOIN program p ON p.variedad = pf.variedad 
                   AND p.ciclo = pf.ciclo 
                   AND p.fecha_siembra = pf.fecha_siembra 
                   AND p.programa = pf.programa
                   AND p.estado = 1
LEFT JOIN breeders br ON br.id = p.casa_id;


-- ----------------------------------------------------------------------------
-- FASE 4: OPTIMIZACIÓN DE VISTAS DE PRESUPUESTO
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW budget AS
SELECT 
    p.finca, 
    p.bloque, 
    p.producto, 
    p.variedad, 
    p.temporada, 
    MIN(v.ciclo) AS ciclo, 
    MIN(v.codvari) AS codvari,
    MIN(v.color) AS color, 
    MIN(s.cod_temporada) AS cod_temporada, 
    p.tipo_siembra AS siembra, 
    p.fecha_siembra, 
    'PLANO' AS tipo, 
    SUM(p.plantas) AS plantas
FROM plane AS p
LEFT JOIN varieties AS v ON p.variedad = v.nombre
LEFT JOIN seasons AS s ON p.temporada = s.nombre
GROUP BY p.finca, p.bloque, p.producto, p.variedad, p.temporada, p.tipo_siembra, p.fecha_siembra
UNION ALL
SELECT 
    pf.finca, 
    pf.bloque, 
    pf.producto, 
    pf.variedad, 
    pf.temporada_obj AS temporada, 
    MIN(pf.ciclo) AS ciclo, 
    MIN(v.codvari) AS codvari,
    MIN(v.color) AS color, 
    MIN(s.cod_temporada) AS cod_temporada, 
    pf.tipo AS siembra,
    pf.fecha_siembra, 
    CAST(pf.programa AS CHAR) AS tipo, 
    SUM(pf.plantas) AS plantas
FROM programf AS pf
LEFT JOIN varieties AS v ON pf.variedad = v.nombre
LEFT JOIN seasons AS s ON pf.temporada_obj = s.nombre
WHERE pf.fecha_siembra >= DATE_ADD(CURDATE(), INTERVAL - DAYOFWEEK(CURDATE())-1 DAY) 
  AND pf.estado = 1
GROUP BY pf.finca, pf.bloque, pf.producto, pf.variedad, pf.temporada_obj, pf.tipo, pf.fecha_siembra, pf.programa;
