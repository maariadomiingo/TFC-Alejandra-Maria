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
$nuevaPassword = $input['nueva_password'] ?? '';

if (!$nuevaPassword) {
    echo json_encode(['success' => false, 'message' => 'La nueva contraseña es obligatoria']);
    exit;
}

try {
    $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE Usuarios SET password = ? WHERE id = ?");
    $stmt->execute([$hash, $usuario_id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la contraseña']);
}
?>