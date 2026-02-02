<?php
// insertar_variedad.php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// ==================== CONFIGURACIÓN DE LA BASE DE DATOS ====================
// USA LOCALHOST - MySQL está en el mismo servidor
$servername = "localhost";
$username = "root";
$password = "AdmSys2014";
$dbname = "informes";

// ==================== LOGGING PARA DEPURACIÓN ====================
error_log("=== SOLICITUD RECIBIDA DESDE: " . ($_SERVER['REMOTE_ADDR'] ?? 'DESCONOCIDO') . " ===");
error_log("Datos POST recibidos: " . print_r($_POST, true));

// ==================== CONEXIÓN A LA BASE DE DATOS ====================
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Error de conexión a la BD: " . $conn->connect_error);
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error de conexión a la base de datos: " . $conn->connect_error]);
    exit();
}

// ==================== CAPTURA Y VALIDACIÓN DE DATOS ====================
$nombre = trim($_POST['nombre'] ?? '');
$producto = trim($_POST['producto'] ?? '');
$color = trim($_POST['color'] ?? '');
$ciclo = $_POST['ciclo'] ?? null;
$codvari = $_POST['codvari'] ?? '';
$casa_comercial = $_POST['casa_comercial'] ?? '';
$estado = $_POST['estado'] ?? 0;

// Log de valores recibidos
error_log("Datos procesados:");
error_log(" - nombre: '$nombre'");
error_log(" - producto: '$producto'");
error_log(" - color: '$color'");
error_log(" - ciclo: " . ($ciclo === null ? 'NULL' : "'$ciclo'"));
error_log(" - codvari: '$codvari'");
error_log(" - casa_comercial: '$casa_comercial'");
error_log(" - estado: '$estado'");

// ==================== VALIDACIÓN DE CAMPOS OBLIGATORIOS ====================
$campos_faltantes = [];

if (empty($nombre)) $campos_faltantes[] = 'nombre';
if (empty($producto)) $campos_faltantes[] = 'producto';
if (empty($color)) $campos_faltantes[] = 'color';
if (empty($codvari)) $campos_faltantes[] = 'codvari';
if (empty($casa_comercial)) $campos_faltantes[] = 'casa_comercial';

if (!empty($campos_faltantes)) {
    error_log("ERROR: Campos obligatorios faltantes: " . implode(', ', $campos_faltantes));
    http_response_code(400);
    echo json_encode([
        "status" => "error", 
        "message" => "Faltan campos obligatorios",
        "campos_faltantes" => $campos_faltantes
    ]);
    $conn->close();
    exit();
}

// ==================== PROCESAMIENTO Y SANITIZACIÓN DE DATOS ====================
// Convertir tipos de datos
$casa_comercial = (int)$casa_comercial;
$estado = (int)$estado;

// Manejar campo ciclo (puede ser NULL)
if ($ciclo !== null && $ciclo !== '') {
    $ciclo = (int)$ciclo;
} else {
    $ciclo = null;
}

// codvari es CHAR(10) - asegurar longitud correcta
$codvari = substr(trim($codvari), 0, 10);
$codvari = str_pad($codvari, 10, ' ', STR_PAD_RIGHT);

// ==================== INSERCIÓN EN LA BASE DE DATOS ====================
$sql = "INSERT INTO varieties (nombre, producto, color, ciclo, codvari, casa_comercial, estado) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Error preparando consulta: " . $conn->error);
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error interno del servidor: " . $conn->error]);
    $conn->close();
    exit();
}

// Bind parameters - CORREGIDO: 'ciclo' en lugar de 'odo'
$stmt->bind_param("sssisii", $nombre, $producto, $color, $ciclo, $codvari, $casa_comercial, $estado);

if ($stmt->execute()) {
    $id_insertado = $stmt->insert_id;
    error_log("✅ Inserción exitosa. ID: $id_insertado");
    
    echo json_encode([
        "status" => "success", 
        "message" => "Variedad insertada correctamente",
        "id" => $id_insertado
    ]);
} else {
    error_log("❌ Error en la inserción: " . $stmt->error);
    
    // Manejar error de duplicado (nombre es UNIQUE)
    if ($stmt->errno == 1062) {
        http_response_code(409);
        echo json_encode([
            "status" => "error", 
            "message" => "Ya existe una variedad con este nombre"
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            "status" => "error", 
            "message" => "Error al guardar en la base de datos: " . $stmt->error
        ]);
    }
}

// ==================== LIMPIEZA ====================
$stmt->close();
$conn->close();

error_log("=== PROCESO FINALIZADO ===");
?>