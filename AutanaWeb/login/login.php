<?php
session_start();
require_once __DIR__ . '/../server/init_db.php'; // Inicializa la BBDD y tablas si no existen
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Conexión a la base de datos ya está hecha en init_db.php y está en $pdo

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
            // Guardar sesión
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
