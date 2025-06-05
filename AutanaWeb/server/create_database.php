<?php
require_once 'db_config.php';

try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS autana CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Base de datos creada correctamente.";
} catch (PDOException $e) {
    die("Error al crear la base de datos: " . $e->getMessage());
}
?>
