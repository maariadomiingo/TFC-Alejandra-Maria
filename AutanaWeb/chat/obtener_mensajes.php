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

// Traer mensajes y distinguir si el remitente es admin o usuario
$stmt = $pdo->prepare("
    SELECT m.*, 
        CASE 
            WHEN m.remitente_tipo = 'admin' THEN 'Admin'
            ELSE u.nombre
        END AS remitente,
        m.remitente_tipo
    FROM mensajes m
    LEFT JOIN Usuarios u ON m.remitente_id = u.id
    WHERE m.chat_id = ?
    ORDER BY m.enviado_en ASC
");
$stmt->execute([$chat_id]);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($mensajes as &$msg) {
    // Cambia 'tipo_remitente' por 'remitente_tipo'
    $msg['texto_mensaje'] = ($msg['remitente_tipo'] === "admin" ? "Admin" : $msg['remitente']) . ": " . $msg['mensaje'];
}

echo json_encode(['mensajes' => $mensajes]);
