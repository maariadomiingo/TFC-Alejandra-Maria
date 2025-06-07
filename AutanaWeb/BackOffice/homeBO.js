document.addEventListener("DOMContentLoaded", function() {
  const navChats = document.getElementById("nav-chats");
  const chatsSection = document.getElementById("chats-section");
  const chatsList = document.getElementById("chats-list");
  const chatAdminModal = document.getElementById("chat-admin-modal");
  const chatAdminTitle = document.getElementById("chat-admin-title");
  const chatAdminMessages = document.getElementById("chat-admin-messages");
  const chatAdminForm = document.getElementById("chat-admin-form");
  const chatAdminInput = document.getElementById("chat-admin-input");
  const closeAdminChat = document.getElementById("close-admin-chat");

  let currentChatId = null;

  // Mostrar sección de chats al pulsar en el menú
  if (navChats) {
    navChats.onclick = function(e) {
      e.preventDefault();
      chatsSection.style.display = "block";
      // Oculta otras secciones si las tienes
      cargarListaChats();
    };
  }

  // Cargar lista de chats
  function cargarListaChats() {
    fetch("../chat/listar_chats.php")
      .then(res => res.json())
      .then(data => {
        chatsList.innerHTML = "";
        if (data.chats.length === 0) {
          chatsList.innerHTML = "<p>No hay chats activos.</p>";
        }
        data.chats.forEach(chat => {
          // Crear el contenedor del chat
          const chatContainer = document.createElement("div");
          chatContainer.className = "chat-list-item-container";

          // Cabecera del chat con flecha
          const header = document.createElement("div");
          header.className = "chat-list-item";
          header.style.display = "flex";
          header.style.alignItems = "center";
          header.style.cursor = "pointer";

          // Flecha
          const arrow = document.createElement("span");
          arrow.textContent = "▶";
          arrow.style.marginRight = "8px";
          arrow.style.transition = "transform 0.2s";
          header.appendChild(arrow);

          // Texto
          const text = document.createElement("span");
          text.textContent = `Producto: ${chat.producto_nombre} | Cliente: ${chat.cliente_nombre}`;
          header.appendChild(text);

          // Contenedor de mensajes (plegado por defecto)
          const messagesDiv = document.createElement("div");
          messagesDiv.className = "chat-messages-admin";
          messagesDiv.style.display = "none";
          messagesDiv.style.padding = "0.5rem 1.5rem";

          // Evento para desplegar/plegar
          let desplegado = false;
          header.onclick = function() {
            if (!desplegado) {
              // Cargar mensajes solo si se despliega
              fetch("../chat/obtener_mensajes_admin.php?chat_id=" + chat.chat_id)
                .then(res => res.json())
                .then(data => {
                  messagesDiv.innerHTML = "";
                  data.mensajes.forEach(msg => {
                    const div = document.createElement("div");
                    div.textContent = msg.remitente + ": " + msg.mensaje;
                    messagesDiv.appendChild(div);
                  });
                  // Añadir formulario de respuesta
                  const form = document.createElement("form");
                  form.style.marginTop = "1rem";
                  form.innerHTML = `
                    <input type="text" class="chat-admin-input" placeholder="Escribe tu respuesta..." required style="width:70%;padding:0.3rem;border-radius:4px;border:1px solid #ccc;">
                    <button type="submit" style="padding:0.3rem 1rem;border-radius:4px;border:none;background:#007bff;color:#fff;font-weight:bold;cursor:pointer;">Enviar</button>
                  `;
                  form.onsubmit = function(e) {
                    e.preventDefault();
                    const input = form.querySelector("input");
                    fetch("../chat/enviar_mensajes.php", {
                      method: "POST",
                      headers: { "Content-Type": "application/json" },
                      body: JSON.stringify({
                        chat_id: chat.chat_id,
                        mensaje: input.value,
                        admin: true
                      })
                    })
                    .then(res => res.json())
                    .then(resp => {
                      if (resp.success) {
                        input.value = "";
                        header.click(); // Recargar mensajes
                      }
                    });
                  };
                  messagesDiv.appendChild(form);
                });
              messagesDiv.style.display = "block";
              arrow.style.transform = "rotate(90deg)";
              desplegado = true;
            } else {
              messagesDiv.style.display = "none";
              arrow.style.transform = "rotate(0deg)";
              desplegado = false;
            }
          };

          chatContainer.appendChild(header);
          chatContainer.appendChild(messagesDiv);
          chatsList.appendChild(chatContainer);
        });
      });
  }

  // Abrir chat y cargar mensajes
  function abrirChat(chatId, producto, cliente) {
    currentChatId = chatId;
    chatAdminTitle.textContent = `Chat de ${cliente} sobre "${producto}"`;
    chatAdminModal.style.display = "block";
    cargarMensajesAdmin();
  }

  // Cerrar modal de chat
  if (closeAdminChat) {
    closeAdminChat.onclick = function() {
      chatAdminModal.style.display = "none";
      chatAdminMessages.innerHTML = "";
      currentChatId = null;
    };
  }

  // Enviar mensaje como admin
  if (chatAdminForm) {
    chatAdminForm.onsubmit = function(e) {
      e.preventDefault();
      fetch("../chat/enviar_mensajes.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          chat_id: currentChatId,
          mensaje: chatAdminInput.value,
          admin: true
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          chatAdminInput.value = "";
          cargarMensajesAdmin();
        }
      });
    };
  }

  // Cargar mensajes del chat seleccionado
  function cargarMensajesAdmin() {
    if (!currentChatId) return;
    fetch("../chat/obtener_mensajes_admin.php?chat_id=" + currentChatId)
      .then(res => res.json())
      .then(data => {
        chatAdminMessages.innerHTML = "";
        data.mensajes.forEach(msg => {
          const div = document.createElement("div");
          div.textContent = msg.remitente + ": " + msg.mensaje;
          chatAdminMessages.appendChild(div);
        });
        chatAdminMessages.scrollTop = chatAdminMessages.scrollHeight;
      });
  }

  // Cargar los usuarios recientes
  fetch("obtener_usuarios_recientes.php")
    .then(res => res.json())
    .then(data => {
      const ul = document.getElementById("recent-users-list");
      ul.innerHTML = "";
      data.usuarios.forEach(usuario => {
        const li = document.createElement("li");
        li.innerHTML = `
          <img src="../img/user.svg" alt="" class="avatar" />
          <span>${usuario.nombre}</span>
        `;
        ul.appendChild(li);
      });
    });

  // Redirigir al pulsar "View all"
  const btnViewAll = document.getElementById("btn-viewall");
  if (btnViewAll) {
    btnViewAll.onclick = function() {
      // Aquí puedes cambiar la pestaña o redirigir
      // Por ejemplo, si tienes una sección con id="users-section":
      // document.getElementById("users-section").style.display = "block";
      // O simplemente redirigir:
      window.location.href = "users.html"; // Cambia esto por la ruta real de tu sección de usuarios
    };
  }

  // Cargar los chats automáticamente al cargar la página
  cargarListaChats();
});