<?php
session_start(); // ✅ Iniciar la sesión antes de cualquier salida
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $dbname = 'autana';

    try {
        // Conectar sin base de datos para crearla si no existe
        $pdo = new PDO("mysql:host=$host", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear base de datos si no existe
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Conectar a la base de datos
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crear tabla si no existe
        $pdo->exec("CREATE TABLE IF NOT EXISTS Usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            correo VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Insertar usuarios de prueba si no existen
        $usuarios = [
            ['nombre' => 'maria', 'correo' => 'domingopuentemaria@gmail.com', 'password' => '123456'],
            ['nombre' => 'alejandra', 'correo' => 'ale@ejemplo.com', 'password' => '123456']
        ];

        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM Usuarios WHERE correo = :correo");
        $insertStmt = $pdo->prepare("INSERT INTO Usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)");

        foreach ($usuarios as $usuario) {
            $checkStmt->execute([':correo' => $usuario['correo']]);
            $exists = $checkStmt->fetchColumn();

            if ($exists == 0) {
                $hashedPassword = password_hash($usuario['password'], PASSWORD_DEFAULT);
                $insertStmt->execute([
                    ':nombre' => $usuario['nombre'],
                    ':correo' => $usuario['correo'],
                    ':password' => $hashedPassword
                ]);
            }
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error al conectar o preparar la base de datos: " . $e->getMessage()]);
        exit;
    }

    // Obtener datos del formulario
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // Validación de datos vacíos
    if (!$email || !$password) {
        echo json_encode(["success" => false, "message" => "Faltan datos"]);
        exit;
    }

    // Verificación de usuario
    try {
        $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE correo = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password'])) {
            // ✅ Guardar sesión
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nombre'];

            echo json_encode(["success" => true, "message" => "Login correcto"]);
        } else {
            echo json_encode(["success" => false, "message" => "Correo o contraseña incorrectos"]);
        }

    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en la base de datos: " . $e->getMessage()]);
    }

} else {
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
}
