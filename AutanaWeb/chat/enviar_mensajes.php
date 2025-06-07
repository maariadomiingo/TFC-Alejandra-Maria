<?php
session_start();
require_once __DIR__ . '/../server/db_config.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

$mensaje = trim($data['mensaje'] ?? '');
$producto_id = $data['producto_id'] ?? null;
$chat_id = $data['chat_id'] ?? null;
$is_admin = !empty($data['admin']) && isset($_SESSION['admin_id']);

if (!$mensaje) {
    echo json_encode(['success' => false, 'message' => 'Mensaje vacío']);
    exit;
}

if ($is_admin && $chat_id) {
    $remitente_id = $_SESSION['admin_id'];
    $remitente_tipo = 'admin';
} else {
    if (!isset($_SESSION['user_id']) || !$producto_id) {
        echo json_encode(['success' => false, 'message' => 'No autenticado o sin producto']);
        exit;
    }
    $remitente_id = $_SESSION['user_id'];
    $remitente_tipo = 'usuario';
    // Buscar o crear chat
    $stmt = $pdo->prepare("SELECT id FROM chats WHERE producto_id = ? AND cliente_id = ?");
    $stmt->execute([$producto_id, $remitente_id]);
    $chat = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$chat) {
        $stmt = $pdo->prepare("INSERT INTO chats (producto_id, cliente_id) VALUES (?, ?)");
        $stmt->execute([$producto_id, $remitente_id]);
        $chat_id = $pdo->lastInsertId();
    } else {
        $chat_id = $chat['id'];
    }
}

// Insertar mensaje
$stmt = $pdo->prepare("INSERT INTO mensajes (chat_id, remitente_id, mensaje, remitente_tipo) VALUES (?, ?, ?, ?)");
$stmt->execute([$chat_id, $remitente_id, $mensaje, $remitente_tipo]);

echo json_encode(['success' => true]);

file_put_contents(__DIR__ . '/debug_admin.txt', print_r($_SESSION, true));
