<?php
session_start();
require_once __DIR__ . '/../server/db_config.php';
header('Content-Type: application/json');

$chat_id = $_GET['chat_id'] ?? null;

if (!$chat_id) {
    echo json_encode(['mensajes' => []]);
    exit;
}

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

echo json_encode(['mensajes' => $mensajes]);
