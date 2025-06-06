<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método no permitido";
    exit;
}

file_put_contents(__DIR__ . '/debug_token.txt', print_r($_POST, true));

if (!isset($_POST['credential'])) {
    http_response_code(400);
    echo "No se recibió el token de Google.";
    exit;
}

$token = $_POST['credential'];
$client = new Google_Client(['client_id' => '912708635445-gmn0t83ot491rv8hhbh19vojs1pek191.apps.googleusercontent.com']);
$payload = $client->verifyIdToken($token);

if ($payload) {
    $email = $payload['email'];
    $name = $payload['name'];

    // Conexión a la base de datos
    require_once __DIR__ . '/../server/db_config.php';

    // Buscar usuario en la BBDD
    $stmt = $pdo->prepare("SELECT * FROM Usuarios WHERE correo = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        // Si no existe, lo creas (sin password)
        $stmt = $pdo->prepare("INSERT INTO Usuarios (nombre, correo, password) VALUES (?, ?, '')");
        $stmt->execute([$name, $email]);
        $userId = $pdo->lastInsertId();
    } else {
        $userId = $usuario['id'];
        $name = $usuario['nombre'];
    }

    // Iniciar sesión
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    // Redirigir al home de usuario
    header("Location: ../homeUser/home.php");
    exit;
} else {
    http_response_code(401);
    echo "Token inválido o error de red.";
}
?>
<div
    id="g_id_onload"
    data-client_id="912708635445-gmn0t83ot491rv8hhbh19vojs1pek191.apps.googleusercontent.com"
    data-login_uri="http://localhost/TFC/TFC-Alejandra-Maria/AutanaWeb/login/google-login.php"
    data-auto_prompt="false">
</div>
</div>