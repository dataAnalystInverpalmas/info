-- ============================================
-- Agrega columna codigo (opcional) a supplies
-- Se muestra en el Formato Solicitud Salida de Materiales
-- ============================================

DROP PROCEDURE IF EXISTS migrar_codigo_supplies;

DELIMITER $$
CREATE PROCEDURE migrar_codigo_supplies()
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'supplies' AND COLUMN_NAME = 'codigo') THEN
        ALTER TABLE supplies ADD COLUMN codigo VARCHAR(50) NULL DEFAULT NULL;
    END IF;
END$$
DELIMITER ;

CALL migrar_codigo_supplies();
DROP PROCEDURE migrar_codigo_supplies;
