<?php
session_start(); // Iniciar sesión (si no está iniciada)

// Destruir todas las variables de sesión
$_SESSION = [];
session_unset();
session_destroy();

// (Opcional) Eliminar cookies relacionadas con la sesión
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirigir al login o a la página de inicio
header("Location: ../login/login.html");
exit;
?>
