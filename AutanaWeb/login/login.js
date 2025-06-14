document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");

  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");

  // Validación en tiempo real
  emailInput.addEventListener("input", () => {
    if (!emailInput.value.trim()) {
      emailError.textContent = "El correo es obligatorio.";
    } else if (!validateEmail(emailInput.value.trim())) {
      emailError.textContent = "Formato de correo inválido.";
    } else {
      emailError.textContent = "";
    }
  });

  passwordInput.addEventListener("input", () => {
    if (!passwordInput.value.trim()) {
      passwordError.textContent = "La contraseña es obligatoria.";
    } else if (passwordInput.value.length < 6) {
      passwordError.textContent = "Debe tener al menos 6 caracteres.";
    } else {
      passwordError.textContent = "";
    }
  });

  // Validación al enviar el formulario
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();

    let valid = true;

    // Validaciones
    if (!email) {
      emailError.textContent = "El correo es obligatorio.";
      valid = false;
    } else if (!validateEmail(email)) {
      emailError.textContent = "Formato de correo inválido.";
      valid = false;
    } else {
      emailError.textContent = "";
    }

    if (!password) {
      passwordError.textContent = "La contraseña es obligatoria.";
      valid = false;
    } else if (password.length < 6) {
      passwordError.textContent = "Debe tener al menos 6 caracteres.";
      valid = false;
    } else {
      passwordError.textContent = "";
    }

    if (!valid) return;

    try {
      const response = await fetch("./login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (data.success) {
        window.location.href = "../homeUser/home.php";
      } else {
        if (
          data.message.toLowerCase().includes("correo") ||
          data.message.toLowerCase().includes("contraseña")
        ) {
          passwordError.textContent = "Correo o contraseña incorrectos.";
        } else {
          passwordError.textContent = data.message || "Error al iniciar sesión.";
        }
      }
    } catch (error) {
      passwordError.textContent = "Error en la solicitud. Intenta más tarde.";
    }
  });

  function validateEmail(email) {
    const regex = /^[\w.-]+@([\w-]+\.)+[a-zA-Z]{2,7}$/;
    return regex.test(email);
  }

  // Toggle para mostrar/ocultar contraseña
  const togglePassword = document.getElementById("togglePassword");

  togglePassword.addEventListener("mousedown", () => {
    passwordInput.type = "text";
    togglePassword.classList.replace("fa-eye", "fa-eye-slash");
  });

  togglePassword.addEventListener("mouseup", () => {
    passwordInput.type = "password";
    togglePassword.classList.replace("fa-eye-slash", "fa-eye");
  });

  togglePassword.addEventListener("mouseleave", () => {
    passwordInput.type = "password";
    togglePassword.classList.replace("fa-eye-slash", "fa-eye");
  });

  // Para móviles
  togglePassword.addEventListener("touchstart", () => {
    passwordInput.type = "text";
    togglePassword.classList.replace("fa-eye", "fa-eye-slash");
  });

  togglePassword.addEventListener("touchend", () => {
    passwordInput.type = "password";
    togglePassword.classList.replace("fa-eye-slash", "fa-eye");
  });
});
