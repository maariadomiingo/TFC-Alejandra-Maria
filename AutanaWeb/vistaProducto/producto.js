window.onload = () => {
      lucide.createIcons();

      // Menú responsive
      const toggle = document.getElementById('menu-toggle');
      const menu = document.getElementById('mobile-menu');
      toggle.addEventListener('click', () => {
        menu.classList.toggle('hidden');
      });

      // Miniaturas → Imagen principal
      const thumbnails = document.querySelectorAll(".thumb");
      const mainImage = document.getElementById("main-image");

      thumbnails.forEach((thumb) => {
        thumb.addEventListener("click", () => {
          if (mainImage.src === thumb.src) return; // evita cambios innecesarios

          mainImage.classList.add("fade"); // Aplica efecto

          setTimeout(() => {
            mainImage.src = thumb.src;
            mainImage.alt = thumb.alt;
          }, 200); // Espera a mitad de la transición

          setTimeout(() => {
            mainImage.classList.remove("fade");
          }, 400); // Fin de la transición
        });
      });
    };

    document.addEventListener("DOMContentLoaded", () => {
    const toggles = document.querySelectorAll(".accordion-toggle");

    toggles.forEach(toggle => {
      toggle.addEventListener("click", () => {
        const content = toggle.nextElementSibling;
        const isOpen = content.style.display === "block";

        // Opcional: cerrar todos antes de abrir otro (modo exclusivo)
        // document.querySelectorAll(".accordion-content").forEach(c => c.style.display = "none");

        content.style.display = isOpen ? "none" : "block";
      });
    });
  });