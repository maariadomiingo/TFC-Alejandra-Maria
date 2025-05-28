<?php
// product.php
session_start();

$isLoggedIn = isset($_SESSION['user_name']);
$username = $isLoggedIn ? $_SESSION['user_name'] : '';

// Obtener el id del producto de la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Si no hay id o no es válido, redirige o muestra error
    header("Location: ../collection/collection.php");
    exit;
}

$productId = (int)$_GET['id'];

// Array de productos
$products = [
  1 => [
    'name' => 'Prenda 1 - Vestido Tradicional',
    'price' => '$120',
    'image' => '../img/outfit1.png',
    'description' => 'Este vestido refleja la esencia artesanal de las comunidades indígenas, confeccionado con técnicas ancestrales y detalles hechos a mano.',
    'inspiration' => 'Inspirada en las comunidades indígenas de Venezuela, esta prenda honra su legado cultural y los colores de su entorno natural.',
    'material' => 'Algodón orgánico teñido con pigmentos naturales extraídos de plantas locales.',
    'work_hours' => '30 horas de trabajo artesanal dedicadas a cada pieza.',
    'story' => 'María, una artesana de la comunidad, preserva la cultura ancestral transmitida por sus antepasados en cada puntada de este vestido.'
  ],
  2 => [
    'name' => 'Prenda 2 - Blusa Bordada',
    'price' => '$150',
    'image' => '../img/outfit2.png',
    'description' => 'Blusa hecha a mano con bordados que representan símbolos tradicionales, perfecta para quienes valoran la autenticidad y la historia.',
    'inspiration' => 'Inspiración en los tejidos y patrones de las etnias venezolanas, buscando conectar pasado y presente.',
    'material' => 'Lino y algodón combinados con tintes naturales.',
    'work_hours' => '25 horas de bordado minucioso y costura.',
    'story' => 'La blusa lleva la historia de Ana, quien aprendió el arte del bordado a través de generaciones en su familia indígena.'
  ],
  3 => [
    'name' => 'Prenda 3 - Pantalón Artesanal',
    'price' => '$140',
    'image' => '../img/outfit3.avif',
    'description' => 'Pantalón cómodo y resistente, elaborado con técnicas textiles ancestrales, ideal para uso diario con estilo único.',
    'inspiration' => 'Diseñado en honor a las comunidades que valoran la funcionalidad sin perder su identidad cultural.',
    'material' => 'Tela de algodón reciclado teñida naturalmente.',
    'work_hours' => '28 horas dedicadas al tejido y acabado artesanal.',
    'story' => 'Este pantalón cuenta la historia de José, un tejedor comprometido con la sostenibilidad y el arte tradicional.'
  ],
  4 => [
    'name' => 'Prenda 4 - Chaleco Tejido',
    'price' => '$110',
    'image' => '../img/outfit4.jpg',
    'description' => 'Chaleco tejido a mano con fibras naturales, con un diseño que representa la flora y fauna local.',
    'inspiration' => 'Inspirado en los patrones y colores que se encuentran en la biodiversidad venezolana.',
    'material' => 'Fibras de algodón y lana natural.',
    'work_hours' => '22 horas de trabajo manual y tejido delicado.',
    'story' => 'Cada hilo lleva la dedicación de Carmen, artesana que mezcla tradición y naturaleza en su trabajo.'
  ],
  5 => [
    'name' => 'Prenda 5 - Falda Plisada',
    'price' => '$130',
    'image' => '../img/outfit5.webp',
    'description' => 'Falda plisada con detalles bordados que evocan las historias y leyendas indígenas.',
    'inspiration' => 'Inspirada en la danza y festividades tradicionales venezolanas.',
    'material' => 'Algodón suave con bordados hechos a mano.',
    'work_hours' => '26 horas dedicadas al plisado y bordado.',
    'story' => 'La falda es un tributo a Lucía, una joven artesana que transmite sus raíces a través del arte textil.'
  ],
  6 => [
    'name' => 'Prenda 6 - Camisa de Lino',
    'price' => '$135',
    'image' => '../img/outift3.png',
    'description' => 'Camisa fresca y elegante, elaborada con lino natural y acabados artesanales.',
    'inspiration' => 'Inspirada en el estilo de vida de las comunidades indígenas que viven en armonía con la naturaleza.',
    'material' => 'Lino 100% natural y tintes orgánicos.',
    'work_hours' => '24 horas de confección artesanal.',
    'story' => 'Esta camisa refleja la pasión de Pedro por mantener vivas las técnicas ancestrales y su compromiso con el medio ambiente.'
  ],
];

// Validar que el producto exista
if (!array_key_exists($productId, $products)) {
    echo "<h2>Producto no encontrado.</h2>";
    exit;
}

$product = $products[$productId];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="../home/index.css">
  <link rel="stylesheet" href="../vistaProducto/producto.css" />
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

  <div class="product-page">
      <section class="product-card" aria-label="Detalle del producto <?php echo htmlspecialchars($product['name']); ?>">
    <div class="product-image" role="img" aria-label="Imagen de <?php echo htmlspecialchars($product['name']); ?>">
      <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
    </div>

    <div class="product-details">
      <h1><?php echo htmlspecialchars($product['name']); ?></h1>
      <div class="product-price"><?php echo htmlspecialchars($product['price']); ?></div>

      <div>
        <h2 class="section-title">Descripción</h2>
        <p><?php echo htmlspecialchars($product['description']); ?></p>
      </div>

      <div>
        <h2 class="section-title">Inspiración</h2>
        <p><?php echo htmlspecialchars($product['inspiration']); ?></p>
      </div>

      <div>
        <h2 class="section-title">Material</h2>
        <p><?php echo htmlspecialchars($product['material']); ?></p>
      </div>

      <div>
        <h2 class="section-title">Horas de trabajo</h2>
        <p><?php echo htmlspecialchars($product['work_hours']); ?></p>
      </div>

      <div>
        <h2 class="section-title">Historia inspiradora</h2>
        <p><?php echo htmlspecialchars($product['story']); ?></p>
      </div>
    </div>

    <form method="POST" action="../cart/cart.html">
  <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
  <button type="submit" class="buy-button">
    Comprar
  </button>
</form>

  </section>
  </div>


</body>
</html>
