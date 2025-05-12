<?php
$host = 'localhost';
$dbname = 'autana';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear tabla si no existe
    $crearTabla = "
        CREATE TABLE IF NOT EXISTS Usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            correo VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ";
    $pdo->exec($crearTabla);

    // Usuarios de prueba
    $usuarios = [
        ['nombre' => 'maria', 'correo' => 'domingopuentemaria@gmail.com', 'password' => '123456'],
        ['nombre' => 'alejandra', 'correo' => 'ale@ejemplo.com', 'password' => '123456']
    ];

    $sql = "INSERT INTO Usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)";
    $stmt = $pdo->prepare($sql);

    foreach ($usuarios as $usuario) {
        // Verifica si ya existe el correo
        $check = $pdo->prepare("SELECT COUNT(*) FROM Usuarios WHERE correo = :correo");
        $check->execute([':correo' => $usuario['correo']]);
        $exists = $check->fetchColumn();

        if ($exists == 0) {
            $hashedPassword = password_hash($usuario['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                ':nombre' => $usuario['nombre'],
                ':correo' => $usuario['correo'],
                ':password' => $hashedPassword
            ]);
            echo "Usuario {$usuario['nombre']} insertado correctamente.<br>";
        } else {
            echo "El usuario con correo {$usuario['correo']} ya existe.<br>";
        }
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
