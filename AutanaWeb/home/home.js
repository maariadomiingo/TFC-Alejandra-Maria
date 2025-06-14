const texts = [
  `<span>Our mission</span> is to preserve indigenous heritage through fashion that honors and shares their stories.`,
  `<span>Philanthropy</span> this project is rooted in respect and admiration for Venezuela's indigenous cultures. Beyond showcasing their rich heritage through fashion, our long-term goal is to generate fair employment opportunities for local artisans. We are committed to ethical collaboration, ensuring their rights, voices, and traditions are fully respected and preserved.`,
  `<span>Sustainability</span> Our commitment to sustainability goes beyond materials — it’s about preserving cultural identity, empowering communities, and minimizing environmental impact. We aim to work with eco-friendly fabrics and responsible production methods, while building a supply chain that values transparency, human dignity, and long-term social development.`
];

const mainText = document.getElementById("mainText");
const options = document.querySelectorAll(".options-static span");

options.forEach((el, index) => {
  el.addEventListener("click", () => {
    mainText.innerHTML = texts[index];

    // Eliminar clase activa de todos
    options.forEach(opt => opt.classList.remove("active"));

    // Añadir clase activa al clicado
    el.classList.add("active");
  });
});

function goToCart() {
  window.location.href = "../cart/cart.html";
}

document.querySelectorAll('.favorite-btn').forEach(btn => {
  btn.addEventListener('click', async () => {
    const outfitDiv = btn.closest('.outfit');
    const productId = outfitDiv.getAttribute('data-product-id');

    try {
      const response = await fetch('/api/favoritos_toggle.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ producto_id: productId })
      });
      const data = await response.json();

      if (data.success) {
        btn.textContent = data.favorito ? '❤️' : '♡';
      } else if (data.error) {
        alert(data.error);
      }
    } catch (e) {
      alert('Error de conexión con el servidor.');
    }
  });
});
const video = document.querySelector('.mission-video');

  video.addEventListener('mouseenter', () => {
    video.pause();
  });

  video.addEventListener('mouseleave', () => {
    video.play();
  });