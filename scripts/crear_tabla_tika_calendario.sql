-- =====================================================================
-- TABLA: tika_calendario
-- Calendario por semana YYWW y año con asignación de modelo_tika_id
-- =====================================================================

CREATE TABLE IF NOT EXISTS `tika_calendario` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `semana` INT NOT NULL COMMENT 'Semana en formato YYWW, ej. 2401, 2653, 3052',
  `anio` INT NOT NULL COMMENT 'Año calendario de 4 dígitos, ej. 2024, 2030',
  `numero_semana` INT NOT NULL COMMENT 'Semana del año 1 a 52 o 53',
  `modelo_tika_id` INT DEFAULT NULL COMMENT 'ID del modelo Tika asignado a la semana',
  `fecha_inicio` DATE DEFAULT NULL COMMENT 'Lunes de la semana',
  `fecha_fin` DATE DEFAULT NULL COMMENT 'Domingo de la semana',
  `observaciones` VARCHAR(255) DEFAULT NULL COMMENT 'Notas u observaciones del modelo/semana',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_semana` (`semana`),
  KEY `idx_anio` (`anio`),
  KEY `idx_modelo_tika_id` (`modelo_tika_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Poblado inicial opcional desde la tabla dates (2401 a 2952)
INSERT IGNORE INTO `tika_calendario` (`semana`, `anio`, `numero_semana`, `fecha_inicio`, `fecha_fin`)
SELECT 
    d.aass AS semana,
    d.año AS anio,
    d.semana AS numero_semana,
    MIN(DATE(d.fecha)) AS fecha_inicio,
    MAX(DATE(d.fecha)) AS fecha_fin
FROM `dates` d
WHERE d.aass >= 2401 AND d.aass <= 2952
GROUP BY d.aass, d.año, d.semana
ORDER BY d.aass ASC;
