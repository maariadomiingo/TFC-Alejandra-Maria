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

  document.addEventListener("DOMContentLoaded", function() {
  const openBtn = document.getElementById("openChatBtn");
  const chatModal = document.getElementById("chatModal");
  const closeBtn = document.getElementById("closeChatBtn");
  const chatForm = document.getElementById("chatForm");
  const chatInput = document.getElementById("chatInput");
  const chatMessages = document.getElementById("chatMessages");
  let chatInterval = null;

  if (openBtn) {
    openBtn.onclick = () => {
      chatModal.style.display = "flex";
      loadMessages();
      // Inicia el refresco automático cada 2 segundos
      chatInterval = setInterval(loadMessages, 2000);
    };
  }
  if (closeBtn) {
    closeBtn.onclick = () => {
      chatModal.style.display = "none";
      // Detiene el refresco automático
      clearInterval(chatInterval);
    };
  }

  // Cerrar modal al hacer click fuera
  window.onclick = function(event) {
    if (event.target === chatModal) chatModal.style.display = "none";
  };

  // Enviar mensaje
  if (chatForm) {
    chatForm.onsubmit = function(e) {
      e.preventDefault();
      fetch("../chat/enviar_mensajes.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          producto_id: PRODUCT_ID,
          mensaje: chatInput.value
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          chatInput.value = "";
          loadMessages();
        }
      });
    };
  }

  // Cargar mensajes
  function loadMessages() {
    fetch("../chat/obtener_mensajes.php?producto_id=" + PRODUCT_ID)
      .then(res => res.json())
      .then(data => {
        chatMessages.innerHTML = "";
        data.mensajes.forEach(msg => {
          const div = document.createElement("div");
          div.textContent = msg.texto_mensaje;
          chatMessages.appendChild(div);
        });
        chatMessages.scrollTop = chatMessages.scrollHeight;
      });
  }
});