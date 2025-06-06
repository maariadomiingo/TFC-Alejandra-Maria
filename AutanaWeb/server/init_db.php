<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'autana';

try {
    // 1. Crear base de datos si no existe
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // 2. Conectar a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. Crear tablas (ahora productos tiene los nuevos campos)
    $crearTablas = "
    CREATE TABLE IF NOT EXISTS Usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        correo VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        es_admin BOOLEAN DEFAULT FALSE,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS user_profiles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        talla_top VARCHAR(10),
        talla_bottom VARCHAR(10),    
        direccion_envio TEXT,
        FOREIGN KEY (usuario_id) REFERENCES Usuarios(id) ON DELETE CASCADE
    );
    CREATE TABLE IF NOT EXISTS productos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        precio DECIMAL(10,2) NOT NULL,
        descripcion TEXT,
        imagen_url TEXT,
        stripe_product_id VARCHAR(255),
        stripe_price_id VARCHAR(255),
        disponible BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    CREATE TABLE IF NOT EXISTS preorders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        product_id INT NOT NULL,
        talla VARCHAR(10),
        cantidad INT DEFAULT 1,
        estado VARCHAR(50) DEFAULT 'pendiente',
        fecha_preorden TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES Usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES productos(id) ON DELETE CASCADE
    );
    CREATE TABLE IF NOT EXISTS favoritos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        producto_id INT NOT NULL,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(usuario_id, producto_id),
        FOREIGN KEY (usuario_id) REFERENCES Usuarios(id) ON DELETE CASCADE,
        FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE
    );
    ";
    $pdo->exec($crearTablas);

    // 4. Insertar usuarios de prueba si no existen
    $usuarios = [
        ['nombre' => 'maria', 'correo' => 'domingopuentemaria@gmail.com', 'password' => '123456'],
        ['nombre' => 'alejandra', 'correo' => 'ale@ejemplo.com', 'password' => '123456']
    ];
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM Usuarios WHERE correo = :correo");
    $insertStmt = $pdo->prepare("INSERT INTO Usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)");
    foreach ($usuarios as $usuario) {
        $checkStmt->execute([':correo' => $usuario['correo']]);
        $exists = $checkStmt->fetchColumn();
        if ($exists == 0) {
            $hashedPassword = password_hash($usuario['password'], PASSWORD_DEFAULT);
            $insertStmt->execute([
                ':nombre' => $usuario['nombre'],
                ':correo' => $usuario['correo'],
                ':password' => $hashedPassword
            ]);
        }
    }

    // 5. Insertar productos outfit1...outfit6 si no existen
    $products = [
        [
            'nombre' => 'Collection 1 - Mamey Wood',
            'precio' => 120,
            'imagen_url' => '../img/outfit1.jpg',
            'stripe_product_id' => 'prod_SOTnUnChSmNeCF',
            'stripe_price_id' => 'price_1RTgpqGh171OKFHVAbr3OP5x'
        ],
        [
            'nombre' => 'Collection 2 - Amazonas',
            'precio' => 150,
            'imagen_url' => '../img/outfit2.jpg',
            'stripe_product_id' => 'prod_SOtVrnssa3LOoi',
            'stripe_price_id' => 'price_1RU5iXGh171OKFHVvjSFRfWL'
        ],
        [
            'nombre' => 'Collection 3 - Serene Earth',
            'precio' => 140,
            'imagen_url' => '../img/outfit3.jpg',
            'stripe_product_id' => 'prod_SOtyJdoQypITGL',
            'stripe_price_id' => 'price_1RU6AlGh171OKFHVfmGHgjq6'
        ],
        [
            'nombre' => 'Collection 4 - Emerald Canopy',
            'precio' => 110,
            'imagen_url' => '../img/outfit4.jpg',
            'stripe_product_id' => 'prod_SOu0xHcpMEcP0x',
            'stripe_price_id' => 'price_1RU6CAGh171OKFHVzVtsz851'
        ],
        [
            'nombre' => 'Collection 5 - Ceiba Spirit',
            'precio' => 130,
            'imagen_url' => '../img/outfit5.webp',
            'stripe_product_id' => 'prod_SOu0hIve6FzxNT',
            'stripe_price_id' => 'price_1RU6CnGh171OKFHVGlhcVxFS'
        ],
        [
            'nombre' => 'Collection 6 - Tepui Waters',
            'precio' => 135,
            'imagen_url' => '../img/outfit3.avif',
            'stripe_product_id' => 'prod_SOu1LVbogt7374',
            'stripe_price_id' => 'price_1RU6DKGh171OKFHVSNQmAdFy'
        ],
    ];

    $checkProd = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE stripe_product_id = :stripe_product_id");
    $insertProd = $pdo->prepare("INSERT INTO productos (nombre, precio, imagen_url, stripe_product_id, stripe_price_id) VALUES (:nombre, :precio, :imagen_url, :stripe_product_id, :stripe_price_id)");
    foreach ($products as $producto) {
        $checkProd->execute([':stripe_product_id' => $producto['stripe_product_id']]);
        $exists = $checkProd->fetchColumn();
        if ($exists == 0) {
            $insertProd->execute([
                ':nombre' => $producto['nombre'],
                ':precio' => $producto['precio'],
                ':imagen_url' => $producto['imagen_url'],
                ':stripe_product_id' => $producto['stripe_product_id'],
                ':stripe_price_id' => $producto['stripe_price_id']
            ]);
        }
    }
} catch (PDOException $e) {
    // Lanza la excepción para que el archivo principal la capture y devuelva JSON
    throw $e;
}
