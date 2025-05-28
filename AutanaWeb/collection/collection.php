<?php
session_start();
$isLoggedIn = isset($_SESSION['user_name']);
$username = $isLoggedIn ? $_SESSION['user_name'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="../home/index.css">
  <link rel="stylesheet" href="../collection/collection.css" />
  <!-- <link rel="stylesheet" href="../css/footer.css"> -->
  <title>Responsive Navbar</title>
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
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white">

  <nav class="px-6 py-4 shadow-md">
    <div class="flex items-center justify-between">
      <!-- Logo -->
       <img src="../img/logoSinFondo.png" alt="" class="logo">

      <!-- Desktop Menu -->
      <ul class="hidden md:flex space-x-6 text-gray-700 font-medium">
        <li><a href="#" class="hover:text-black">Collection</a></li>
        <li><a href="#" class="hover:text-black">Our Mission</a></li>
        <li><a href="#" class="hover:text-black">Behind the threads</a></li>
      </ul>

      <!-- Icons -->
      <div class="flex items-center space-x-4">
    <?php if ($isLoggedIn): ?>
  <div class="relative" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center space-x-2 text-gray-700 hover:text-black">
      <i data-lucide="user" class="w-6 h-6"></i>
      <span><?php echo htmlspecialchars($username); ?></span>
    </button>

    <!-- Dropdown menu (visible only when 'open' is true) -->
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
      <a href="#" class="block text-gray-700 hover:text-black">Colection</a>
      <a href="#" class="block text-gray-700 hover:text-black">Our Mission</a>
      <a href="#" class="block text-gray-700 hover:text-black">Behind the threads</a>
    </div>
  </nav>

  <section class="carrusel-collection">
  <h1 class="titulo">READY-TO-WEAR</h1>

  <div class="outfit-container">
  <a href="../vistaProducto/producto.php?id=1" class="outfit" data-product-id="1">
    <img src="../img/outfit1.png" alt="Outfit 1">
    Nombre de la prenda 1 <br>
    Descripción 1 <br>
    <span>$precio1</span>
    <button class="favorite-btn" aria-label="Agregar a favoritos">♡</button>
  </a>

  <a href="../vistaProducto/producto.php?id=2" class="outfit" data-product-id="2">
    <img src="../img/outfit2.png" alt="Outfit 2">
    Nombre de la prenda 2 <br>
    Descripción 2 <br>
    <span>$precio2</span>
    <button class="favorite-btn" aria-label="Agregar a favoritos">♡</button>
  </a>

  <a href="../vistaProducto/producto.php?id=3" class="outfit" data-product-id="3">
    <img src="../img/outfit3.avif" alt="Outfit 3">
    Nombre de la prenda 3 <br>
    Descripción 3 <br>
    <span>$precio3</span>
    <button class="favorite-btn" aria-label="Agregar a favoritos">♡</button>
  </a>

  <a href="../vistaProducto/producto.php?id=4" class="outfit" data-product-id="4">
    <img src="../img/outfit3.avif" alt="Outfit 4">
    Nombre de la prenda 3 <br>
    Descripción 3 <br>
    <span>$precio3</span>
    <button class="favorite-btn" aria-label="Agregar a favoritos">♡</button>
  </a>

  <a href="../vistaProducto/producto.php?id=5" class="outfit" data-product-id="5">
    <img src="../img/outfit3.avif" alt="Outfit 5">
    Nombre de la prenda 3 <br>
    Descripción 3 <br>
    <span>$precio3</span>
    <button class="favorite-btn" aria-label="Agregar a favoritos">♡</button>
  </a>

  <a href="../vistaProducto/producto.php?id=6" class="outfit" data-product-id="6">
    <img src="../img/outfit3.avif" alt="Outfit 6">
    Nombre de la prenda 3 <br>
    Descripción 3 <br>
    <span>$precio3</span>
    <button class="favorite-btn" aria-label="Agregar a favoritos">♡</button>
  </a>

  <!-- continúa igual para los demás -->
</div>

</section>
</body>

</html>