  const texts = [
      `<span>Our mission</span> is to preserve indigenous heritage through fashion that honors and shares their stories.`,
      `<span>Philanthropy</span> this project is rooted in respect and admiration for Venezuela's indigenous cultures. Beyond showcasing their rich heritage through fashion, our long-term goal is to generate fair employment opportunities for local artisans. We are committed to ethical collaboration, ensuring their rights, voices, and traditions are fully respected and preserved.`,
      `<span>Sustainability</span> Our commitment to sustainability goes beyond materials — it’s about preserving cultural identity, empowering communities, and minimizing environmental impact. We aim to work with eco-friendly fabrics and responsible production methods, while building a supply chain that values transparency, human dignity, and long-term social development.`
    ];

    const mainText = document.getElementById("mainText");
    const options = document.querySelectorAll(".options div");

    options.forEach((el, index) => {
      el.addEventListener("click", () => {
        mainText.innerHTML = texts[index];
      });
    });

    function goToCart() {
  window.location.href = "../cart/cart.html";
}