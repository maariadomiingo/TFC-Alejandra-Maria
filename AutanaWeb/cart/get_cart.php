<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../server/db_config.php';
require_once __DIR__ . '/../stripeCart/vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51RSxu8Gh171OKFHVlvRsoJhQidKNxbepMXZtguoBchlZjwpjpiRUDRRvXHDZegvmsxfDJW6qsO1Ljbivo67HaydL008YcTuFyC');

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$cartProducts = [];

if (!empty($cart)) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
    $stmt->execute($cart);
    $cartProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cartProducts as &$product) {
        try {
            $stripePrice = \Stripe\Price::retrieve($product['stripe_price_id']);
            $product['precio_stripe'] = number_format($stripePrice->unit_amount / 100, 2, '.', '');
            $product['moneda_stripe'] = strtoupper($stripePrice->currency);
        } catch (Exception $e) {
            $product['precio_stripe'] = $product['precio'];
            $product['moneda_stripe'] = 'USD';
        }
    }
}

// Siempre devuelve JSON, aunque esté vacío
header('Content-Type: application/json');

// Log para depuración en el log de errores de PHP
error_log('Productos enviados al carrito: ' . print_r($cartProducts, true));
error_log('Contenido del carrito recibido: ' . print_r($cart, true));

echo json_encode($cartProducts);
