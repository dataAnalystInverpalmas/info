# 🚀 Módulo de Proyectos, Tareas y Bitácora

## ✅ Checklist de Implementación

Módulo completo de gestión de proyectos con tareas y auditoría (bitácora).

### Archivos Creados

#### 📊 Base de Datos
- `scripts/crear_tablas_proyectos.sql` - Esquema MySQL con 3 tablas

#### 🏗️ Modelos (src/Models/)
- `Proyecto.php` - Operaciones CRUD proyectos
- `Tarea.php` - Operaciones CRUD tareas
- `Bitacora.php` - Registros de auditoría

#### 🎮 Controladores (src/Controllers/)
- `ProyectoController.php` - Lógica de proyectos
- `TareaController.php` - Lógica de tareas
- `BitacoraController.php` - Lógica de bitácora

#### 🔌 Endpoints AJAX (ajax/)
- `proyectos.php` - API REST proyectos
- `tareas.php` - API REST tareas
- `bitacora.php` - API REST bitácora

#### 👁️ Vistas (views/)
- `Proyectos/index.php` - Tabla de proyectos con DataTables
- `Tareas/index.php` - Tabla de tareas con tabs

#### 📚 Documentación
- `docs/API_PROYECTOS_TAREAS_BITACORA.md` - Documentación completa API
- `README.md` - Este archivo

---

## 🔧 Instalación

### 1️⃣ Crear las tablas en MySQL

```bash
# Opción A: Desde terminal
mysql -u root -p informes < scripts/crear_tablas_proyectos.sql

# Opción B: Desde MySQL Workbench
# Copiar y ejecutar el contenido de scripts/crear_tablas_proyectos.sql
```

### 2️⃣ Verificar la instalación

```bash
mysql -u root -p informes

# Ejecutar:
SHOW TABLES;
DESCRIBE proyectos;
DESCRIBE tareas;
DESCRIBE bitacora;
```

### 3️⃣ Acceder a las vistas

```
http://tu-servidor/views/Proyectos/index.php
http://tu-servidor/views/Tareas/index.php
```

---

## 📋 Estructura de Datos

### proyectos
```
id (PK)
nombre (UNIQUE)
descripcion
estado (activo|pausado|completado|cancelado)
fecha_inicio
fecha_fin
fecha_creacion
fecha_actualizacion
```

### tareas
```
id (PK)
proyecto_id (FK, NULLABLE)  ← Tareas imprevistas
nombre
descripcion
estado (pendiente|en_progreso|completada|cancelada)
prioridad (baja|media|alta|urgente)
fecha_vencimiento
fecha_creacion
fecha_actualizacion
```

### bitacora
```
id (PK)
tarea_id (FK, NULLABLE)  ← Registros generales
tipo_registro (creacion|actualizacion|completada|nota|cambio_estado)
descripcion
autor
fecha_registro
```

---

## 🌐 Endpoints Principales

### Proyectos
```
GET    /ajax/proyectos.php                  Listar todos
GET    /ajax/proyectos.php?id=1             Obtener uno
GET    /ajax/proyectos.php?id=1&accion=estadisticas
POST   /ajax/proyectos.php                  Crear
PUT    /ajax/proyectos.php?id=1             Actualizar
DELETE /ajax/proyectos.php?id=1             Eliminar
```

### Tareas
```
GET    /ajax/tareas.php                     Listar todas
GET    /ajax/tareas.php?accion=pendientes   Pendientes
GET    /ajax/tareas.php?accion=imprevistas  Sin proyecto
GET    /ajax/tareas.php?accion=proximas     Próximas 7 días
GET    /ajax/tareas.php?accion=por_proyecto&proyecto_id=1
POST   /ajax/tareas.php                     Crear
PUT    /ajax/tareas.php?id=1                Actualizar
PUT    /ajax/tareas.php?id=1&accion=cambiar_estado
DELETE /ajax/tareas.php?id=1                Eliminar
```

### Bitácora
```
GET    /ajax/bitacora.php                   Listar todos
GET    /ajax/bitacora.php?tarea_id=1        Por tarea
GET    /ajax/bitacora.php?accion=historial&tarea_id=1
GET    /ajax/bitacora.php?accion=reporte&fecha_inicio=2026-01-01&fecha_fin=2026-01-31
POST   /ajax/bitacora.php                   Crear registro
PUT    /ajax/bitacora.php?id=1              Actualizar
DELETE /ajax/bitacora.php?id=1              Eliminar
```

---

## 💻 Ejemplos de Uso (JavaScript)

### Crear proyecto
```javascript
fetch('ajax/proyectos.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    nombre: 'Mi Proyecto',
    descripcion: 'Descripción',
    estado: 'activo',
    fecha_inicio: '2026-02-01',
    fecha_fin: '2026-06-30'
  })
})
.then(r => r.json())
.then(data => console.log(data));
```

### Crear tarea (con bitácora automática)
```javascript
fetch('ajax/tareas.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    nombre: 'Tarea importante',
    proyecto_id: 1,
    prioridad: 'alta',
    fecha_vencimiento: '2026-02-28',
    autor: 'Juan'
  })
})
.then(r => r.json())
.then(data => console.log(data));
// Nota: Se crea automáticamente un registro en bitácora
```

### Cambiar estado de tarea
```javascript
fetch('ajax/tareas.php?id=1&accion=cambiar_estado', {
  method: 'PUT',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    estado: 'completada',
    autor: 'Juan'
  })
})
.then(r => r.json())
.then(data => console.log(data));
// Nota: Se registra automáticamente en bitácora
```

### Obtener historial de tarea
```javascript
fetch('ajax/bitacora.php?accion=historial&tarea_id=1')
  .then(r => r.json())
  .then(historial => console.log(historial));
```

### Obtener tareas imprevistas
```javascript
fetch('ajax/tareas.php?accion=imprevistas')
  .then(r => r.json())
  .then(tareas => console.log(tareas));
```

---

## 🔒 Seguridad

✅ **Implementado:**
- PDO con prepared statements
- Validación básica de datos
- UTF-8 en todas las conexiones
- DELETE CASCADE para integridad referencial
- Inputs sanitizados

⚠️ **Considerar:**
- Agregar autenticación (si no existe)
- Validación server-side más robusta
- Rate limiting en endpoints
- Logging de cambios críticos

---

## 📈 Performance

**Índices creados:**
- `proyectos.estado`
- `tareas.proyecto_id`
- `tareas.estado`
- `tareas.proyecto_id + estado`
- `bitacora.tarea_id`
- `bitacora.fecha_registro`

**Queries optimizadas:**
- JOINs en estadísticas
- GROUP BY para reportes
- LIMIT en consultas grandes
- Índices en claves foráneas

---

## 🧪 Testing

### Test con cURL

```bash
# Crear proyecto
curl -X POST http://localhost:9258/ajax/proyectos.php \
  -H "Content-Type: application/json" \
  -d '{
    "nombre": "Test Project",
    "descripcion": "Descripción de prueba",
    "estado": "activo"
  }'

# Listar proyectos
curl http://localhost:9258/ajax/proyectos.php

# Listar tareas imprevistas
curl http://localhost:9258/ajax/tareas.php?accion=imprevistas

# Obtener historial de tarea
curl http://localhost:9258/ajax/bitacora.php?accion=historial&tarea_id=1
```

---

## 📱 Integración con DataTables

Las vistas incluyen ejemplos listos para usar:

```javascript
$('#tablaTareas').DataTable({
  ajax: {
    url: 'ajax/tareas.php',
    dataSrc: ''  // El endpoint retorna array directamente
  },
  columns: [
    { data: 'id' },
    { data: 'nombre' },
    { data: 'estado' },
    // ...
  ]
});
```

---

## 🔄 Flujo de una tarea completa

```
1. Crear proyecto
   ↓
2. Crear tareas en el proyecto (o imprevistas)
   ↓
3. Cambiar estados (pendiente → en_progreso → completada)
   ↓
4. Ver historial completo en bitácora
   ↓
5. Generar reportes por fecha
```

**Todo automáticamente registrado en bitácora** ✅

---

## 🛠️ Customización

### Agregar nuevo estado de tarea
```php
// En Tarea.php, cambiar ENUM:
CREATE TABLE ... estados ... ENUM('pendiente','en_progreso','completada','cancelada','pausada')
```

### Agregar campos adicionales
```php
// 1. Migración SQL
ALTER TABLE tareas ADD COLUMN asignado_a VARCHAR(100);

// 2. Actualizar modelo
// Agregar en Tarea::crear() y Tarea::actualizar()
```

### Agregar permisos/roles
```php
// En Controllers, validar:
if (!tienePermiso('crear_proyectos')) {
    return ['error' => 'Permiso denegado'];
}
```

---

## 📞 Soporte y Mantenimiento

### Verificar conexión a BD
```php
// Desde terminal
php -r "require 'bd/conexion.php'; echo 'OK';"
```

### Resetear tablas
```bash
mysql -u root -p informes -e "DROP TABLE bitacora, tareas, proyectos;"
mysql -u root -p informes < scripts/crear_tablas_proyectos.sql
```

### Ver registros de bitácora
```sql
SELECT * FROM bitacora ORDER BY fecha_registro DESC LIMIT 20;
SELECT COUNT(*) as total FROM bitacora WHERE DATE(fecha_registro) = CURDATE();
```

---

## 📚 Documentación Adicional

- [API_PROYECTOS_TAREAS_BITACORA.md](docs/API_PROYECTOS_TAREAS_BITACORA.md) - Documentación de endpoints
- [Modelos PHP](src/Models/) - Código fuente detallado
- [Vistas HTML](views/) - Interfaces de usuario

---

## ✨ Características

✅ CRUD completo (Create, Read, Update, Delete)
✅ Relaciones: Proyecto → Tareas → Bitácora
✅ Tareas imprevistas (sin proyecto)
✅ Auditoría automática en bitácora
✅ Registros de cambios de estado
✅ Filtros y búsquedas
✅ Reportes por fecha
✅ DataTables integrado
✅ Validación de datos
✅ JSON API REST
✅ PDO seguro
✅ UTF-8 completo

---

## 📄 Licencia

Uso interno - Proyecto informes

---

**Última actualización:** Abril 2026
