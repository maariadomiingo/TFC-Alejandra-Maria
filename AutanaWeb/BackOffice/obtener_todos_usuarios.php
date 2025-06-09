<?php
require_once __DIR__ . '/../server/db_config.php';
header('Content-Type: application/json');

$stmt = $pdo->query("SELECT id, nombre, correo, creado_en FROM Usuarios ORDER BY creado_en DESC");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['usuarios' => $usuarios]);
?>