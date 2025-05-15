<?php
require_once 'db_config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $talla_top = $_POST['talla_top'] ?? '';
    $talla_bottom = $_POST['talla_bottom'] ?? '';
    $direccion_envio = $_POST['direccion_envio'] ?? '';

    if (!$name || !$email || !$password) {
        echo "Todos los campos obligatorios deben estar completos.";
        exit;
    }

    // Hashear contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insertar en users
        $stmt = $pdo->prepare("INSERT INTO Usuarios (nombre, correo, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword]);
        $userId = $pdo->lastInsertId();

        // Insertar en user_profiles
        $stmt = $pdo->prepare(
            "INSERT INTO user_profiles (usuario_id, talla_top, talla_bottom, direccion_envio) 
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $talla_top, $talla_bottom, $direccion_envio]);

        echo "¡Registro exitoso! Bienvenido/a a AUTANA.";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            echo "El correo electrónico ya está registrado.";
        } else {
            echo "Error al registrar: " . $e->getMessage();
        }
    }
} else {
    echo "Método no permitido.";
}
?>
