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
          const div = document.createElement("div");
          div.className = "chat-list-item";
          div.textContent = `Producto: ${chat.producto_nombre} | Cliente: ${chat.cliente_nombre}`;
          div.style.cursor = "pointer";
          div.onclick = () => abrirChat(chat.chat_id, chat.producto_nombre, chat.cliente_nombre);
          chatsList.appendChild(div);
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
});