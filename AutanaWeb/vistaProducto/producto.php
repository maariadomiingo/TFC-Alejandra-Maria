<?php
// product.php
session_start();
require_once __DIR__ . '/../server/db_config.php';

$isLoggedIn = isset($_SESSION['user_name']);
$username = $isLoggedIn ? $_SESSION['user_name'] : '';

// Obtener el id del producto de la URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  // Si no hay id o no es válido, redirige o muestra error
  header("Location: ../collection/collection.php");
  exit;
}

$productId = (int)$_GET['id'];

// Array local SOLO para los campos extra
$productsExtra = [
  1 => [
    'description' => 'Este vestido refleja la esencia artesanal de las comunidades indígenas, confeccionado con técnicas ancestrales y detalles hechos a mano.',
    'inspiration' => 'Inspirada en las comunidades indígenas de Venezuela, esta prenda honra su legado cultural y los colores de su entorno natural.',
    'material' => 'Algodón orgánico teñido con pigmentos naturales extraídos de plantas locales.',
    'work_hours' => '30 horas de trabajo artesanal dedicadas a cada pieza.',
    'story' => 'María, una artesana de la comunidad, preserva la cultura ancestral transmitida por sus antepasados en cada puntada de este vestido.'
  ],
  2 => [
    'description' => 'Blusa hecha a mano con bordados que representan símbolos tradicionales, perfecta para quienes valoran la autenticidad y la historia.',
    'inspiration' => 'Inspiración en los tejidos y patrones de las etnias venezolanas, buscando conectar pasado y presente.',
    'material' => 'Lino y algodón combinados con tintes naturales.',
    'work_hours' => '25 horas de bordado minucioso y costura.',
    'story' => 'La blusa lleva la historia de Ana, quien aprendió el arte del bordado a través de generaciones en su familia indígena.'
  ],
  3 => [
    'description' => 'Pantalón cómodo y resistente, elaborado con técnicas textiles ancestrales, ideal para uso diario con estilo único.',
    'inspiration' => 'Diseñado en honor a las comunidades que valoran la funcionalidad sin perder su identidad cultural.',
    'material' => 'Tela de algodón reciclado teñida naturalmente.',
    'work_hours' => '28 horas dedicadas al tejido y acabado artesanal.',
    'story' => 'Este pantalón cuenta la historia de José, un tejedor comprometido con la sostenibilidad y el arte tradicional.'
  ],
  4 => [
    'description' => 'Chaleco tejido a mano con fibras naturales, con un diseño que representa la flora y fauna local.',
    'inspiration' => 'Inspirado en los patrones y colores que se encuentran en la biodiversidad venezolana.',
    'material' => 'Fibras de algodón y lana natural.',
    'work_hours' => '22 horas de trabajo manual y tejido delicado.',
    'story' => 'Cada hilo lleva la dedicación de Carmen, artesana que mezcla tradición y naturaleza en su trabajo.'
  ],
  5 => [
    'description' => 'Falda plisada con detalles bordados que evocan las historias y leyendas indígenas.',
    'inspiration' => 'Inspirada en la danza y festividades tradicionales venezolanas.',
    'material' => 'Algodón suave con bordados hechos a mano.',
    'work_hours' => '26 horas dedicadas al plisado y bordado.',
    'story' => 'La falda es un tributo a Lucía, una joven artesana que transmite sus raíces a través del arte textil.'
  ],
  6 => [
    'description' => 'Camisa fresca y elegante, elaborada con lino natural y acabados artesanales.',
    'inspiration' => 'Inspirada en el estilo de vida de las comunidades indígenas que viven en armonía con la naturaleza.',
    'material' => 'Lino 100% natural y tintes orgánicos.',
    'work_hours' => '24 horas de confección artesanal.',
    'story' => 'Esta camisa refleja la pasión de Pedro por mantener vivas las técnicas ancestrales y su compromiso con el medio ambiente.'
  ],
];

// Obtener nombre, precio e imagen de la BBDD
$stmt = $pdo->prepare("SELECT nombre, precio, imagen_url FROM productos WHERE id = ?");
$stmt->execute([$productId]);
$productFromDB = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$productFromDB || !isset($productsExtra[$productId])) {
  echo "<h2>Producto no encontrado.</h2>";
  exit;
}

// Separar título y subtítulo
$partes = explode('-', $productFromDB['nombre'], 2);
$titulo = trim($partes[0]);
$subtitulo = isset($partes[1]) ? trim($partes[1]) : '';
$extra = $productsExtra[$productId];
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../home/index.css">
  <link rel="stylesheet" href="../vistaProducto/producto.css" />
  <title><?php echo htmlspecialchars($titulo); ?></title>
</head>

<body>
  <div class="product-page">
    <section class="product-card" aria-label="Detalle del producto <?php echo htmlspecialchars($titulo); ?>">
      <div class="product-image">
        <img src="<?php echo htmlspecialchars($productFromDB['imagen_url']); ?>" alt="<?php echo htmlspecialchars($productFromDB['nombre']); ?>" />
      </div>
      <div class="product-details">
        <h1><?php echo htmlspecialchars($titulo); ?></h1>
        <?php if ($subtitulo): ?>
          <div class="product-subtitle"><?php echo htmlspecialchars($subtitulo); ?></div>
        <?php endif; ?>
        <div class="product-price">$<?php echo number_format($productFromDB['precio'], 2); ?></div>
        <div>
          <h2 class="section-title">Descripción</h2>
          <p><?php echo htmlspecialchars($extra['description']); ?></p>
        </div>
        <div>
          <h2 class="section-title">Inspiración</h2>
          <p><?php echo htmlspecialchars($extra['inspiration']); ?></p>
        </div>
        <div>
          <h2 class="section-title">Material</h2>
          <p><?php echo htmlspecialchars($extra['material']); ?></p>
        </div>
        <div>
          <h2 class="section-title">Horas de trabajo</h2>
          <p><?php echo htmlspecialchars($extra['work_hours']); ?></p>
        </div>
        <div>
          <h2 class="section-title">Historia inspiradora</h2>
          <p><?php echo htmlspecialchars($extra['story']); ?></p>
        </div>
      </div>
      <form method="POST" action="../cart/add_to_cart.php">
        <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
        <button type="submit" class="buy-button">
          Comprar
        </button>
      </form>
    </section>
  </div>
</body>

</html>