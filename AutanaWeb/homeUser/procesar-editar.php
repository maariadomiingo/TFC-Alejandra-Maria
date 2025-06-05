<?php
require_once '../server/db_config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}

$usuario_id = $_SESSION['user_id'];
$correo = $_POST['correo'] ?? '';
$nuevaPassword = $_POST['password'] ?? '';
$passwordActual = $_POST['current_password'] ?? '';
$talla_top = $_POST['talla_top'] ?? '';
$talla_bottom = $_POST['talla_bottom'] ?? '';
$direccion_envio = $_POST['direccion_envio'] ?? '';

// Obtener contraseña actual del usuario
$stmt = $pdo->prepare("SELECT correo, password FROM Usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!password_verify($passwordActual, $usuario['password'])) {
    header("Location: editar-perfil.php?error=Contraseña actual incorrecta");
    exit;
}


// Actualizar correo si se modificó
if ($correo !== $usuario['correo']) {
    $checkCorreo = $pdo->prepare("SELECT id FROM Usuarios WHERE correo = ?");
    $checkCorreo->execute([$correo]);

    if ($checkCorreo->rowCount() > 0) {
        die("Ese correo ya está registrado.");
    }

    // Simular envío de confirmación por correo (puedes usar PHPMailer aquí)
    // mail($correo, "Confirma tu nuevo correo", "Haz clic aquí para confirmar tu nuevo correo...");

    $updateCorreo = $pdo->prepare("UPDATE Usuarios SET correo = ? WHERE id = ?");
    $updateCorreo->execute([$correo, $usuario_id]);
}

// Actualizar contraseña si se ingresó una nueva
if (!empty($nuevaPassword)) {
    $hashed = password_hash($nuevaPassword, PASSWORD_DEFAULT);
    $updatePassword = $pdo->prepare("UPDATE Usuarios SET password = ? WHERE id = ?");
    $updatePassword->execute([$hashed, $usuario_id]);
}

// Actualizar o insertar perfil
$stmt = $pdo->prepare("SELECT id FROM user_profiles WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);

if ($stmt->rowCount() > 0) {
    $updateProfile = $pdo->prepare("UPDATE user_profiles SET talla_top = ?, talla_bottom = ?, direccion_envio = ? WHERE usuario_id = ?");
    $updateProfile->execute([$talla_top, $talla_bottom, $direccion_envio, $usuario_id]);
} else {
    $insertProfile = $pdo->prepare("INSERT INTO user_profiles (usuario_id, talla_top, talla_bottom, direccion_envio) VALUES (?, ?, ?, ?)");
    $insertProfile->execute([$usuario_id, $talla_top, $talla_bottom, $direccion_envio]);
}

header("Location: editar-perfil.php?exito=1");
exit;
?>
