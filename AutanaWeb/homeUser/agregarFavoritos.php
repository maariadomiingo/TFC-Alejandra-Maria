<?php
// add_favorite.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}

require_once '../server/db_config.php';

$usuario_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

    if ($product_id > 0) {
        $mysqli = new mysqli($host, $user, $pass, $dbname);
        if ($mysqli->connect_error) {
            die("Error de conexión: " . $mysqli->connect_error);
        }

        // Insertar favorito, evitando duplicados con ON DUPLICATE KEY o IGNORE
        $sql = "INSERT IGNORE INTO favoritos (usuario_id, producto_id) VALUES (?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $product_id);
        $stmt->execute();

        $stmt->close();
        $mysqli->close();

        // Redirigir a la página del producto o favoritos
        header("Location: product.php?id=" . $product_id);
        exit;
    } else {
        echo "ID de producto inválido.";
    }
} else {
    header('Location: ../collection/collection.php');
    exit;
}
?>
