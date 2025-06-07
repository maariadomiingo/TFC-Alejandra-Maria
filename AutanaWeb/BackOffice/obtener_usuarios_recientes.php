<?php
require_once __DIR__ . '/../server/db_config.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT nombre FROM Usuarios ORDER BY creado_en DESC LIMIT 5");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['usuarios' => $usuarios]);
