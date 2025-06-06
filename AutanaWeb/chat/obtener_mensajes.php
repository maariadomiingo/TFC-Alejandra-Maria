<?php
session_start();
require_once __DIR__ . '/../server/db_config.php';
header('Content-Type: application/json');

$producto_id = $_GET['producto_id'] ?? null;
$cliente_id = $_SESSION['user_id'] ?? null;

if (!$producto_id || !$cliente_id) {
    echo json_encode(['mensajes' => []]);
    exit;
}

// Buscar chat
$stmt = $pdo->prepare("SELECT id FROM chats WHERE producto_id = ? AND cliente_id = ?");
$stmt->execute([$producto_id, $cliente_id]);
$chat = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$chat) {
    echo json_encode(['mensajes' => []]);
    exit;
}

$chat_id = $chat['id'];

// Obtener mensajes
$stmt = $pdo->prepare("SELECT m.*, u.nombre AS remitente FROM mensajes m JOIN Usuarios u ON m.remitente_id = u.id WHERE chat_id = ? ORDER BY enviado_en ASC");
$stmt->execute([$chat_id]);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['mensajes' => $mensajes]);
?>