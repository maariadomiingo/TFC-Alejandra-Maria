<?php
session_start();
require_once __DIR__ . '/../server/db_config.php';
header('Content-Type: application/json');

$chat_id = $_GET['chat_id'] ?? null;

if (!$chat_id) {
    echo json_encode(['mensajes' => []]);
    exit;
}

$stmt = $pdo->prepare("SELECT m.*, u.nombre AS remitente FROM mensajes m JOIN Usuarios u ON m.remitente_id = u.id WHERE chat_id = ? ORDER BY enviado_en ASC");
$stmt->execute([$chat_id]);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['mensajes' => $mensajes]);
