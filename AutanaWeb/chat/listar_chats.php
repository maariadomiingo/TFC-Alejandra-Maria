<?php
session_start();
require_once __DIR__ . '/../server/db_config.php';
header('Content-Type: application/json');

// Aquí puedes comprobar si el usuario es admin si lo necesitas

$stmt = $pdo->query("
    SELECT c.id as chat_id, c.producto_id, c.cliente_id, u.nombre as cliente_nombre, p.nombre as producto_nombre
    FROM chats c
    JOIN Usuarios u ON c.cliente_id = u.id
    JOIN productos p ON c.producto_id = p.id
    ORDER BY c.creado_en DESC
");
$chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['chats' => $chats]);
?>