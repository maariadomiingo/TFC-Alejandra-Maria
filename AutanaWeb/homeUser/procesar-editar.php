<?php
require_once '../server/db_config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}

// Solo requerir verificación para cambios sensibles
$requireVerification = false;

// Verificar si se están haciendo cambios sensibles
if ((isset($_POST['correo']) && $_POST['correo'] !== $_SESSION['user_email']) || 
    !empty($_POST['password'])) {
    $requireVerification = true;
}

if ($requireVerification) {
    if (empty($_POST['current_password'])) {
        $_SESSION['pending_changes'] = $_POST;
        header("Location: editar-perfil.php?error=Se requiere la contraseña actual para realizar cambios");
        exit;
    }
    
    // Verificar contraseña actual
    $stmt = $pdo->prepare("SELECT password FROM Usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!password_verify($_POST['current_password'], $user['password'])) {
        $_SESSION['pending_changes'] = $_POST;
        header("Location: editar-perfil.php?error=Contraseña actual incorrecta");
        exit;
    }
}

// Validar y sanitizar entradas
$usuario_id = $_SESSION['user_id'];
$correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
$nuevaPassword = $_POST['password'] ?? '';
$passwordActual = $_POST['current_password'] ?? '';

// Validar medidas corporales
$hombro = is_numeric($_POST['hombro'] ?? null) ? (float)$_POST['hombro'] : null;
$pecho = is_numeric($_POST['pecho'] ?? null) ? (float)$_POST['pecho'] : null;
$cintura = is_numeric($_POST['cintura'] ?? null) ? (float)$_POST['cintura'] : null;
$cadera = is_numeric($_POST['cadera'] ?? null) ? (float)$_POST['cadera'] : null;
$altura = is_numeric($_POST['altura'] ?? null) ? (float)$_POST['altura'] : null;

// Validar dirección
$direccion_calle = filter_input(INPUT_POST, 'direccion_calle', FILTER_SANITIZE_STRING);
$direccion_ciudad = filter_input(INPUT_POST, 'direccion_ciudad', FILTER_SANITIZE_STRING);
$direccion_estado = filter_input(INPUT_POST, 'direccion_estado', FILTER_SANITIZE_STRING);
$direccion_codigo_postal = filter_input(INPUT_POST, 'direccion_codigo_postal', FILTER_SANITIZE_STRING);
$direccion_pais = filter_input(INPUT_POST, 'direccion_pais', FILTER_SANITIZE_STRING);

try {
    // Obtener usuario actual
    $stmt = $pdo->prepare("SELECT correo, password FROM Usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception("Usuario no encontrado");
    }

    // Verificar contraseña actual (solo si se está cambiando el correo o contraseña)
    if ((!empty($correo) && $correo !== $usuario['correo']) || !empty($nuevaPassword)) {
        if (empty($passwordActual)) {
            throw new Exception("Se requiere la contraseña actual para realizar cambios");
        }
        if (!password_verify($passwordActual, $usuario['password'])) {
            header("Location: editar-perfil.php?error=Contraseña actual incorrecta");
            exit;
        }
    }

    // Actualizar correo si es diferente y válido
    if (!empty($correo) && $correo !== $usuario['correo']) {
        $checkCorreo = $pdo->prepare("SELECT id FROM Usuarios WHERE correo = ?");
        $checkCorreo->execute([$correo]);

        if ($checkCorreo->rowCount() > 0) {
            throw new Exception("El correo electrónico ya está registrado");
        }

        $updateCorreo = $pdo->prepare("UPDATE Usuarios SET correo = ? WHERE id = ?");
        if (!$updateCorreo->execute([$correo, $usuario_id])) {
            throw new Exception("Error al actualizar el correo electrónico");
        }
    }

    // Actualizar contraseña si se proporcionó una nueva
    if (!empty($nuevaPassword)) {
        $hashed = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        $updatePassword = $pdo->prepare("UPDATE Usuarios SET password = ? WHERE id = ?");
        if (!$updatePassword->execute([$hashed, $usuario_id])) {
            throw new Exception("Error al actualizar la contraseña");
        }
    }

    // Actualizar perfil
    $stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);

    if ($stmt->rowCount() > 0) {
        $updateProfile = $pdo->prepare("UPDATE user_profiles SET
            hombro = ?, pecho = ?, cintura = ?, cadera = ?, altura = ?,
            direccion_calle = ?, direccion_ciudad = ?, direccion_estado = ?, 
            direccion_codigo_postal = ?, direccion_pais = ?
            WHERE usuario_id = ?");
        
        if (!$updateProfile->execute([
            $hombro, $pecho, $cintura, $cadera, $altura,
            $direccion_calle, $direccion_ciudad, $direccion_estado,
            $direccion_codigo_postal, $direccion_pais,
            $usuario_id
        ])) {
            throw new Exception("Error al actualizar el perfil");
        }
    } else {
        $insertProfile = $pdo->prepare("INSERT INTO user_profiles 
            (usuario_id, hombro, pecho, cintura, cadera, altura,
             direccion_calle, direccion_ciudad, direccion_estado, 
             direccion_codigo_postal, direccion_pais) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$insertProfile->execute([
            $usuario_id, $hombro, $pecho, $cintura, $cadera, $altura,
            $direccion_calle, $direccion_ciudad, $direccion_estado,
            $direccion_codigo_postal, $direccion_pais
        ])) {
            throw new Exception("Error al crear el perfil");
        }
    }

    header("Location: editar-perfil.php?exito=1");
    exit;

} catch (Exception $e) {
    header("Location: editar-perfil.php?error=" . urlencode($e->getMessage()));
    exit;
}