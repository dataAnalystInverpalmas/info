-- ============================================
-- Módulo de Proyectos, Tareas y Bitácora
-- ============================================

-- Tabla de Proyectos
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    categoria VARCHAR(100) DEFAULT NULL COMMENT 'Agrupación: Tika, Estadística, etc.',
    descripcion TEXT,
    estado ENUM('activo', 'pausado', 'completado', 'cancelado') DEFAULT 'activo',
    fecha_inicio DATE,
    fecha_fin DATE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY nombre_unico (nombre)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Tareas
CREATE TABLE IF NOT EXISTS tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proyecto_id INT DEFAULT NULL COMMENT 'NULL = tarea sin proyecto',
    nombre VARCHAR(255) NOT NULL,
    tipo ENUM('prevista', 'imprevista') DEFAULT 'prevista',
    descripcion TEXT,
    estado ENUM('pendiente', 'en_progreso', 'completada', 'cancelada') DEFAULT 'pendiente',
    prioridad ENUM('baja', 'media', 'alta', 'urgente') DEFAULT 'media',
    responsable VARCHAR(150) DEFAULT NULL,
    fecha_inicio DATE DEFAULT NULL,
    fecha_vencimiento DATE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
    KEY idx_proyecto (proyecto_id),
    KEY idx_estado (estado),
    KEY idx_tipo (tipo)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Bitácora (registros de cambios/actividad)
CREATE TABLE IF NOT EXISTS bitacora (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tarea_id INT,
    proyecto_id INT DEFAULT NULL,
    tipo_registro ENUM('creacion', 'actualizacion', 'completada', 'nota', 'cambio_estado') DEFAULT 'nota',
    descripcion TEXT NOT NULL,
    autor VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tarea_id) REFERENCES tareas(id) ON DELETE CASCADE,
    KEY idx_tarea (tarea_id),
    KEY idx_proyecto (proyecto_id),
    KEY idx_fecha (fecha_registro)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices adicionales para optimización
CREATE INDEX idx_proyectos_estado ON proyectos(estado);
CREATE INDEX idx_proyectos_categoria ON proyectos(categoria);
CREATE INDEX idx_tareas_proyecto_estado ON tareas(proyecto_id, estado);
