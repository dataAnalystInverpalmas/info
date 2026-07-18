# 📋 MVP de Reordenamiento de Tareas - Guía de Uso

## ✅ ¿Qué se implementó?

### 1. Drag-and-Drop Visual (Fase 1)
- Las tareas ahora se pueden **arrastrar** para reordenarlas dentro de la tabla
- Primer columna tiene un **handle visual** (⋮⋮) para agarrar y arrastrar
- Feedback visual: fila se desvanece al arrastrar (ghostClass)
- Compatible con todos los navegadores modernos (usa SortableJS)

### 2. API de Reordenamiento (Fase 2)
- Nuevo endpoint: `POST /ajax/tareas.php?accion=reordenar`
- Acepta JSON array con cambios de orden
- Usa transacción MySQL (garantiza atomicidad: todo o nada)
- Respuesta: `{"success": true, "mensaje": "..."}`

### 3. Indicadores Visuales (Fase 4)
- **Tipo**: Badge azul (Prevista) o naranja (Imprevista)
- **Estado**: Verde (Completada), Azul (En Progreso), Rojo (Cancelada), Gris (Pendiente)
- **Prioridad**: Rojo (Urgente), Naranja (Alta), Azul (Media), Gris claro (Baja)
- **Orden**: Número en badge gris

---

## 🎯 Cómo usar

### Reordenar tareas (Interfaz)

1. Ve a **Gestión de Tareas** en tu navegador
2. Localiza el icono **⋮⋮** en la primera columna de cualquier tarea
3. **Arrastra** la tarea hacia arriba o hacia abajo
4. **Suelta** en la posición deseada
5. El sistema automáticamente:
   - Recalcula el `orden_ejecucion` de todas las tareas
   - Envía los cambios al servidor (AJAX)
   - Actualiza la base de datos en una transacción atómica

### Reordenar tareas (API REST)

```bash
curl -X POST http://localhost/info/ajax/tareas.php?accion=reordenar \
  -H "Content-Type: application/json" \
  -d '{
    "tareas": [
      {"id": 5, "proyecto_id": 0, "orden_ejecucion": 1},
      {"id": 3, "proyecto_id": 0, "orden_ejecucion": 2},
      {"id": 1, "proyecto_id": 0, "orden_ejecucion": 3}
    ]
  }'
```

**Respuesta exitosa:**
```json
{
  "success": true,
  "mensaje": "Tareas reordenadas exitosamente"
}
```

---

## 🔍 Verificación rápida

### Paso 1: Verificar que SortableJS está cargado
Abre la consola del navegador (F12) y ejecuta:
```javascript
console.log(typeof Sortable); // Debe mostrar "function"
```

### Paso 2: Verificar que el endpoint responde
```bash
curl -X POST http://localhost/info/ajax/tareas.php?accion=reordenar \
  -H "Content-Type: application/json" \
  -d '{"tareas":[{"id":1,"proyecto_id":0,"orden_ejecucion":1}]}'
```

Debe retornar JSON con `"success": true` o `false` (con mensaje de error).

### Paso 3: Test interactivo
- Abre las Tareas en navegador
- Intenta arrastrar una tarea
- Abre DevTools (F12) → Consola
- Verifica que aparezcan logs: `"Tareas reordenadas exitosamente"`

---

## 📊 Cambios en la Base de Datos

**No se modificó la estructura de BD.** El sistema usa el campo `orden_ejecucion` existente:

```sql
-- Ejemplo de cómo quedaría después de reordenar:
SELECT id, nombre, orden_ejecucion FROM tareas WHERE proyecto_id = 1;

-- Antes:
| id | nombre       | orden_ejecucion |
|----|--------------|-----------------|
| 1  | Tarea A      | 1               |
| 2  | Tarea B      | 2               |
| 3  | Tarea C      | 3               |

-- Después de reordenar C→1, A→2, B→3:
| id | nombre       | orden_ejecucion |
|----|--------------|-----------------|
| 3  | Tarea C      | 1               |
| 1  | Tarea A      | 2               |
| 2  | Tarea B      | 3               |
```

---

## 🛠️ Arquivos Modificados

1. **[src/Views/Tareas/index.php](src/Views/Tareas/index.php)**
   - Agregado handle visual en columna 1
   - Data attributes en filas (data-id, data-proyecto)
   - CDN de SortableJS + script de inicialización
   - Estilos para drag-drop visual

2. **[src/Controllers/TareaController.php](src/Controllers/TareaController.php)**
   - Método `reordenar()` (validación, transacción, update batch)
   - Enrutamiento en `handleRequest()` para acción 'reordenar'

3. **[scripts/tareas.js](scripts/tareas.js)**
   - Ajuste de índices de columna para filtro (agregamos columna handle)
   - Lógica AJAX de reordenamiento

---

## ⚠️ Limitaciones (MVP)

- ✅ Reordenamiento dentro de **un proyecto específico** (respeta proyecto_id)
- ⚠️ **Sin validación de máquina de estados** (puedes cambiar completada → pendiente)
- ⚠️ **Sin subtareas** (todas las tareas están al mismo nivel)
- ⚠️ **Sin notificaciones** de cambios en tiempo real
- ✅ **Sin cambios en BD** (aprovecha `orden_ejecucion` existente)

---

## 🚀 Próximas Mejoras (Fase 3+)

1. **Subtareas**: Agregar `tarea_padre_id` para jerarquía
2. **Máquina de estados**: Validar transiciones permitidas
3. **Dashboard**: Vista agregada de proyectos, matriz de riesgos
4. **Notificaciones**: Alertas de tareas vencidas, proyectos en riesgo

---

## 📧 Soporte

Si encuentras algún error:

1. Verifica que no hay errores de sintaxis (VS Code errores)
2. Abre la consola del navegador (F12) y copia los errores
3. Verifica los logs del servidor en `error.log`

---

**Última actualización**: 2026-06-22  
**Version MVP**: 1.0 (Fase 1, 2, 4 parcial)
