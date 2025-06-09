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
$stmt = $pdo->prepare("SELECT hombro, pecho, cintura, cadera, altura, 
                      direccion_calle, direccion_ciudad, direccion_estado, direccion_codigo_postal, direccion_pais 
                      FROM user_profiles WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Asignar valores para mostrar en el formulario
$nombre = $user['nombre'] ?? '';
$correo = $user['correo'] ?? '';
$hombro = $profile['hombro'] ?? '';
$pecho = $profile['pecho'] ?? '';
$cintura = $profile['cintura'] ?? '';
$cadera = $profile['cadera'] ?? '';
$altura = $profile['altura'] ?? '';
$direccion_calle = $profile['direccion_calle'] ?? '';
$direccion_ciudad = $profile['direccion_ciudad'] ?? '';
$direccion_estado = $profile['direccion_estado'] ?? '';
$direccion_codigo_postal = $profile['direccion_codigo_postal'] ?? '';
$direccion_pais = $profile['direccion_pais'] ?? '';
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
<div id="passwordModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center">
  <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-lg relative">
    <h2 class="text-lg font-semibold mb-4">Change Password</h2>
    
    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
    <input type="password" id="password" name="password" class="input mb-4" placeholder="Enter new password">

    <div class="flex justify-end space-x-2">
      <button type="button" onclick="toggleModal()" class="text-gray-500 hover:text-gray-700">Cancel</button>
      <button type="button" onclick="savePassword()" class="bg-black text-white px-4 py-2 rounded hover:bg-gray-800">Save</button>
    </div>
    <button onclick="toggleModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
      <i data-lucide="x" class="w-5 h-5"></i>
    </button>
  </div>
</div>
<?php if (isset($_GET['exito'])): ?>
  <div id="successPopup" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-30">
    <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-lg shadow-lg relative max-w-sm w-full">
      <strong class="font-semibold">Success!</strong>
      <span class="block mt-1">Your profile has been updated successfully.</span>
      <button onclick="closeSuccessPopup()" class="absolute top-2 right-2 text-green-700 hover:text-green-900">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>
  </div>
<?php endif; ?>
<div id="currentPasswordModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
  <div class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
    <h2 class="text-lg font-semibold mb-4">Confirma tu contraseña</h2>
    <input
      type="password"
      id="current_password_input"
      placeholder="Contraseña actual"
      class="w-full border p-2 rounded mb-4"
    />
    <div class="flex justify-end space-x-2">
      <button onclick="closeCurrentPasswordModal()" class="px-4 py-2 bg-gray-300 rounded">Cancelar</button>
      <button onclick="validateCurrentPassword()" class="px-4 py-2 bg-blue-500 text-white rounded">Confirmar</button>
    </div>
    <p id="currentPasswordError" class="text-red-500 text-sm mt-2 hidden">Contraseña incorrecta.</p>
  </div>
</div>

<div class="bg-white">

  <!-- Navbar -->
  <nav class="px-6 py-4 shadow-md">
    <div class="flex items-center justify-between">
      <!-- Logo -->
       <a href="../homeUser/home.php">
       <img src="../img/logoSinFondo.png" alt="" class="logo"> 
       </a>

      <!-- Desktop Menu -->
      <ul class="hidden md:flex space-x-6 text-gray-700 font-medium">
        <li><a href="../collection/collection.php" class="hover:text-black">Collection</a></li>
        <li><a href="../ourMission/ourMission.html" class="hover:text-black">Our Mission</a></li>
        <li><a href="../home/aboutUs.html" class="hover:text-black">Behind the threads</a></li>
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
      <a href="../homeUser/favoritos.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Favorites</a>
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
      <a href="../collection/collection.php" class="block text-gray-700 hover:text-black">Collection</a>
      <a href="../ourMission/ourMission.html" class="block text-gray-700 hover:text-black">Our Mission</a>
      <a href="../home/aboutUs.html" class="block text-gray-700 hover:text-black">Behind the threads</a>
    </div>
  </nav>

  <!-- Edit Profile Section -->
  <section class="py-6 px-4">
 
  <?php if (isset($_GET['error'])): ?>
  <div class="bg-red-100 text-red-700 px-4 py-2 rounded-md mb-4">
    <?php echo htmlspecialchars($_GET['error']); ?>
  </div>
<?php elseif (isset($_GET['exito'])): ?>
  <div class="bg-green-100 text-green-700 px-4 py-2 rounded-md mb-4">
    Perfil actualizado con éxito.
  </div>
<?php endif; ?>

  <!-- Profile update form -->
  <form method="POST" action="procesar-editar.php" class="space-y-6 form-container">
  <h1 class="text-xl font-semibold">Edit Profile</h1>

  <div class="form-group">
    <label for="correo" class="label">Email Address</label>
    <input type="email" id="correo" name="correo" class="input" value="<?php echo htmlspecialchars($correo); ?>">
    <p class="info-text">If you change your email, confirmation will be required via email.</p>
  </div>

 <div class="form-group">
  <label class="label">Password</label>
  <button type="button" onclick="toggleModal()" class="bg-gray-100 px-4 py-2 rounded hover:bg-gray-200">
    Change Password
  </button>
</div>


  <!-- Sección de medidas corporales -->
<div class="form-group">
  <h3 class="subtitle">Body Measurements (cm)</h3>
  <div class="grid grid-cols-2 gap-4">
    <div>
      <label for="hombro" class="label">Shoulder</label>
      <input type="number" id="hombro" name="hombro" class="input" value="<?php echo htmlspecialchars($profile['hombro'] ?? ''); ?>">
    </div>
    <div>
      <label for="pecho" class="label">Chest</label>
      <input type="number" id="pecho" name="pecho" class="input" value="<?php echo htmlspecialchars($profile['pecho'] ?? ''); ?>">
    </div>
    <div>
      <label for="cintura" class="label">Waist</label>
      <input type="number" id="cintura" name="cintura" class="input" value="<?php echo htmlspecialchars($profile['cintura'] ?? ''); ?>">
    </div>
    <div>
      <label for="cadera" class="label">Hip</label>
      <input type="number" id="cadera" name="cadera" class="input" value="<?php echo htmlspecialchars($profile['cadera'] ?? ''); ?>">
    </div>
    <div>
      <label for="altura" class="label">Height</label>
      <input type="number" id="altura" name="altura" class="input" value="<?php echo htmlspecialchars($profile['altura'] ?? ''); ?>">
    </div>
  </div>
  <button type="button" onclick="calcularTallas()" class="btn-secondary mt-2">Calculate Recommended Sizes</button>
</div>

<!-- Sección de dirección dividida -->
<div class="form-group">
  <h3 class="subtitle">Shipping Address</h3>
  <div class="grid grid-cols-1 gap-4">
    <div>
      <label for="direccion_calle" class="label">Street Address</label>
      <input type="text" id="direccion_calle" name="direccion_calle" class="input" value="<?php echo htmlspecialchars($profile['direccion_calle'] ?? ''); ?>">
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label for="direccion_ciudad" class="label">City</label>
        <input type="text" id="direccion_ciudad" name="direccion_ciudad" class="input" value="<?php echo htmlspecialchars($profile['direccion_ciudad'] ?? ''); ?>">
      </div>
      <div>
        <label for="direccion_estado" class="label">State/Province</label>
        <input type="text" id="direccion_estado" name="direccion_estado" class="input" value="<?php echo htmlspecialchars($profile['direccion_estado'] ?? ''); ?>">
      </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label for="direccion_codigo_postal" class="label">Postal Code</label>
        <input type="text" id="direccion_codigo_postal" name="direccion_codigo_postal" class="input" value="<?php echo htmlspecialchars($profile['direccion_codigo_postal'] ?? ''); ?>">
      </div>
      <div>
        <label for="direccion_pais" class="label">Country</label>
        <select id="direccion_pais" name="direccion_pais" class="input">
          <option value="">Select Country</option>
          <option value="US" <?php echo (isset($profile['direccion_pais']) && $profile['direccion_pais'] == 'US') ? 'selected' : ''; ?>>United States</option>
          <option value="CA" <?php echo (isset($profile['direccion_pais']) && $profile['direccion_pais'] == 'CA') ? 'selected' : ''; ?>>Canada</option>
          <!-- Más opciones de países -->
        </select>
      </div>
    </div>
  </div>

 
  <input type="hidden" id="hidden_password" name="password">
  <input type="hidden" name="current_password" id="hidden_current_password">

<button onclick="validateCurrentPassword()" type="submit" class="btn-submit">Update Profile</button>

</form>



    
  <script src="../homeUser/editar.js"></script>
<footer class="bg-white py-8 border-t border-gray-200">
  <div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between">
      <!-- Logo -->
      <div class="mb-6 md:mb-0">
        <img src="../img/logoSinFondo.png" alt="Logo" class="h-12">
      </div>

      <!-- Redes sociales -->
      <div class="flex space-x-6">
        <a href="#" class="text-gray-600 hover:text-black transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/>
          </svg>
        </a>
        <a href="#" class="text-gray-600 hover:text-black transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
          </svg>
        </a>
        <a href="#" class="text-gray-600 hover:text-black transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
          </svg>
        </a>
      </div>
    </div>

    <!-- Derechos de autor -->
    <div class="mt-8 text-center text-gray-500 text-sm">
        <a href="../home/legal.html"><p >© Autana. Todos los derechos reservados.</p></a>
    </div>
  </div>
</footer>
</body>
</html>
