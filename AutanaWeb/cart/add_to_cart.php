<?php
session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $productId = (int)$_POST['product_id'];
    $_SESSION['cart'][] = $productId;
    error_log('Carrito tras añadir: ' . print_r($_SESSION['cart'], true)); // <-- Añade esto
    header('Location: cart.html');
    exit;
}

header('Location: cart.html');
exit;
?>