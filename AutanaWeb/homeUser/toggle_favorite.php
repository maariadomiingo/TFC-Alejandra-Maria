<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

if (!isset($_POST['producto_id'])) {
    echo json_encode(['error' => 'Producto no especificado']);
    exit;
}

$producto_id = (int)$_POST['producto_id'];
$usuario_id = $_SESSION['user_id'];

// Config DB
$host = 'localhost';
$dbname = 'autana';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar si ya está en favoritos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favoritos WHERE usuario_id = ? AND producto_id = ?");
    $stmt->execute([$usuario_id, $producto_id]);
    $existe = $stmt->fetchColumn();

    if ($existe) {
        // Si existe, eliminar favorito (toggle off)
        $stmt = $pdo->prepare("DELETE FROM favoritos WHERE usuario_id = ? AND producto_id = ?");
        $stmt->execute([$usuario_id, $producto_id]);
        echo json_encode(['status' => 'removed']);
    } else {
        // Si no existe, agregar favorito (toggle on)
        $stmt = $pdo->prepare("INSERT INTO favoritos (usuario_id, producto_id) VALUES (?, ?)");
        $stmt->execute([$usuario_id, $producto_id]);
        echo json_encode(['status' => 'added']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
