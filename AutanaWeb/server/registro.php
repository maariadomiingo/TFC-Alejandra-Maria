<?php
session_start();
header('Content-Type: application/json');
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$password || !$confirm_password) {
        echo json_encode(["success" => false, "message" => "Todos los campos obligatorios deben estar completos."]);
        exit;
    }

    if ($password !== $confirm_password) {
        echo json_encode(["success" => false, "message" => "Las contraseñas no coinciden."]);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO Usuarios (nombre, correo, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword]);
        $userId = $pdo->lastInsertId();

        // ✅ Iniciar sesión automáticamente
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            echo json_encode(["success" => false, "message" => "El correo electrónico ya está registrado."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al registrar: " . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido."]);
}
?>
