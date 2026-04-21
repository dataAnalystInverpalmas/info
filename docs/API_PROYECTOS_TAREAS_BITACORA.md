# 📋 API de Proyectos, Tareas y Bitácora

## Descripción General
API RESTful en PHP para gestionar proyectos, tareas y registros de bitácora (auditoría). Utiliza PDO para conexión segura a MySQL.

---

## 🗄️ Estructura de la Base de Datos

### Tablas

#### `proyectos`
```sql
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('activo', 'pausado', 'completado', 'cancelado'),
    fecha_inicio DATE,
    fecha_fin DATE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### `tareas`
```sql
CREATE TABLE IF NOT EXISTS tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proyecto_id INT (nullable),  -- NULL = tarea imprevista
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    estado ENUM('pendiente', 'en_progreso', 'completada', 'cancelada'),
    prioridad ENUM('baja', 'media', 'alta', 'urgente'),
    fecha_vencimiento DATE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE
);
```

#### `bitacora`
```sql
CREATE TABLE IF NOT EXISTS bitacora (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tarea_id INT (nullable),  -- NULL = registros generales
    tipo_registro ENUM('creacion', 'actualizacion', 'completada', 'nota', 'cambio_estado'),
    descripcion TEXT NOT NULL,
    autor VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tarea_id) REFERENCES tareas(id) ON DELETE CASCADE
);
```

---

## 🚀 Endpoints

### Base URL
```
http://tu-servidor.com/ajax/proyectos.php
http://tu-servidor.com/ajax/tareas.php
http://tu-servidor.com/ajax/bitacora.php
```

---

## 📂 PROYECTOS

### GET: Listar todos los proyectos
**Endpoint:** `GET /ajax/proyectos.php`

**Parámetros opcionales:**
- `estado` (activo|pausado|completado|cancelado)

**Ejemplo:**
```javascript
fetch('ajax/proyectos.php?estado=activo')
    .then(r => r.json())
    .then(data => console.log(data));
```

**Respuesta:**
```json
[
  {
    "id": 1,
    "nombre": "Proyecto A",
    "descripcion": "Descripción del proyecto",
    "estado": "activo",
    "fecha_inicio": "2026-01-15",
    "fecha_fin": "2026-12-31",
    "fecha_creacion": "2026-01-10 10:30:00",
    "fecha_actualizacion": "2026-01-15 14:22:00"
  }
]
```

---

### GET: Obtener un proyecto específico
**Endpoint:** `GET /ajax/proyectos.php?id=1`

**Respuesta:**
```json
{
  "id": 1,
  "nombre": "Proyecto A",
  "descripcion": "...",
  "estado": "activo",
  "fecha_inicio": "2026-01-15",
  "fecha_fin": "2026-12-31",
  "fecha_creacion": "2026-01-10 10:30:00",
  "fecha_actualizacion": "2026-01-15 14:22:00",
  "estadisticas": {
    "id": 1,
    "nombre": "Proyecto A",
    "total_tareas": 10,
    "tareas_completadas": 5,
    "tareas_en_progreso": 3,
    "tareas_pendientes": 2,
    "total_registros_bitacora": 25
  }
}
```

---

### GET: Obtener estadísticas
**Endpoint:** `GET /ajax/proyectos.php?id=1&accion=estadisticas`

**Respuesta:**
```json
{
  "id": 1,
  "nombre": "Proyecto A",
  "total_tareas": 10,
  "tareas_completadas": 5,
  "tareas_en_progreso": 3,
  "tareas_pendientes": 2,
  "total_registros_bitacora": 25
}
```

---

### POST: Crear proyecto
**Endpoint:** `POST /ajax/proyectos.php`

**Headers:**
```
Content-Type: application/json
```

**Body:**
```json
{
  "nombre": "Nuevo Proyecto",
  "descripcion": "Descripción opcional",
  "estado": "activo",
  "fecha_inicio": "2026-02-01",
  "fecha_fin": "2026-06-30"
}
```

**Ejemplo JavaScript:**
```javascript
fetch('ajax/proyectos.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    nombre: 'Nuevo Proyecto',
    descripcion: 'Descripción',
    estado: 'activo',
    fecha_inicio: '2026-02-01',
    fecha_fin: '2026-06-30'
  })
})
.then(r => r.json())
.then(data => console.log(data));
```

**Respuesta:**
```json
{
  "success": true,
  "id": 5,
  "mensaje": "Proyecto creado exitosamente"
}
```

---

### PUT: Actualizar proyecto
**Endpoint:** `PUT /ajax/proyectos.php?id=1`

**Body:**
```json
{
  "nombre": "Proyecto A Actualizado",
  "estado": "completado"
}
```

**Respuesta:**
```json
{
  "success": true,
  "mensaje": "Proyecto actualizado exitosamente"
}
```

---

### DELETE: Eliminar proyecto
**Endpoint:** `DELETE /ajax/proyectos.php?id=1`

**Ejemplo JavaScript:**
```javascript
fetch('ajax/proyectos.php?id=1', {
  method: 'DELETE'
})
.then(r => r.json())
.then(data => console.log(data));
```

**Respuesta:**
```json
{
  "success": true,
  "mensaje": "Proyecto eliminado exitosamente"
}
```

---

## 📝 TAREAS

### GET: Listar todas las tareas
**Endpoint:** `GET /ajax/tareas.php`

**Parámetros opcionales:**
- `proyecto_id` - Filtrar por proyecto
- `estado` (pendiente|en_progreso|completada|cancelada)
- `prioridad` (baja|media|alta|urgente)

**Ejemplo:**
```javascript
fetch('ajax/tareas.php?proyecto_id=1&estado=pendiente')
    .then(r => r.json())
    .then(data => console.log(data));
```

---

### GET: Tareas pendientes
**Endpoint:** `GET /ajax/tareas.php?accion=pendientes`

---

### GET: Tareas imprevistas (sin proyecto)
**Endpoint:** `GET /ajax/tareas.php?accion=imprevistas`

**Respuesta:**
```json
[
  {
    "id": 15,
    "proyecto_id": null,
    "nombre": "Tarea imprevista 1",
    "descripcion": "...",
    "estado": "pendiente",
    "prioridad": "alta",
    "fecha_vencimiento": "2026-02-20"
  }
]
```

---

### GET: Tareas próximas a vencer
**Endpoint:** `GET /ajax/tareas.php?accion=proximas&dias=7`

---

### GET: Tareas por proyecto
**Endpoint:** `GET /ajax/tareas.php?accion=por_proyecto&proyecto_id=1`

---

### POST: Crear tarea
**Endpoint:** `POST /ajax/tareas.php`

**Body:**
```json
{
  "nombre": "Nueva Tarea",
  "descripcion": "Descripción",
  "proyecto_id": 1,
  "estado": "pendiente",
  "prioridad": "alta",
  "fecha_vencimiento": "2026-02-28",
  "autor": "Usuario"
}
```

**Respuesta:**
```json
{
  "success": true,
  "id": 20,
  "mensaje": "Tarea creada exitosamente"
}
```

> ✅ **Nota**: Al crear una tarea, se registra automáticamente en bitácora.

---

### PUT: Actualizar tarea
**Endpoint:** `PUT /ajax/tareas.php?id=1`

**Body:**
```json
{
  "nombre": "Tarea actualizada",
  "estado": "en_progreso"
}
```

---

### PUT: Cambiar estado de tarea
**Endpoint:** `PUT /ajax/tareas.php?id=1&accion=cambiar_estado`

**Body:**
```json
{
  "estado": "completada",
  "autor": "Usuario"
}
```

**Respuesta:**
```json
{
  "success": true,
  "mensaje": "Tarea marcada como completada"
}
```

> ✅ **Nota**: Al cambiar estado, se registra automáticamente en bitácora.

---

### DELETE: Eliminar tarea
**Endpoint:** `DELETE /ajax/tareas.php?id=1`

---

## 📜 BITÁCORA

### GET: Listar todos los registros
**Endpoint:** `GET /ajax/bitacora.php`

**Parámetros opcionales:**
- `tarea_id` - Filtrar por tarea
- `tipo_registro` (creacion|actualizacion|completada|nota|cambio_estado)

---

### GET: Registros de una tarea específica
**Endpoint:** `GET /ajax/bitacora.php?tarea_id=1`

**Respuesta:**
```json
[
  {
    "id": 1,
    "tarea_id": 1,
    "tipo_registro": "creacion",
    "descripcion": "Tarea creada: Mi Tarea",
    "autor": "Usuario",
    "fecha_registro": "2026-01-15 10:30:00"
  },
  {
    "id": 2,
    "tarea_id": 1,
    "tipo_registro": "cambio_estado",
    "descripcion": "Estado cambió a: en_progreso",
    "autor": "Usuario",
    "fecha_registro": "2026-01-16 14:22:00"
  }
]
```

---

### GET: Historial completo de una tarea
**Endpoint:** `GET /ajax/bitacora.php?accion=historial&tarea_id=1`

**Respuesta:**
```json
[
  {
    "tarea_id": 1,
    "tarea_nombre": "Mi Tarea",
    "tarea_estado": "en_progreso",
    "prioridad": "alta",
    "proyecto_id": 1,
    "bitacora_id": 2,
    "tipo_registro": "cambio_estado",
    "descripcion": "Estado cambió a: en_progreso",
    "autor": "Usuario",
    "fecha_registro": "2026-01-16 14:22:00"
  }
]
```

---

### GET: Reporte de bitácora por fechas
**Endpoint:** `GET /ajax/bitacora.php?accion=reporte&fecha_inicio=2026-01-01&fecha_fin=2026-01-31`

**Respuesta:**
```json
[
  {
    "fecha": "2026-01-15",
    "tipo_registro": "creacion",
    "cantidad": 5,
    "autores": 3
  },
  {
    "fecha": "2026-01-16",
    "tipo_registro": "cambio_estado",
    "cantidad": 8,
    "autores": 2
  }
]
```

---

### POST: Crear registro de bitácora
**Endpoint:** `POST /ajax/bitacora.php`

**Body:**
```json
{
  "tarea_id": 1,
  "tipo_registro": "nota",
  "descripcion": "Se agregó una nota importante",
  "autor": "Usuario"
}
```

**Respuesta:**
```json
{
  "success": true,
  "id": 100,
  "mensaje": "Registro de bitácora creado exitosamente"
}
```

---

### PUT: Actualizar registro
**Endpoint:** `PUT /ajax/bitacora.php?id=1`

**Body:**
```json
{
  "descripcion": "Descripción actualizada"
}
```

---

### DELETE: Eliminar registro
**Endpoint:** `DELETE /ajax/bitacora.php?id=1`

---

## 🔍 Validación y Errores

### Validación HTTP
- **400**: Request inválido (datos faltantes)
- **404**: Recurso no encontrado
- **500**: Error del servidor

### Respuestas de error

```json
{
  "error": "El nombre del proyecto es requerido"
}
```

```json
{
  "success": false,
  "mensaje": "Error al actualizar: ..."
}
```

---

## 💾 Instalación

### 1. Crear las tablas
```bash
mysql -u root -p informes < scripts/crear_tablas_proyectos.sql
```

### 2. Verificar estructura
```bash
mysql -u root -p informes
SHOW TABLES;
DESCRIBE proyectos;
DESCRIBE tareas;
DESCRIBE bitacora;
```

---

## 🧪 Ejemplos de uso completo

### Crear proyecto → Tareas → Registrar en bitácora

```javascript
// 1. Crear proyecto
async function crearProyecto() {
  const res = await fetch('ajax/proyectos.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      nombre: 'Mi Proyecto',
      descripcion: 'Descripción',
      estado: 'activo'
    })
  });
  return await res.json();
}

// 2. Crear tarea en el proyecto
async function crearTarea(proyecto_id) {
  const res = await fetch('ajax/tareas.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      nombre: 'Tarea 1',
      proyecto_id: proyecto_id,
      prioridad: 'alta',
      autor: 'Juan'
    })
  });
  return await res.json();
}

// 3. Cambiar estado
async function completarTarea(tarea_id) {
  const res = await fetch(`ajax/tareas.php?id=${tarea_id}&accion=cambiar_estado`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      estado: 'completada',
      autor: 'Juan'
    })
  });
  return await res.json();
}

// 4. Obtener historial
async function obtenerHistorial(tarea_id) {
  const res = await fetch(`ajax/bitacora.php?accion=historial&tarea_id=${tarea_id}`);
  return await res.json();
}

// Ejecutar secuencialmente
(async () => {
  const proyecto = await crearProyecto();
  console.log('Proyecto creado:', proyecto);
  
  const tarea = await crearTarea(proyecto.id);
  console.log('Tarea creada:', tarea);
  
  const actualizado = await completarTarea(tarea.id);
  console.log('Tarea actualizada:', actualizado);
  
  const historial = await obtenerHistorial(tarea.id);
  console.log('Historial:', historial);
})();
```

---

## 📊 Integración con DataTables

```html
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="DataTables/datatables.min.css">
</head>
<body>

<table id="tablaTareas" class="display">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Estado</th>
            <th>Prioridad</th>
            <th>Acciones</th>
        </tr>
    </thead>
</table>

<script src="DataTables/datatables.min.js"></script>
<script>
$(document).ready(function() {
    $('#tablaTareas').DataTable({
        ajax: {
            url: 'ajax/tareas.php',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'estado' },
            { data: 'prioridad' },
            { data: null, 
              render: function(data) {
                return `<button onclick="completarTarea(${data.id})">Completar</button>`;
              }
            }
        ]
    });
});

function completarTarea(id) {
    fetch(`ajax/tareas.php?id=${id}&accion=cambiar_estado`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ estado: 'completada' })
    })
    .then(r => r.json())
    .then(data => {
        alert(data.mensaje);
        $('#tablaTareas').DataTable().ajax.reload();
    });
}
</script>
</body>
</html>
```

---

## ⚙️ Configuración

### Variables de entorno (opcional)
Edita `bd/conexion.php` para usar variables de entorno:

```php
$host = getenv('DB_HOST') ?: '172.10.18.128';
$dbname = getenv('DB_NAME') ?: 'informes';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'AdmSys2014';
```

---

## 📝 Notas importantes

✅ **Relaciones:**
- Proyecto → Tareas (1:N) con DELETE CASCADE
- Tarea → Bitácora (1:N) con DELETE CASCADE
- `proyecto_id` en tareas puede ser NULL (tareas imprevistas)
- `tarea_id` en bitácora puede ser NULL (registros generales)

✅ **Seguridad:**
- PDO con prepared statements previene SQL injection
- UTF-8 en todas las conexiones

✅ **Performance:**
- Índices en `proyecto_id`, `estado`, `fecha_vencimiento`
- Queries optimizadas con JOINs

---

## 🤝 Soporte

Para reportar bugs o sugerencias, documenta el problema con:
- Endpoint utilizado
- Body de la request
- Error recibido
- Tabla Y BASE DE DATOS INVOLUCRADA
