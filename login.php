<?php
if (session_status() === PHP_SESSION_NONE) session_start();
//lamar conexion
if (is_file("funciones/conexion.php")){
  include ("funciones/conexion.php");
}else{
  include ("../funciones/conexion.php");
}
  //comprobar si se logea en formulario

if (isset($_POST['login'])) {
    $usuario = trim($_POST['email'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($usuario === '' || $password === '') {
        \App\Helpers\Auth::logLogin($conexion, null, $usuario ?: 'VACIO', 'FALLIDO', 'Campos requeridos incompletos');
        header("location: index.php?error=1");
        exit;
    }

    $stmt = $conexion->prepare("SELECT id, name, password, email, role, activo FROM users WHERE email = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            $userId = (int)$row['id'];
            $userEmail = (string)$row['email'];
            $isActivo = isset($row['activo']) ? (int)$row['activo'] : 1;

            // Verificar si la cuenta está desactivada por el administrador
            if ($isActivo === 0) {
                \App\Helpers\Auth::logLogin($conexion, $userId, $userEmail, 'BLOQUEADO', 'Cuenta desactivada por el administrador');
                $stmt->close();
                header("location: index.php?error=inactive");
                exit;
            }

            $stored = (string)$row['password'];
            $isValid = false;

            // 1. Si ya está hasheada con bcrypt/argon2
            if (password_verify($password, $stored)) {
                $isValid = true;
                // Si requiere rehash por actualización de costo/algoritmo
                if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $up = $conexion->prepare("UPDATE users SET password = ? WHERE id = ?");
                    if ($up) {
                        $up->bind_param("si", $newHash, $userId);
                        $up->execute();
                        $up->close();
                    }
                }
            } elseif ($stored === $password) {
                // 2. Contraseña heredada en texto plano: verificar y migrar automáticamente a hash seguro
                $isValid = true;
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                $up = $conexion->prepare("UPDATE users SET password = ? WHERE id = ?");
                if ($up) {
                    $up->bind_param("si", $newHash, $userId);
                    $up->execute();
                    $up->close();
                }
            }

            if ($isValid) {
                session_regenerate_id(true);
                $_SESSION['usuario']       = $row['name'];
                $_SESSION['email']         = $row['email'];
                $_SESSION['role']          = intval($row['role']);
                $_SESSION['id']            = $userId;
                $_SESSION['LAST_ACTIVITY'] = time();
                unset($_SESSION['role_name']);
                unset($_SESSION['allowed_dirs']); // Forzar refresco de permisos

                \App\Helpers\Auth::logLogin($conexion, $userId, $userEmail, 'EXITOSO', 'Credenciales válidas');
                $stmt->close();
                header("location: index.php");
                exit;
            } else {
                \App\Helpers\Auth::logLogin($conexion, $userId, $userEmail, 'FALLIDO', 'Contraseña incorrecta');
            }
        } else {
            \App\Helpers\Auth::logLogin($conexion, null, $usuario, 'FALLIDO', 'Usuario no registrado');
        }
        $stmt->close();
    }

    header("location: index.php?error=1");
    exit;
}
?>
