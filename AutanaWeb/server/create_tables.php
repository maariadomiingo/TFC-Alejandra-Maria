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
            es_admin BOOLEAN DEFAULT FALSE,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS user_profiles (
    id SERIAL PRIMARY KEY,
    usuario_id INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    talla_top VARCHAR(10),
    talla_bottom VARCHAR(10),    
    direccion_envio TEXT
);

CREATE TABLE IF NOT EXISTS productos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen_url TEXT,
    disponible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS preorders (
    id SERIAL PRIMARY KEY,
    usuario_id INT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    product_id INT NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
    talla VARCHAR(10),
    cantidad INT DEFAULT 1,
    estado VARCHAR(50) DEFAULT 'pendiente',
    fecha_preorden TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
