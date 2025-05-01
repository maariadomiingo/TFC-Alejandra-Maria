<?php
header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once '../server/db_config.php';

    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // Validación de campos
    if (!$email || !$password) {
        echo json_encode(["success" => false, "message" => "Faltan datos"]);
        exit;
    }

    try {
        // Preparar y ejecutar consulta para obtener el usuario con ese correo
        $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE correo = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el usuario existe y si la contraseña coincide
        if ($usuario) {
            if (password_verify($password, $usuario['password'])) {
                echo json_encode(["success" => true, "message" => "Login correcto"]);
            } else {
                echo json_encode(["success" => false, "message" => "Correo o contraseña incorrectos"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Correo o contraseña incorrectos"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error en la base de datos: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido"]);
}
?>
