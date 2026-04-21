-- Script para crear tablas de dimensiones y hechos
-- Sistema de gestión de refrigerios y comidas
-- Ejecuta este script en phpMyAdmin


-- ========================
-- TABLAS DE DIMENSIONES (con prefijo refri_)
-- ========================

CREATE TABLE IF NOT EXISTS `refri_areas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL UNIQUE,
  `finca` VARCHAR(100) NOT NULL,
  `activo` BOOLEAN DEFAULT TRUE,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `refri_secciones` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `id_area` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `activo` BOOLEAN DEFAULT TRUE,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_area) REFERENCES refri_areas(id) ON DELETE CASCADE,
  INDEX idx_area (id_area),
  UNIQUE KEY unique_seccion_area (id_area, nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `refri_proveedores` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(150) NOT NULL,
  `nit` VARCHAR(20) NOT NULL UNIQUE,
  `descuento_administrativo` BOOLEAN DEFAULT FALSE,
  `activo` BOOLEAN DEFAULT TRUE,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_nombre (nombre),
  INDEX idx_nit (nit)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `refri_fechas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fecha` DATE NOT NULL UNIQUE,
  `año` INT NOT NULL,
  `mes` INT NOT NULL,
  `quincena` INT NOT NULL,
  `dia_semana` VARCHAR(15),
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_fecha (fecha),
  INDEX idx_año_mes (año, mes)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `refri_refrigerios` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `categoria` VARCHAR(100),
  `descripcion_categoria` VARCHAR(100),
  `descripcion` TEXT,
  `activo` BOOLEAN DEFAULT TRUE,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jornadas: solo almacena hora
CREATE TABLE IF NOT EXISTS `refri_jornadas` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `hora` TIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `refri_valores` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `refrigerio_id` INT NOT NULL,
  `proveedor_id` INT NOT NULL,
  `valor` DECIMAL(10, 2) NOT NULL,
  `activo` BOOLEAN DEFAULT TRUE,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (refrigerio_id) REFERENCES refri_refrigerios(id) ON DELETE CASCADE,
  FOREIGN KEY (proveedor_id) REFERENCES refri_proveedores(id) ON DELETE CASCADE,
  INDEX idx_refrigerio (refrigerio_id),
  INDEX idx_proveedor (proveedor_id),
  UNIQUE KEY unique_valor (refrigerio_id, proveedor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================
-- TABLA DE HECHOS (con prefijo refri_)
-- ========================

CREATE TABLE IF NOT EXISTS `refri_hechos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fecha_id` INT NOT NULL,
  `proveedor_id` INT NOT NULL,
  `seccion_id` INT NOT NULL,
  `refrigerio_id` INT NOT NULL,
  `jornada_id` INT NOT NULL,
  `cantidad` INT NOT NULL DEFAULT 1,
  `valor_unitario` DECIMAL(10, 2),
  `valor_total` DECIMAL(10, 2),
  `cuenta_cobro` VARCHAR(50),
  `observaciones` TEXT,
  `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (fecha_id) REFERENCES refri_fechas(id) ON DELETE CASCADE,
  FOREIGN KEY (proveedor_id) REFERENCES refri_proveedores(id) ON DELETE CASCADE,
  FOREIGN KEY (seccion_id) REFERENCES refri_secciones(id) ON DELETE CASCADE,
  FOREIGN KEY (refrigerio_id) REFERENCES refri_refrigerios(id) ON DELETE CASCADE,
  FOREIGN KEY (jornada_id) REFERENCES refri_jornadas(id) ON DELETE CASCADE,
  INDEX idx_fecha (fecha_id),
  INDEX idx_proveedor (proveedor_id),
  INDEX idx_seccion (seccion_id),
  INDEX idx_refrigerio (refrigerio_id),
  INDEX idx_jornada (jornada_id),
  INDEX idx_fecha_proveedor (fecha_id, proveedor_id),
  INDEX idx_fecha_creacion (fecha_creacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
