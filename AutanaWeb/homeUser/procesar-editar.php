<?php
require_once '../server/db_config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}

$usuario_id = $_SESSION['user_id'];
$correo = $_POST['correo'] ?? '';
$password = $_POST['password'] ?? '';
$talla_top = $_POST['talla_top'] ?? '';
$talla_bottom = $_POST['talla_bottom'] ?? '';
$direccion_envio = $_POST['direccion_envio'] ?? '';

// Verificar si se cambió el correo
$stmt = $pdo->prepare("SELECT correo FROM Usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch();

if ($correo !== $usuario['correo']) {
    // Verifica si el correo ya existe
    $checkCorreo = $pdo->prepare("SELECT id FROM Usuarios WHERE correo = ?");
    $checkCorreo->execute([$correo]);

    if ($checkCorreo->rowCount() > 0) {
        die("Ese correo ya está registrado.");
    }

    // Simula envío de confirmación (puedes integrar PHPMailer aquí)
    // mail($correo, "Confirma tu nuevo correo", "Haz clic en este enlace para confirmar...");

    $updateCorreo = $pdo->prepare("UPDATE Usuarios SET correo = ? WHERE id = ?");
    $updateCorreo->execute([$correo, $usuario_id]);
}

// Si hay nueva contraseña, actualizarla
if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
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
