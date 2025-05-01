<?php
$host = 'localhost';
$dbname = 'autana';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    //usuarios de prueba
    $usuarios = [
        ['nombre' => 'maria', 'correo' => 'domingopuentemaria@gmail.com', 'password' => '123456'],
        ['nombre' => 'alejandra', 'correo' => 'ale@ejemplo.com', 'password' => '123456']
    ];

    $sql = "INSERT INTO Usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)";
    $stmt = $pdo->prepare($sql);

    foreach ($usuarios as $usuario) {
        $hashedPassword = password_hash($usuario['password'], PASSWORD_DEFAULT);

        $stmt->execute([
            ':nombre' => $usuario['nombre'],
            ':correo' => $usuario['correo'],
            ':password' => $hashedPassword
        ]);
    }

    echo "Usuarios de prueba insertados correctamente.";

} catch (PDOException $e) {
    die("Error al insertar usuarios: " . $e->getMessage());
}
?>
