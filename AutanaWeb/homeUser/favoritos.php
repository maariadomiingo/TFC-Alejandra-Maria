<?php

require_once '../server/db_config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}

$usuario_id = $_SESSION['user_id'];

$mysqli = new mysqli($host, $user, $pass, $dbname);
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Consulta para obtener productos favoritos con datos completos
$sql = "
    SELECT p.id, p.nombre, p.descripcion, p.imagen_url, p.created_at
    FROM productos p
    JOIN favoritos f ON p.id = f.producto_id
    WHERE f.usuario_id = ?
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$favoritos = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$mysqli->close();

?>
