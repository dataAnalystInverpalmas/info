// Variables globales
let tareaEnEdicion = null;

// Ejecutar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    cargarTareas();
    
    // Evento del formulario
    document.getElementById('formTarea').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (tareaEnEdicion) {
            actualizarTarea();
        } else {
            crearTarea();
        }
    });
});

// Crear una nueva tarea
function crearTarea() {
    const titulo = document.getElementById('titulo').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    
    if (!titulo || !descripcion) {
        mostrarAlerta('Por favor completa todos los campos', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('titulo', titulo);
    formData.append('descripcion', descripcion);
    
    fetch('api/crear.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Tarea creada exitosamente', 'success');
            document.getElementById('formTarea').reset();
            cargarTareas();
        } else {
            mostrarAlerta(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error al crear la tarea', 'danger');
    });
}

// Leer y mostrar todas las tareas
function cargarTareas() {
    fetch('api/leer.php')
    .then(response => response.json())
    .then(data => {
        const contenedor = document.getElementById('listaTareas');
        
        if (data.success && data.data.length > 0) {
            contenedor.innerHTML = '';
            
            data.data.forEach(tarea => {
                const fecha = new Date(tarea.fecha_creacion).toLocaleDateString('es-ES', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                const tareaDiv = document.createElement('div');
                tareaDiv.className = 'task-item';
                tareaDiv.innerHTML = `
                    <div class="task-title">${escaparHTML(tarea.titulo)}</div>
                    <div class="task-description">${escaparHTML(tarea.descripcion)}</div>
                    <div class="task-date">📅 ${fecha}</div>
                    <div class="task-actions">
                        <button class="btn btn-editar" onclick="prepararEdicion(${tarea.id}, '${escaparAtributo(tarea.titulo)}', '${escaparAtributo(tarea.descripcion)}')">
                            ✏️ Editar
                        </button>
                        <button class="btn btn-eliminar" onclick="confirmarEliminar(${tarea.id}, '${escaparAtributo(tarea.titulo)}')">
                            🗑️ Eliminar
                        </button>
                    </div>
                `;
                contenedor.appendChild(tareaDiv);
            });
        } else {
            contenedor.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p class="no-tasks">No hay tareas registradas. ¡Crea una nueva!</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error al cargar las tareas', 'danger');
    });
}

// Preparar formulario para editar
function prepararEdicion(id, titulo, descripcion) {
    tareaEnEdicion = id;
    document.getElementById('titulo').value = titulo;
    document.getElementById('descripcion').value = descripcion;
    
    const btnEnviar = document.getElementById('formTarea').querySelector('button');
    btnEnviar.textContent = '✏️ Actualizar Tarea';
    btnEnviar.classList.add('btn-warning');
    
    document.querySelector('.form-title').textContent = 'Editar Tarea';
    document.getElementById('titulo').focus();
    
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Actualizar una tarea
function actualizarTarea() {
    const titulo = document.getElementById('titulo').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    
    if (!titulo || !descripcion) {
        mostrarAlerta('Por favor completa todos los campos', 'warning');
        return;
    }
    
    const formData = new FormData();
    formData.append('id', tareaEnEdicion);
    formData.append('titulo', titulo);
    formData.append('descripcion', descripcion);
    
    fetch('api/actualizar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Tarea actualizada exitosamente', 'success');
            cancelarEdicion();
            cargarTareas();
        } else {
            mostrarAlerta(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error al actualizar la tarea', 'danger');
    });
}

// Confirmar eliminación
function confirmarEliminar(id, titulo) {
    if (confirm(`¿Estás seguro de que deseas eliminar la tarea "${titulo}"?`)) {
        eliminarTarea(id);
    }
}

// Eliminar una tarea
function eliminarTarea(id) {
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('api/eliminar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Tarea eliminada exitosamente', 'success');
            cargarTareas();
        } else {
            mostrarAlerta(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarAlerta('Error al eliminar la tarea', 'danger');
    });
}

// Cancelar edición
function cancelarEdicion() {
    tareaEnEdicion = null;
    document.getElementById('formTarea').reset();
    document.querySelector('.form-title').textContent = 'Crear Nueva Tarea';
    
    const btnEnviar = document.getElementById('formTarea').querySelector('button');
    btnEnviar.textContent = '➕ Crear Tarea';
    btnEnviar.classList.remove('btn-warning');
}

// Mostrar alertas
function mostrarAlerta(mensaje, tipo) {
    const contenedor = document.getElementById('alertas');
    
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo} alert-dismissible fade show`;
    alerta.role = 'alert';
    alerta.innerHTML = `
        ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    contenedor.appendChild(alerta);
    
    // Auto-cerrar después de 5 segundos
    setTimeout(() => {
        alerta.remove();
    }, 5000);
}

// Escapar HTML para seguridad
function escaparHTML(texto) {
    const mapa = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return texto.replace(/[&<>"']/g, m => mapa[m]);
}

// Escapar atributos
function escaparAtributo(texto) {
    return texto.replace(/'/g, "\\'").replace(/"/g, '\\"');
}
