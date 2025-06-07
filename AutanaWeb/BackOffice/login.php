<?php
session_start();
require_once __DIR__ . '/../server/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE correo = ?");
    $stmt->execute([$correo]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_nombre'] = $admin['nombre'];
        header("Location: homeBO.html");
        exit;
    } else {
        header("Location: login.html?error=1");
        exit;
    }
} else {
    header("Location: login.html");
    exit;
}
