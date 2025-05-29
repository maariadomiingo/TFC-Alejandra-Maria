<?php
session_start();
require_once __DIR__ . '/../server/db_config.php';

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartProducts = [];

if (!empty($cart)) {
    // Obtener productos de la BBDD
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
    $stmt->execute($cart);
    $cartProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode($cartProducts);
