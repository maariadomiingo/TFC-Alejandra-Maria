<?php
require_once '../server/db_config.php';
session_start();

$isLoggedIn = isset($_SESSION['user_name']);
$username = $isLoggedIn ? $_SESSION['user_name'] : '';

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    die("Debes iniciar sesión para ver tus compras.");
}

// Consulta para obtener las preordenes del usuario con datos del producto
$stmt = $pdo->prepare("
    SELECT pr.talla, pr.cantidad, pr.estado, pr.fecha_preorden,
           p.nombre, p.descripcion, p.imagen_url
    FROM preorders pr
    JOIN productos p ON pr.product_id = p.id
    WHERE pr.usuario_id = ?
    ORDER BY pr.fecha_preorden DESC
");
$stmt->execute([$userId]);
$preorders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="../home/index.css">
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
      <a href="#" class="block text-gray-700 hover:text-black">Colection</a>
      <a href="#" class="block text-gray-700 hover:text-black">Our Mission</a>
      <a href="#" class="block text-gray-700 hover:text-black">Behind the threads</a>
    </div>
  </nav>
    <<h1 class="text-4xl font-extrabold text-gray-900 text-center mt-12 mb-8">
  My Purchases
</h1>


    <?php if (count($preorders) > 0): ?>
        <div class="productos-container">
            <?php foreach ($preorders as $item): ?>
                <div class="producto">
                    <img src="<?php echo htmlspecialchars($item['imagen_url']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" />
                    <h2><?php echo htmlspecialchars($item['nombre']); ?></h2>
                    <p><?php echo htmlspecialchars($item['descripcion']); ?></p>
                    <p><strong>Talla:</strong> <?php echo htmlspecialchars($item['talla']); ?></p>
                    <p><strong>Cantidad:</strong> <?php echo htmlspecialchars($item['cantidad']); ?></p>
                    <p><strong>Estado:</strong> <?php echo htmlspecialchars($item['estado']); ?></p>
                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($item['fecha_preorden']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="flex flex-col items-center justify-center mt-20 text-gray-600">
  <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.6 8h13.2M7 13l-4-8m16 0h-2m-4 16a2 2 0 100-4 2 2 0 000 4z" />
  </svg>
  <h2 class="text-xl font-semibold mb-2">Oops!</h2>
  <p class="text-center max-w-md">It looks like you haven’t placed any orders yet. Explore our collection and make your first purchase!</p>
  <a href="../collection/collection.php" class="mt-6 inline-block bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
    View Collection
  </a>
</div>

    <?php endif; ?>
</body>
</html>
