CREATE TABLE IF NOT EXISTS informes.plano_reemplazos (
    id INT AUTO_INCREMENT PRIMARY KEY,

    finca VARCHAR(50) NOT NULL,
    bloque VARCHAR(50) NOT NULL,
    tabla VARCHAR(50) NOT NULL,
    nave VARCHAR(50) NOT NULL,
    cama VARCHAR(50) NOT NULL,

    variedad_original VARCHAR(100) NULL,
    temporada_original VARCHAR(100) NULL,

    variedad_nueva_id INT NOT NULL,
    temporada_nueva_id INT NOT NULL,

    activo TINYINT(1) NOT NULL DEFAULT 1,
    activo_unique TINYINT GENERATED ALWAYS AS (CASE WHEN activo = 1 THEN 1 ELSE NULL END) STORED,

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by VARCHAR(100) NULL,

    INDEX idx_plano_reemplazos_loc (finca, bloque, tabla, nave, cama),
    INDEX idx_plano_reemplazos_activo (activo),
    UNIQUE KEY uk_plano_reemplazo_activo (finca, bloque, tabla, nave, cama, activo_unique),

    CONSTRAINT fk_plano_reemplazos_variedad
        FOREIGN KEY (variedad_nueva_id)
        REFERENCES informes.varieties(id),

    CONSTRAINT fk_plano_reemplazos_temporada
        FOREIGN KEY (temporada_nueva_id)
        REFERENCES informes.seasons(id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
