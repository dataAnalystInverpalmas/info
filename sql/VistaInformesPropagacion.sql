CREATE OR REPLACE VIEW viewsowing AS
SELECT a.fecha,v.producto,.variedad,a.temporada,a.tipo,b.fecha_pico,sum(a.valor) as valor FROM labors_sowing as a 
LEFT JOIN seasons as b on b.nombre=a.temporada
LEFT JOIN varieties AS v ON b.nombre=a.variedad
WHERE a.valor>0
GROUP by a.fecha,a.variedad,a.temporada,a.tipo,b.fecha_pico
UNION
SELECT a.fecha_siembra,v.producto,a.variedad,a.temporada_obj,'TEO_SIEMBRA' AS tipo,b.fecha_pico,sum(a.plantas) FROM program as a 
LEFT JOIN seasons as b on b.nombre=a.temporada_obj
LEFT JOIN varieties AS v ON b.nombre=a.variedad
WHERE a.plantas>0 
GROUP by a.fecha_siembra,a.variedad,a.temporada_obj,a.tipo,b.fecha_pico
UNION
SELECT a.fecha_ensarte,v.producto,a.variedad,a.temporada_obj,'TEO_ENSARTE' AS tipo,b.fecha_pico,sum(a.plantas) FROM program as a
LEFT JOIN seasons as b on b.nombre=a.temporada_obj
LEFT JOIN varieties AS v ON b.nombre=a.variedad
WHERE a.plantas>0 
GROUP by a.fecha_ensarte,a.variedad,a.temporada_obj,a.tipo,b.fecha_pico 
UNION
SELECT a.fecha_cosecha,v.producto,a.variedad,a.temporada_obj,'TEO_COSECHA' AS tipo,b.fecha_pico,sum(a.plantas) FROM program as a 
LEFT JOIN seasons as b on b.nombre=a.temporada_obj
LEFT JOIN varieties AS v ON b.nombre=a.variedad
WHERE a.plantas>0 
GROUP BY a.fecha_cosecha,a.variedad,a.temporada_obj,a.tipo,b.fecha_pico
UNION
SELECT a.fecha_siembra,v.producto,a.variedad,a.temporada,'SIEMBRA' as tipo,b.fecha_pico,sum(a.plantas) FROM hplane as a 
LEFT JOIN seasons as b on b.nombre=a.temporada
LEFT JOIN varieties AS v ON b.nombre=a.variedad
WHERE a.plantas>0 
GROUP BY a.fecha_siembra,a.variedad,a.temporada,b.fecha_pico