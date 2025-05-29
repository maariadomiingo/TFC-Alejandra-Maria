document.addEventListener("DOMContentLoaded", () => {
  const correoInput = document.getElementById("correo");
  const passwordInput = document.getElementById("password");
  const tallaTopInput = document.getElementById("talla_top");
  const tallaBottomInput = document.getElementById("talla_bottom");
  const direccionInput = document.getElementById("direccion_envio");

  function validarCorreo(correo) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(correo);
  }

  function mostrarError(input, mensaje) {
    eliminarError(input); // Eliminar errores previos
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

  // Eventos de validación en tiempo real
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

  // Validación completa al enviar
  document.querySelector("form").addEventListener("submit", function (e) {
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
    }
  });
});
