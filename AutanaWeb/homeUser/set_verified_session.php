<?php
require_once '../server/db_config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$_SESSION['password_verified'] = true;
echo json_encode(['success' => true]);
?>