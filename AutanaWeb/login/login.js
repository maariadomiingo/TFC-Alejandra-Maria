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

    // Validaciones previas
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
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (data.success) {
        emailError.textContent = "";
        passwordError.textContent = "";
        alert("Inicio de sesión exitoso");
        window.location.href = "../html/home.html";
      } else {
        // Mostrar errores en campos
        if (
          data.message.toLowerCase().includes("correo") ||
          data.message.toLowerCase().includes("contraseña")
        ) {
          passwordError.textContent = "Correo o contraseña incorrectos.";
        } else {
          alert(data.message || "Error al iniciar sesión.");
        }
      }
    } catch (error) {
      alert("Error en la solicitud: " + error.message);
    }
  });

  function validateEmail(email) {
    const regex = /^[\w.-]+@([\w-]+\.)+[a-zA-Z]{2,7}$/;
    return regex.test(email);
  }
});
