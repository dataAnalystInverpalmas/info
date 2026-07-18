-- ============================================
-- Migracion: Resumen Ejecutivo de Proyectos
-- Fecha: 2026-05-14
-- ============================================

ALTER TABLE proyectos
    ADD COLUMN IF NOT EXISTS objetivo_alcance TEXT NULL AFTER descripcion,
    ADD COLUMN IF NOT EXISTS responsable_proyecto VARCHAR(150) NULL AFTER objetivo_alcance;

CREATE TABLE IF NOT EXISTS proyecto_logros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proyecto_id INT NOT NULL,
    descripcion TEXT NOT NULL,
    impacto VARCHAR(255) NULL,
    fecha_logro DATE NULL,
    estado ENUM('registrado', 'validado') NOT NULL DEFAULT 'registrado',
    autor VARCHAR(100) NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    KEY idx_logros_proyecto_fecha (proyecto_id, fecha_logro),
    KEY idx_logros_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS proyecto_riesgos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proyecto_id INT NOT NULL,
    descripcion TEXT NOT NULL,
    probabilidad ENUM('baja', 'media', 'alta', 'muy_alta') NOT NULL DEFAULT 'media',
    impacto ENUM('bajo', 'medio', 'alto', 'critico') NOT NULL DEFAULT 'medio',
    estado ENUM('abierto', 'en_seguimiento', 'mitigado', 'cerrado') NOT NULL DEFAULT 'abierto',
    responsable VARCHAR(150) NULL,
    plan_mitigacion TEXT NULL,
    fecha_compromiso DATE NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    KEY idx_riesgos_proyecto_estado (proyecto_id, estado),
    KEY idx_riesgos_compromiso (fecha_compromiso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE tareas
    ADD INDEX idx_tareas_actualizacion (fecha_actualizacion);

ALTER TABLE bitacora
    ADD INDEX idx_bitacora_proyecto_fecha (proyecto_id, fecha_registro);
