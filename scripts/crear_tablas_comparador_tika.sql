-- =====================================================================
-- MIGRACIÓN: Comparador de escenarios Tika
-- Tablas agregadas por modelo de planeación de siembras de flores
-- =====================================================================

CREATE TABLE IF NOT EXISTS `tika_modelo` (
    `id` INT NOT NULL,
    `escenario` VARCHAR(255) NOT NULL,
    `ruta` VARCHAR(500) DEFAULT NULL,
    `cargado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `cargado_por` VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tika_variedad` (
    `nombre` VARCHAR(120) NOT NULL,
    `flor` VARCHAR(60) NOT NULL DEFAULT 'SIN FLOR',
    `color` VARCHAR(80) NOT NULL DEFAULT '',
    PRIMARY KEY (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tika_esquejes` (
    `modelo_id` INT NOT NULL,
    `semana` INT NOT NULL,
    `variedad` VARCHAR(120) NOT NULL,
    `finca` VARCHAR(80) NOT NULL,
    `ciclo` INT NOT NULL,
    `cantidad` BIGINT NOT NULL DEFAULT 0,
    PRIMARY KEY (`modelo_id`, `semana`, `variedad`, `finca`, `ciclo`),
    KEY `idx_tika_esq_mod_sem` (`modelo_id`, `semana`),
    CONSTRAINT `fk_tika_esq_modelo` FOREIGN KEY (`modelo_id`) REFERENCES `tika_modelo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tika_produccion` (
    `modelo_id` INT NOT NULL,
    `semana` INT NOT NULL,
    `variedad` VARCHAR(120) NOT NULL,
    `finca` VARCHAR(80) NOT NULL,
    `nueva` TINYINT(1) NOT NULL DEFAULT 0,
    `cantidad` BIGINT NOT NULL DEFAULT 0,
    PRIMARY KEY (`modelo_id`, `semana`, `variedad`, `finca`, `nueva`),
    KEY `idx_tika_prod_mod_sem` (`modelo_id`, `semana`),
    CONSTRAINT `fk_tika_prod_modelo` FOREIGN KEY (`modelo_id`) REFERENCES `tika_modelo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tika_guarde` (
    `modelo_id` INT NOT NULL,
    `semana` INT NOT NULL,
    `variedad` VARCHAR(120) NOT NULL,
    `finca` VARCHAR(80) NOT NULL,
    `nsg` VARCHAR(40) NOT NULL,
    `cantidad` BIGINT NOT NULL DEFAULT 0,
    `perdidas` BIGINT NOT NULL DEFAULT 0,
    PRIMARY KEY (`modelo_id`, `semana`, `variedad`, `finca`, `nsg`),
    KEY `idx_tika_gua_mod_sem` (`modelo_id`, `semana`),
    CONSTRAINT `fk_tika_gua_modelo` FOREIGN KEY (`modelo_id`) REFERENCES `tika_modelo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `tika_salvamento` (
    `modelo_id` INT NOT NULL,
    `semana` INT NOT NULL,
    `variedad` VARCHAR(120) NOT NULL,
    `submercado` VARCHAR(80) NOT NULL,
    `cantidad` BIGINT NOT NULL DEFAULT 0,
    PRIMARY KEY (`modelo_id`, `semana`, `variedad`, `submercado`),
    KEY `idx_tika_sal_mod_sem` (`modelo_id`, `semana`),
    CONSTRAINT `fk_tika_sal_modelo` FOREIGN KEY (`modelo_id`) REFERENCES `tika_modelo` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

