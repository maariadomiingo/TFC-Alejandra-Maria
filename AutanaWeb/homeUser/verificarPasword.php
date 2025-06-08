<?php
require_once '../server/db_config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$usuario_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents("php://input"), true);
$passwordActual = $input['password_actual'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT password FROM Usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception("Usuario no encontrado");
    }

    if (password_verify($passwordActual, $usuario['password'])) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception("Contraseña incorrecta");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>