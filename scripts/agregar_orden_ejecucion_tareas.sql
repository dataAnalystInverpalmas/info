ALTER TABLE tareas
    ADD COLUMN orden_ejecucion INT UNSIGNED DEFAULT NULL COMMENT 'Orden manual de ejecucion dentro del proyecto' AFTER proyecto_id,
    ADD KEY idx_proyecto_orden (proyecto_id, orden_ejecucion);
