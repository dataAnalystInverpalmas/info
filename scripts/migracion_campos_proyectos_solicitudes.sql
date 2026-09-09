-- ============================================
-- Completa Proyectos, Cronograma y Solicitudes Operativas
-- ============================================

DROP PROCEDURE IF EXISTS migrar_campos_proyectos_solicitudes;

DELIMITER $$
CREATE PROCEDURE migrar_campos_proyectos_solicitudes()
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'tipo') THEN
        ALTER TABLE proyectos ADD COLUMN tipo VARCHAR(50) NULL AFTER nombre;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'area_solicitante') THEN
        ALTER TABLE proyectos ADD COLUMN area_solicitante VARCHAR(150) NULL AFTER tipo;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'problema_negocio') THEN
        ALTER TABLE proyectos ADD COLUMN problema_negocio TEXT NULL AFTER area_solicitante;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'prioridad') THEN
        ALTER TABLE proyectos ADD COLUMN prioridad ENUM('baja', 'media', 'alta', 'urgente') NOT NULL DEFAULT 'media' AFTER responsable_proyecto;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'entregable_periodo') THEN
        ALTER TABLE proyectos ADD COLUMN entregable_periodo TEXT NULL AFTER fecha_fin;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'riesgo_principal') THEN
        ALTER TABLE proyectos ADD COLUMN riesgo_principal TEXT NULL AFTER entregable_periodo;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'proximo_hito') THEN
        ALTER TABLE proyectos ADD COLUMN proximo_hito TEXT NULL AFTER riesgo_principal;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'criterio_exito') THEN
        ALTER TABLE proyectos ADD COLUMN criterio_exito TEXT NULL AFTER proximo_hito;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'stakeholders') THEN
        ALTER TABLE proyectos ADD COLUMN stakeholders TEXT NULL AFTER criterio_exito;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'link_evidencias') THEN
        ALTER TABLE proyectos ADD COLUMN link_evidencias VARCHAR(500) NULL AFTER stakeholders;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proyectos' AND COLUMN_NAME = 'migrado_a_solicitud_en') THEN
        ALTER TABLE proyectos ADD COLUMN migrado_a_solicitud_en DATETIME NULL AFTER link_evidencias;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tareas' AND COLUMN_NAME = 'etapa_fase') THEN
        ALTER TABLE tareas ADD COLUMN etapa_fase VARCHAR(150) NULL AFTER proyecto_id;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tareas' AND COLUMN_NAME = 'fecha_fin_real') THEN
        ALTER TABLE tareas ADD COLUMN fecha_fin_real DATE NULL AFTER fecha_vencimiento;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tareas' AND COLUMN_NAME = 'entregable_concreto') THEN
        ALTER TABLE tareas ADD COLUMN entregable_concreto TEXT NULL AFTER fecha_fin_real;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tareas' AND COLUMN_NAME = 'evidencia_soporte') THEN
        ALTER TABLE tareas ADD COLUMN evidencia_soporte VARCHAR(500) NULL AFTER entregable_concreto;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tareas' AND COLUMN_NAME = 'observaciones') THEN
        ALTER TABLE tareas ADD COLUMN observaciones TEXT NULL AFTER evidencia_soporte;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tareas' AND COLUMN_NAME = 'dependencia') THEN
        ALTER TABLE tareas ADD COLUMN dependencia TEXT NULL AFTER observaciones;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tareas' AND COLUMN_NAME = 'solicitud_id') THEN
        ALTER TABLE tareas ADD COLUMN solicitud_id INT NULL AFTER proyecto_id;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tareas' AND INDEX_NAME = 'uq_tarea_solicitud') THEN
        ALTER TABLE tareas ADD UNIQUE KEY uq_tarea_solicitud (solicitud_id);
    END IF;
END$$
DELIMITER ;

CALL migrar_campos_proyectos_solicitudes();
DROP PROCEDURE migrar_campos_proyectos_solicitudes;

CREATE TABLE IF NOT EXISTS solicitudes_extra (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    tarea_origen_id INT NULL,
    proyecto_origen_id INT NULL,
    fecha DATE NULL,
    solicitante VARCHAR(150) NULL,
    area VARCHAR(150) NULL,
    descripcion TEXT NOT NULL,
    tipo VARCHAR(100) NULL,
    tiempo_invertido_horas DECIMAL(8,2) NULL,
    responsable VARCHAR(150) NULL,
    estado ENUM('pendiente', 'en_progreso', 'completada', 'cancelada') NOT NULL DEFAULT 'pendiente',
    se_convirtio_en_proyecto TINYINT(1) NOT NULL DEFAULT 0,
    proyecto_convertido_id INT NULL,
    observaciones TEXT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_solicitud_tarea_origen (tarea_origen_id),
    KEY idx_solicitudes_usuario_fecha (usuario_id, fecha),
    KEY idx_solicitudes_estado (estado),
    CONSTRAINT fk_solicitudes_proyecto_origen FOREIGN KEY (proyecto_origen_id) REFERENCES proyectos(id) ON DELETE SET NULL,
    CONSTRAINT fk_solicitudes_proyecto_convertido FOREIGN KEY (proyecto_convertido_id) REFERENCES proyectos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP PROCEDURE IF EXISTS migrar_relacion_solicitud_tarea;

DELIMITER $$
CREATE PROCEDURE migrar_relacion_solicitud_tarea()
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'solicitudes_extra' AND COLUMN_NAME = 'tarea_origen_id') THEN
        ALTER TABLE solicitudes_extra ADD COLUMN tarea_origen_id INT NULL AFTER usuario_id;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'solicitudes_extra' AND INDEX_NAME = 'uq_solicitud_tarea_origen') THEN
        ALTER TABLE solicitudes_extra ADD UNIQUE KEY uq_solicitud_tarea_origen (tarea_origen_id);
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'solicitudes_extra' AND INDEX_NAME = 'idx_solicitudes_proyecto_origen') THEN
        ALTER TABLE solicitudes_extra ADD KEY idx_solicitudes_proyecto_origen (proyecto_origen_id);
    END IF;
    IF EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'solicitudes_extra' AND INDEX_NAME = 'uq_solicitud_proyecto_origen') THEN
        ALTER TABLE solicitudes_extra DROP INDEX uq_solicitud_proyecto_origen;
    END IF;
END$$
DELIMITER ;

CALL migrar_relacion_solicitud_tarea();
DROP PROCEDURE migrar_relacion_solicitud_tarea;

INSERT INTO solicitudes_extra (
    usuario_id, tarea_origen_id, proyecto_origen_id, fecha, solicitante, area,
    descripcion, tipo, responsable, estado, observaciones
)
SELECT
    t.usuario_id,
    t.id,
    p.id,
    COALESCE(t.fecha_inicio, p.fecha_inicio),
    NULL,
    p.area_solicitante,
    t.nombre,
    'Soporte',
    t.responsable,
    CASE t.estado
        WHEN 'completada' THEN 'completada'
        WHEN 'en_progreso' THEN 'en_progreso'
        WHEN 'cancelada' THEN 'cancelada'
        ELSE 'pendiente'
    END,
    t.descripcion
FROM tareas t
INNER JOIN proyectos p ON p.id = t.proyecto_id
LEFT JOIN solicitudes_extra s ON s.tarea_origen_id = t.id
WHERE LOWER(TRIM(p.categoria)) = 'plan semanal'
  AND s.id IS NULL;

UPDATE proyectos p
INNER JOIN tareas t ON t.proyecto_id = p.id
INNER JOIN solicitudes_extra s ON s.tarea_origen_id = t.id
SET p.migrado_a_solicitud_en = COALESCE(p.migrado_a_solicitud_en, NOW())
WHERE LOWER(TRIM(p.categoria)) = 'plan semanal';
