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
         hombro DECIMAL(5,2) DEFAULT NULL,
        pecho DECIMAL(5,2) DEFAULT NULL,
        cintura DECIMAL(5,2) DEFAULT NULL,
        cadera DECIMAL(5,2) DEFAULT NULL,
        altura DECIMAL(5,2) DEFAULT NULL,    
        direccion_calle VARCHAR(100),
        direccion_ciudad VARCHAR(50),
        direccion_estado VARCHAR(50),
        direccion_codigo_postal VARCHAR(20),
        direccion_pais VARCHAR(50),
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
    -- TABLAS DE CHAT
    CREATE TABLE IF NOT EXISTS chats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        producto_id INT NOT NULL,
        cliente_id INT NOT NULL,
        creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
        FOREIGN KEY (cliente_id) REFERENCES Usuarios(id) ON DELETE CASCADE
    );
    CREATE TABLE IF NOT EXISTS mensajes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        chat_id INT NOT NULL,
        remitente_id INT NOT NULL,
        mensaje TEXT NOT NULL,
        enviado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        leido BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
        FOREIGN KEY (remitente_id) REFERENCES Usuarios(id) ON DELETE CASCADE
    );
    ";
    $pdo->exec($crearTablas);

    // 4. Insertar usuarios de prueba si no existen
    $usuariosRandom = [
        ['nombre' => 'Maria', 'correo' => 'domingopuentemaria@gmail.com', 'password' => '123456'],
        ['nombre' => 'Lucas Romero', 'correo' => 'lucas.romero@example.com', 'password' => 'pass1234'],
        ['nombre' => 'Sofía Torres', 'correo' => 'sofia.torres@example.com', 'password' => 'pass1234'],
        ['nombre' => 'Mateo García', 'correo' => 'mateo.garcia@example.com', 'password' => 'pass1234'],
        ['nombre' => 'Valentina Ruiz', 'correo' => 'valentina.ruiz@example.com', 'password' => 'pass1234'],
        ['nombre' => 'Diego Fernández', 'correo' => 'diego.fernandez@example.com', 'password' => 'pass1234'],
        ['nombre' => 'Martina López', 'correo' => 'martina.lopez@example.com', 'password' => 'pass1234'],
        ['nombre' => 'Daniel Pérez', 'correo' => 'daniel.perez@example.com', 'password' => 'pass1234'],
        ['nombre' => 'Emma Sánchez', 'correo' => 'emma.sanchez@example.com', 'password' => 'pass1234'],
        ['nombre' => 'Alejandro Castro', 'correo' => 'alejandro.castro@example.com', 'password' => 'pass1234'],
        ['nombre' => 'Camila Gómez', 'correo' => 'camila.gomez@example.com', 'password' => 'pass1234'],
    ];

    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM Usuarios WHERE correo = :correo");
    $insertStmt = $pdo->prepare("INSERT INTO Usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)");
    foreach ($usuariosRandom as $usuario) {
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
            'imagen_url' => '../img/vestido.jpg',
            'stripe_product_id' => 'prod_SOTnUnChSmNeCF',
            'stripe_price_id' => 'price_1RYZkQGh171OKFHVqVLVW1QL'
        ],
        [
            'nombre' => 'Collection 2 - Amazonas',
            'precio' => 150,
            'imagen_url' => '../img/outfit2.jpg',
            'stripe_product_id' => 'prod_SOtVrnssa3LOoi',
            'stripe_price_id' => 'price_1RYZkeGh171OKFHVEZH6D1DQ'
        ],
        [
            'nombre' => 'Collection 3 - Serene Earth',
            'precio' => 140,
            'imagen_url' => '../img/outfit3.jpg',
            'stripe_product_id' => 'prod_SOtyJdoQypITGL',
            'stripe_price_id' => 'price_1RYZkqGh171OKFHV9MExPKwe'
        ],
        [
            'nombre' => 'Collection 4 - Emerald Canopy',
            'precio' => 110,
            'imagen_url' => '../img/outfit4.jpg',
            'stripe_product_id' => 'prod_SOu0xHcpMEcP0x',
            'stripe_price_id' => 'price_1RYZlOGh171OKFHV7tRv19LJ'
        ],
        [
            'nombre' => 'Collection 5 - Ceiba Spirit',
            'precio' => 130,
            'imagen_url' => '../img/outfit5.webp',
            'stripe_product_id' => 'prod_SOu0hIve6FzxNT',
            'stripe_price_id' => 'price_1RYZlaGh171OKFHVAtyy7OOx'
        ],
        [
            'nombre' => 'Collection 6 - Tepui Waters',
            'precio' => 135,
            'imagen_url' => '../img/outfit3.avif',
            'stripe_product_id' => 'prod_SOu1LVbogt7374',
            'stripe_price_id' => 'price_1RYZlmGh171OKFHVm0MryFm6'
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

    $pdo->exec("
    CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nombre VARCHAR(100) NOT NULL,
        correo VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    );
    ");

    // Insertar admin por defecto si no existe
    $checkAdmin = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE correo = ?");
    $checkAdmin->execute(['admin@autana.com']);
    if ($checkAdmin->fetchColumn() == 0) {
        $hashed = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admins (nombre, correo, password) VALUES (?, ?, ?)")
            ->execute(['admin1', 'admin@autana.com', $hashed]);
    }

    // Asegura que la columna remitente_tipo existe en la tabla mensajes
    $colRemitenteTipo = $pdo->query("SHOW COLUMNS FROM mensajes LIKE 'remitente_tipo'")->fetch();
    if (!$colRemitenteTipo) {
        $pdo->exec("ALTER TABLE mensajes ADD COLUMN remitente_tipo VARCHAR(20) DEFAULT 'usuario'");
    }
} catch (PDOException $e) {
    // Lanza la excepción para que el archivo principal la capture y devuelva JSON
    throw $e;
}
