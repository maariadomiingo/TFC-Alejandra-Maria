<?php
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$productId = isset($data['product_id']) ? (int)$data['product_id'] : 0;

if (isset($_SESSION['cart']) && $productId > 0) {
    // Fuerza todos los IDs a int para evitar problemas de tipo
    $_SESSION['cart'] = array_map('intval', $_SESSION['cart']);
    $key = array_search($productId, $_SESSION['cart']);
    if ($key !== false) {
        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindexa
    }
}

echo json_encode(['success' => true]);