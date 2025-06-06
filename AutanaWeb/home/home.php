<?php
// Conexión a la base de datos
$host = 'localhost';
$dbname = 'autana';
$dbuser = 'root';
$dbpass = '';
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener los 3 primeros productos
$stmt = $pdo->query("SELECT id, nombre, precio, imagen_url FROM productos LIMIT 3");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los datos como JSON
header('Content-Type: application/json');
echo json_encode($productos);
?>