document.addEventListener("DOMContentLoaded", () => {
  const correoInput = document.getElementById("correo");
  const passwordInput = document.getElementById("password");
  const tallaTopInput = document.getElementById("talla_top");
  const tallaBottomInput = document.getElementById("talla_bottom");
  const direccionInput = document.getElementById("direccion_envio");
  const form = document.querySelector("form");

  let isPasswordVerified = false; // Mover fuera del submit para mantener estado

  function validarCorreo(correo) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(correo);
  }

  function mostrarError(input, mensaje) {
    eliminarError(input);
    const error = document.createElement("p");
    error.className = "text-red-500 text-sm mt-1";
    error.innerText = mensaje;
    input.classList.add("border-red-500");
    input.insertAdjacentElement("afterend", error);
  }

  function eliminarError(input) {
    input.classList.remove("border-red-500");
    const siguiente = input.nextElementSibling;
    if (siguiente && siguiente.classList.contains("text-red-500")) {
      siguiente.remove();
    }
  }

  // Validaciones en tiempo real
  correoInput.addEventListener("input", () => {
    eliminarError(correoInput);
    if (!validarCorreo(correoInput.value)) {
      mostrarError(correoInput, "Correo inválido");
    }
  });

  passwordInput.addEventListener("input", () => {
    eliminarError(passwordInput);
    if (passwordInput.value && passwordInput.value.length < 6) {
      mostrarError(passwordInput, "La contraseña debe tener al menos 6 caracteres");
    }
  });

  tallaTopInput.addEventListener("input", () => {
    eliminarError(tallaTopInput);
    if (!tallaTopInput.value.trim()) {
      mostrarError(tallaTopInput, "Talla superior requerida");
    }
  });

  tallaBottomInput.addEventListener("input", () => {
    eliminarError(tallaBottomInput);
    if (!tallaBottomInput.value.trim()) {
      mostrarError(tallaBottomInput, "Talla inferior requerida");
    }
  });

  direccionInput.addEventListener("input", () => {
    eliminarError(direccionInput);
    if (!direccionInput.value.trim()) {
      mostrarError(direccionInput, "Dirección de envío requerida");
    }
  });

  // Envío del formulario
  form.addEventListener("submit", function (e) {
    let errores = false;

    eliminarError(correoInput);
    if (!validarCorreo(correoInput.value)) {
      mostrarError(correoInput, "Correo inválido");
      errores = true;
    }

    eliminarError(passwordInput);
    if (passwordInput.value && passwordInput.value.length < 6) {
      mostrarError(passwordInput, "La contraseña debe tener al menos 6 caracteres");
      errores = true;
    }

    eliminarError(tallaTopInput);
    if (!tallaTopInput.value.trim()) {
      mostrarError(tallaTopInput, "Talla superior requerida");
      errores = true;
    }

    eliminarError(tallaBottomInput);
    if (!tallaBottomInput.value.trim()) {
      mostrarError(tallaBottomInput, "Talla inferior requerida");
      errores = true;
    }

    eliminarError(direccionInput);
    if (!direccionInput.value.trim()) {
      mostrarError(direccionInput, "Dirección de envío requerida");
      errores = true;
    }

    if (errores) {
      e.preventDefault();
    } else if (!isPasswordVerified) {
      e.preventDefault();
      openCurrentPasswordModal();
    }
  });

  // Funciones del modal de verificación de contraseña
  window.openCurrentPasswordModal = function () {
    document.getElementById('currentPasswordModal').classList.remove('hidden');
  }

  window.closeCurrentPasswordModal = function () {
    document.getElementById('currentPasswordModal').classList.add('hidden');
    document.getElementById('currentPasswordError').classList.add('hidden');
    document.getElementById('current_password_input').value = '';
  }

  window.validateCurrentPassword = function () {
    const entered = document.getElementById('current_password_input').value;
    const currentPasswordCorrect = "123456"; // Aquí debes integrar con el servidor

    if (entered === currentPasswordCorrect) {
      isPasswordVerified = true;
      closeCurrentPasswordModal();
      form.submit();
    } else {
      document.getElementById('currentPasswordError').classList.remove('hidden');
    }
  }

  // Auto cierre de popup de éxito
  const popup = document.getElementById('successPopup');
  if (popup) {
    setTimeout(() => popup.remove(), 5000);
  }
});

function savePassword() {
  const newPassword = document.getElementById('password').value;
  const hiddenField = document.getElementById('hidden_password');

  if (newPassword.trim().length < 6) {
    alert("La nueva contraseña debe tener al menos 6 caracteres.");
    return;
  }

  hiddenField.value = newPassword; // actualizar valor del campo oculto
  toggleModal();
}
  // function savePassword() {
  //   const newPassword = document.getElementById('password').value;
  //   if (newPassword.trim() !== '') {
  //     const hiddenField = document.createElement('input');
  //     hiddenField.type = 'hidden';
  //     hiddenField.name = 'password';
  //     hiddenField.value = newPassword;

  //     const form = document.querySelector('form');
  //     form.appendChild(hiddenField);

  //     toggleModal();
  //   }
  // }
   function closeSuccessPopup() {
    const popup = document.getElementById('successPopup');
    if (popup) popup.remove();
  }

  // Auto-close after 5 seconds
  window.addEventListener('DOMContentLoaded', () => {
    const popup = document.getElementById('successPopup');
    if (popup) {
      setTimeout(() => popup.remove(), 5000);
    }
  });