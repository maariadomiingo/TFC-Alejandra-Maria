<?php
session_start();

$isLoggedIn = isset($_SESSION['user_name']);
$username = $isLoggedIn ? $_SESSION['user_name'] : '';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.html');
    exit;
}

$usuario_id = $_SESSION['user_id'];


$host = 'localhost';
$dbname = 'autana';
$dbuser = 'root';
$dbpass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbuser, $dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener ids favoritos
    $stmt = $pdo->prepare("SELECT producto_id FROM favoritos WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $favoriteIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (count($favoriteIds) > 0) {
        // Obtener info productos favoritos
        $placeholders = implode(',', array_fill(0, count($favoriteIds), '?'));
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ($placeholders)");
        $stmt->execute($favoriteIds);
        $productosFavoritos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $productosFavoritos = [];
    }

} catch (PDOException $e) {
    die("Error DB: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Mis Favoritos</title>
  <link rel="stylesheet" href="../collection/collection.css" />
  <link rel="stylesheet" href="../home/index.css">
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
      <li><a href="../collection/collection.php" class="hover:text-black">Collection</a></li>
        <li><a href="../ourMission/ourMission.html" class="hover:text-black">Our Mission</a></li>
        <li><a href="../home/aboutUs.html" class="hover:text-black">Behind the threads</a></li>
    </div>
  </nav>

<h1>Mis Productos Favoritos</h1>

<?php if (empty($productosFavoritos)): ?>
  <p>No tienes productos favoritos.</p>
<?php else: ?>
  <div class="outfit-container">
    <?php foreach ($productosFavoritos as $producto): ?>
      <div class="outfit">
        <a href="../vistaProducto/producto.php?id=<?= $producto['id'] ?>">
          <img src="<?= htmlspecialchars($producto['imagen_url']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" />
          <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
          <p><?= htmlspecialchars($producto['descripcion']) ?></p>
          <span>$<?= htmlspecialchars($producto['precio']) ?></span>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
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
      <p>© Autana. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>
</body>
</html>
