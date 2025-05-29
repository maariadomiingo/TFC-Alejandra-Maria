<?php
session_start();

header('Content-Type: application/json');

$favoritos = [];

if (isset($_SESSION['user_id'])) {
    $usuario_id = $_SESSION['user_id'];

    $mysqli = new mysqli("localhost", "root", "", "Autana");

    if ($mysqli->connect_error) {
        echo json_encode(['error' => 'DB connection failed']);
        exit;
    }

    $sql = "SELECT producto_id FROM favoritos WHERE usuario_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $favoritos[] = $row['producto_id'];
    }

    $stmt->close();
    $mysqli->close();
}

echo json_encode($favoritos);
