<?php
// Tablas del seguimiento diario de floreros (uso móvil).
//  - fv_daily_causes: causa de fin de florero por tallo (un tallo termina una sola vez).
//  - fv_daily_final:  florero cuya evaluación ya se registró (queda cerrado).
function fvd_ensure_tables($conexion)
{
    $conexion->query("CREATE TABLE IF NOT EXISTS fv_daily_causes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fv_id INT NOT NULL,
        tallo_numero INT NOT NULL,
        causes_item_id INT NOT NULL,
        fecha_revision DATE NOT NULL,
        dias INT NOT NULL,
        user_id INT NULL,
        registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uq_fv_tallo (fv_id, tallo_numero),
        KEY idx_fv (fv_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

    $conexion->query("CREATE TABLE IF NOT EXISTS fv_daily_final (
        fv_id INT NOT NULL PRIMARY KEY,
        fecha_final DATE NOT NULL,
        user_id INT NULL,
        registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8");
}
