<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];
    // Puedes manejar cantidades si lo deseas
    $_SESSION['cart'][] = $productId;
    header('Location: cart.html');
    exit;
}

header('Location: cart.html');
exit;
?>