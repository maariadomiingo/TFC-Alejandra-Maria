<?php
require_once '../server/db_config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}

$usuario_id = $_SESSION['user_id']; // <- Esto es lo que falta
$isLoggedIn = isset($_SESSION['user_name']);
$username = $isLoggedIn ? $_SESSION['user_name'] : '';

// Obtener datos del usuario desde la tabla Usuarios
$stmt = $pdo->prepare("SELECT nombre, correo FROM Usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener datos del perfil desde user_profiles
$stmt = $pdo->prepare("SELECT talla_top, talla_bottom, direccion_envio FROM user_profiles WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Asignar valores para mostrar en el formulario
$nombre = $user['nombre'] ?? '';
$correo = $user['correo'] ?? '';
$talla_top = $profile['talla_top'] ?? '';
$talla_bottom = $profile['talla_bottom'] ?? '';
$direccion_envio = $profile['direccion_envio'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="../home/index.css">
  <link rel="stylesheet" href="editar.css">
  <title>Editar Perfil</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    window.onload = () => {
      lucide.createIcons();
      // Toggle mobile menu
      const toggle = document.getElementById('menu-toggle');
      const menu = document.getElementById('mobile-menu');
      toggle.addEventListener('click', () => {
        menu.classList.toggle('hidden');
      });
    };
  </script>
</head>
<body class="bg-white">

  <!-- Navbar -->
  <nav class="px-6 py-4 shadow-md">
    <div class="flex items-center justify-between">
      <!-- Logo -->
      <img src="../img/logoSinFondo.png" alt="Logo" class="logo">

      <!-- Desktop Menu -->
      <ul class="hidden md:flex space-x-6 text-gray-700 font-medium">
        <li><a href="#" class="hover:text-black">Collection</a></li>
        <li><a href="#" class="hover:text-black">Our Mission</a></li>
        <li><a href="#" class="hover:text-black">Behind the threads</a></li>
      </ul>

      <!-- Icons -->
      <div class="flex items-center space-x-4">
        <?php if ($isLoggedIn): ?>
        <div class="relative">
          <button class="flex items-center space-x-2 text-gray-700 hover:text-black">
            <i data-lucide="user" class="w-6 h-6"></i>
            <span><?php echo htmlspecialchars($username); ?></span>
          </button>
          
          <!-- Menú desplegable visible siempre -->
          <div x-show="open" @click.away="open = false" x-transition
         class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
      <a href="../homeUser/editar-perfil.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Edit Profile</a>
      <a href="../homeUser/compras.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Purchases</a>
      <a href="/favoritos.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Favorites</a>
      <a href="../homeUser/logout.php" class="block px-4 py-2 text-red-500 hover:bg-gray-100">Log Out</a>
    </div>
        </div>
        <?php else: ?>
        <button aria-label="Login">
          <a href="../login/login.html" aria-label="Login">
            <i data-lucide="user" class="w-6 h-6 text-gray-700 hover:text-black"></i>
          </a>
        </button>
        <?php endif; ?>

        <button aria-label="Cart" onclick="goToCart()">
          <i data-lucide="shopping-cart" class="w-6 h-6 text-gray-700 hover:text-black"></i>
        </button>

        <!-- Mobile Hamburger -->
        <button id="menu-toggle" class="md:hidden">
          <i data-lucide="menu" class="w-6 h-6 text-gray-700"></i>
        </button>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="mt-4 hidden md:hidden space-y-2">
      <a href="#" class="block text-gray-700 hover:text-black">Collection</a>
      <a href="#" class="block text-gray-700 hover:text-black">Our Mission</a>
      <a href="#" class="block text-gray-700 hover:text-black">Behind the threads</a>
    </div>
  </nav>

  <!-- Edit Profile Section -->
  <section class="py-6 px-4">
 
  
  <!-- Profile update form -->
  <form method="POST" action="procesar-editar.php" class="space-y-6 form-container">
 <h1 class="text-xl font-semibold">Edit Profile</h1>
    <div class="form-group">
      <label for="correo" class="label">Email Address</label>
      <input type="email" id="correo" name="correo" class="input" value="<?php echo htmlspecialchars($correo); ?>">
      <p class="info-text">If you change your email, confirmation will be required via email.</p>
    </div>

    <div class="form-group">
      <label for="password" class="label">New Password</label>
      <input type="password" id="password" name="password" class="input" placeholder="Leave blank if you don't want to change it">
    </div>

    <div class="form-group">
      <label for="talla_top" class="label">Top Size</label>
      <input type="text" id="talla_top" name="talla_top" required class="input" value="<?php echo htmlspecialchars($talla_top); ?>">
    </div>

    <div class="form-group">
      <label for="talla_bottom" class="label">Bottom Size</label>
      <input type="text" id="talla_bottom" name="talla_bottom" required class="input" value="<?php echo htmlspecialchars($talla_bottom); ?>">
    </div>

    <div class="form-group">
      <label for="direccion_envio" class="label">Shipping Address</label>
      <textarea id="direccion_envio" name="direccion_envio" required class="textarea"><?php echo htmlspecialchars($direccion_envio); ?></textarea>
    </div>

    <button type="submit" class="btn-submit">Update Profile</button>
  </form>
</section>


    
  <script src="../homeUser/editar.js"></script>

</body>
</html>
