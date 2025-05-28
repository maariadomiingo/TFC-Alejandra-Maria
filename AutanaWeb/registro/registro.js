const form = document.getElementById("registerForm");
const inputs = {
  name: document.getElementById("name"),
  email: document.getElementById("email"),
  password: document.getElementById("password"),
  confirm_password: document.getElementById("confirm_password") // Agregamos el campo para confirmar la contraseña
};

const errors = {
  name: document.getElementById("nameError"),
  email: document.getElementById("emailError"),
  password: document.getElementById("passwordError"),
  confirm_password: document.getElementById("confirmPasswordError") // Error para confirmar la contraseña
};

const validateField = (field, condition, errorMsgElement) => {
  if (!condition) {
    field.classList.add("invalid");
    field.classList.remove("valid");
    errorMsgElement.style.display = "block";
    return false;
  } else {
    field.classList.remove("invalid");
    field.classList.add("valid");
    errorMsgElement.style.display = "none";
    return true;
  }
};

// Validaciones en tiempo real
inputs.name.addEventListener("input", () => {
  validateField(inputs.name, inputs.name.value.trim() !== "", errors.name);
});

inputs.email.addEventListener("input", () => {
  const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(inputs.email.value);
  validateField(inputs.email, valid, errors.email);
});

inputs.password.addEventListener("input", () => {
  validateField(inputs.password, inputs.password.value.length >= 6, errors.password);
});

// Validar confirmación de la contraseña en tiempo real
inputs.confirm_password.addEventListener("input", () => {
  validateField(
    inputs.confirm_password,
    inputs.password.value === inputs.confirm_password.value,
    errors.confirm_password
  );
});

// Validar al enviar
form.addEventListener("submit", async function (e) {
  e.preventDefault();

  // Realizamos las validaciones de los campos
  const validName = validateField(inputs.name, inputs.name.value.trim() !== "", errors.name);
  const validEmail = validateField(inputs.email, /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(inputs.email.value), errors.email);
  const validPassword = validateField(inputs.password, inputs.password.value.length >= 6, errors.password);

  // Validamos que la confirmación de la contraseña coincida con la contraseña
  const validConfirmPassword = validateField(
    inputs.confirm_password,
    inputs.password.value === inputs.confirm_password.value,
    errors.confirm_password
  );

  if (validName && validEmail && validPassword && validConfirmPassword) {
    const formData = new FormData(form);
    const res = await fetch("../server/registro.php", {
      method: "POST",
      body: formData
    });

    const result = await res.text();
    document.getElementById("responseMessage").innerText = result;
    form.reset();

    // Reseteamos los estilos de validación
    for (let input of Object.values(inputs)) {
      input.classList.remove("valid", "invalid");
    }
  } else {
    document.getElementById("responseMessage").innerText = "";
  }
});
