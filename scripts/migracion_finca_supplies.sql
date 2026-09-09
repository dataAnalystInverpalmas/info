-- ============================================
-- Agrega columna finca a la tabla supplies
-- Permite que los insumos difieran por finca
-- ============================================

DROP PROCEDURE IF EXISTS migrar_finca_supplies;

DELIMITER $$
CREATE PROCEDURE migrar_finca_supplies()
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplies' AND COLUMN_NAME = 'finca') THEN
        ALTER TABLE supplies ADD COLUMN finca VARCHAR(50) NOT NULL AFTER arrangement_id;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplies' AND INDEX_NAME = 'idx_supplies_finca') THEN
        ALTER TABLE supplies ADD INDEX idx_supplies_finca (finca);
    END IF;
END$$
DELIMITER ;

CALL migrar_finca_supplies();
DROP PROCEDURE migrar_finca_supplies;
