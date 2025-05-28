<?php
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperamos los datos del formulario
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validamos que los campos obligatorios estén presentes
    if (!$name || !$email || !$password || !$confirm_password) {
        echo "Todos los campos obligatorios deben estar completos.";
        exit;
    }

    // Validar que las contraseñas coincidan
    if ($password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
        exit;
    }

    // Hashear la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insertar el usuario en la tabla 'Usuarios'
        $stmt = $pdo->prepare("INSERT INTO Usuarios (nombre, correo, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword]);
        $userId = $pdo->lastInsertId();

        // Aquí no insertamos datos de 'user_profiles' porque ya no son necesarios
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
