<?php
// product.php
session_start();
require_once __DIR__ . '/../server/db_config.php';

$isLoggedIn = isset($_SESSION['user_name']);
$username = $isLoggedIn ? $_SESSION['user_name'] : '';

// Get product ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: ../collection/collection.php");
  exit;
}

$productId = (int)$_GET['id'];

// Local array ONLY for extra fields
$productsExtra = [
  1 => [
    'description' => 'This dress reflects the artisanal essence of indigenous communities, crafted with ancestral techniques and handmade details.',
    'inspiration' => 'Inspired by the indigenous communities of Venezuela, this garment honors their cultural legacy and the colors of their natural environment.',
    'material' => 'Organic cotton dyed with natural pigments extracted from local plants.',
    'work_hours' => '30 hours of dedicated artisan work for each piece.',
    'story' => 'María, an artisan from the community, preserves the ancestral culture passed down by her ancestors in every stitch of this dress.'
  ],
  2 => [
    'description' => 'Handmade blouse with embroideries representing traditional symbols, perfect for those who value authenticity and history.',
    'inspiration' => 'Inspired by the fabrics and patterns of Venezuelan ethnic groups, connecting past and present.',
    'material' => 'Linen and cotton combined with natural dyes.',
    'work_hours' => '25 hours of meticulous embroidery and sewing.',
    'story' => 'This blouse carries the story of Ana, who learned the art of embroidery through generations in her indigenous family.'
  ],
  3 => [
    'description' => 'Comfortable and durable pants, made with ancestral textile techniques, ideal for daily wear with unique style.',
    'inspiration' => 'Designed in honor of communities that value functionality without losing their cultural identity.',
    'material' => 'Recycled cotton fabric with natural dyes.',
    'work_hours' => '28 hours dedicated to weaving and artisanal finishing.',
    'story' => 'These pants tell the story of José, a weaver committed to sustainability and traditional art.'
  ],
  4 => [
    'description' => 'Hand-knitted vest with natural fibers, featuring a design that represents local flora and fauna.',
    'inspiration' => 'Inspired by the patterns and colors found in Venezuelan biodiversity.',
    'material' => 'Cotton and natural wool fibers.',
    'work_hours' => '22 hours of manual work and delicate knitting.',
    'story' => 'Every thread carries the dedication of Carmen, an artisan who blends tradition and nature in her work.'
  ],
  5 => [
    'description' => 'Pleated skirt with embroidered details evoking indigenous stories and legends.',
    'inspiration' => 'Inspired by traditional Venezuelan dances and festivities.',
    'material' => 'Soft cotton with handmade embroideries.',
    'work_hours' => '26 hours dedicated to pleating and embroidery.',
    'story' => 'This skirt is a tribute to Lucía, a young artisan who expresses her roots through textile art.'
  ],
  6 => [
    'description' => 'Fresh and elegant shirt, made with natural linen and artisanal finishes.',
    'inspiration' => 'Inspired by the lifestyle of indigenous communities living in harmony with nature.',
    'material' => '100% natural linen and organic dyes.',
    'work_hours' => '24 hours of artisanal craftsmanship.',
    'story' => 'This shirt reflects Pedro\'s passion for keeping ancestral techniques alive and his commitment to the environment.'
  ],
];

// Get name, price and image from DB
$stmt = $pdo->prepare("SELECT nombre, precio, imagen_url FROM productos WHERE id = ?");
$stmt->execute([$productId]);
$productFromDB = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$productFromDB || !isset($productsExtra[$productId])) {
  echo "<h2>Product not found.</h2>";
  exit;
}

// Separate title and subtitle
$parts = explode('-', $productFromDB['nombre'], 2);
$title = trim($parts[0]);
$subtitle = isset($parts[1]) ? trim($parts[1]) : '';
$extra = $productsExtra[$productId];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../home/index.css">
  <link rel="stylesheet" href="../vistaProducto/producto.css" />
  <title><?php echo htmlspecialchars($title); ?></title>
  <script>
    const PRODUCT_ID = <?php echo json_encode($productId); ?>;
  </script>
  <script src="producto.js"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script>
    window.onload = () => {
      lucide.createIcons();
      // Toggle mobile menu
      const toggle = document.getElementById("menu-toggle");
      const menu = document.getElementById("mobile-menu");
      toggle.addEventListener("click", () => {
        menu.classList.toggle("hidden");
      });
    };
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">
  <nav class="px-6 py-4 shadow-sm bg-white">
    <div class="flex items-center justify-between">
      <!-- Logo -->
      <img src="../img/logoSinFondo.png" alt="Autana Logo" class="h-12" />

      <!-- Desktop Menu -->
      <ul class="hidden md:flex space-x-6 text-gray-700 font-medium">
      <li><a href="../collection/collection.php" class="hover:text-black">Collection</a></li>
        <li><a href="../ourMission/ourMission.html" class="hover:text-black">Our Mission</a></li>
        <li><a href="../home/aboutUs.html" class="hover:text-black">Behind the threads</a></li>
      </ul>

      <!-- Icons -->
      <div class="flex items-center space-x-4">
        <button aria-label="Login">
          <a href="../login/login.html" aria-label="Login">
            <i data-lucide="user" class="w-6 h-6 text-gray-700 hover:text-primary-color transition-colors"></i>
          </a>
        </button>
        <button aria-label="Cart" onclick="goToCart()">
          <i data-lucide="shopping-cart" class="w-6 h-6 text-gray-700 hover:text-primary-color transition-colors"></i>
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

  <div class="product-page">
    <section class="product-card" aria-label="<?php echo htmlspecialchars($title); ?> product details">
      <!-- Back arrow in top left corner of card -->
      <a href="javascript:history.back()" class="back-arrow" aria-label="Go back">
        <img src="../img/flecha-izquierda.png" alt="Go back" class="back-arrow-img" />
      </a>
      
      <div class="product-image">
        <img src="<?php echo htmlspecialchars($productFromDB['imagen_url']); ?>" alt="<?php echo htmlspecialchars($productFromDB['nombre']); ?>" />
      </div>
      
      <div class="product-details">
        <h1><?php echo htmlspecialchars($title); ?></h1>
        <?php if ($subtitle): ?>
          <div class="product-subtitle"><?php echo htmlspecialchars($subtitle); ?></div>
        <?php endif; ?>
        <div class="product-price">$<?php echo number_format($productFromDB['precio'], 2); ?></div>
        
        <div style="--i: 0">
          <h2 class="section-title">Description</h2>
          <p><?php echo htmlspecialchars($extra['description']); ?></p>
        </div>
        
        <div style="--i: 1">
          <h2 class="section-title">Inspiration</h2>
          <p><?php echo htmlspecialchars($extra['inspiration']); ?></p>
        </div>
        
        <div style="--i: 2">
          <h2 class="section-title">Material</h2>
          <p><?php echo htmlspecialchars($extra['material']); ?></p>
        </div>
        
        <div style="--i: 3">
          <h2 class="section-title">Crafting Time</h2>
          <p><?php echo htmlspecialchars($extra['work_hours']); ?></p>
        </div>
        
        <div style="--i: 4">
          <h2 class="section-title">Artisan Story</h2>
          <p><?php echo htmlspecialchars($extra['story']); ?></p>
        </div>
        
        <form method="POST" action="../cart/add_to_cart.php">
          <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
          <button type="submit" class="buy-button">
            Add to Cart
          </button>
        </form>

        <?php if ($isLoggedIn): ?>
          <button id="openChatBtn">
            <i data-lucide="message-circle" class="w-4 h-4"></i>
            Chat with Admin
          </button>
        <?php endif; ?>
      </div>
    </section>
  </div>

  <!-- Chat Modal -->
  <div id="chatModal" style="display:none;">
    <div class="chat-modal-content">
      <span id="closeChatBtn">&times;</span>
      <div id="chatMessages"></div>
      <form id="chatForm">
        <input type="text" id="chatInput" placeholder="Type your message..." required />
        <button type="submit">Send</button>
      </form>
    </div>
  </div>

  <footer class="bg-white py-8 border-t border-gray-200 mt-12">
    <div class="container mx-auto px-4">
      <div class="flex flex-col md:flex-row items-center justify-between">
        <!-- Logo -->
        <div class="mb-6 md:mb-0">
          <img src="../img/logoSinFondo.png" alt="Autana Logo" class="h-12">
        </div>

        <!-- Social Media -->
        <div class="flex space-x-6">
          <a href="#" class="text-gray-600 hover:text-primary-color transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z" />
            </svg>
          </a>
          <a href="#" class="text-gray-600 hover:text-primary-color transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zM12 16c-2.209 0-4-1.79-4-4s1.791-4 4-4 4 1.79 4 4-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
            </svg>
          </a>
          <a href="#" class="text-gray-600 hover:text-primary-color transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
              <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
            </svg>
          </a>
        </div>
      </div>

      <!-- Copyright -->
      <div class="mt-8 text-center text-gray-500 text-sm">
        <p>© Autana. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <script>
    // Chat functionality
    document.addEventListener('DOMContentLoaded', function() {
      const openChatBtn = document.getElementById('openChatBtn');
      const closeChatBtn = document.getElementById('closeChatBtn');
      const chatModal = document.getElementById('chatModal');
      
      if (openChatBtn) {
        openChatBtn.addEventListener('click', function() {
          chatModal.style.display = 'block';
        });
      }
      
      if (closeChatBtn) {
        closeChatBtn.addEventListener('click', function() {
          chatModal.style.display = 'none';
        });
      }
    });
  </script>
</body>
</html>