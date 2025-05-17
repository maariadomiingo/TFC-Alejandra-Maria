const form = document.getElementById("registerForm");
    const inputs = {
      name: document.getElementById("name"),
      email: document.getElementById("email"),
      password: document.getElementById("password"),
      talla_top: document.getElementById("talla_top"),
      talla_bottom: document.getElementById("talla_bottom"),
      direccion_envio: document.getElementById("direccion_envio")
    };

    const errors = {
      name: document.getElementById("nameError"),
      email: document.getElementById("emailError"),
      password: document.getElementById("passwordError"),
      talla_top: document.getElementById("topError"),
      talla_bottom: document.getElementById("bottomError"),
      direccion_envio: document.getElementById("direccionError")
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

    inputs.talla_top.addEventListener("input", () => {
      validateField(inputs.talla_top, inputs.talla_top.value.trim() !== "", errors.talla_top);
    });

    inputs.talla_bottom.addEventListener("input", () => {
      validateField(inputs.talla_bottom, inputs.talla_bottom.value.trim() !== "", errors.talla_bottom);
    });

    inputs.direccion_envio.addEventListener("input", () => {
      validateField(inputs.direccion_envio, inputs.direccion_envio.value.trim().length >= 5, errors.direccion_envio);
    });

    // Validar al enviar
    form.addEventListener("submit", async function (e) {
      e.preventDefault();

      const validName = validateField(inputs.name, inputs.name.value.trim() !== "", errors.name);
      const validEmail = validateField(inputs.email, /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(inputs.email.value), errors.email);
      const validPassword = validateField(inputs.password, inputs.password.value.length >= 6, errors.password);
      const validTop = validateField(inputs.talla_top, inputs.talla_top.value.trim() !== "", errors.talla_top);
      const validBottom = validateField(inputs.talla_bottom, inputs.talla_bottom.value.trim() !== "", errors.talla_bottom);
      const validAddress = validateField(inputs.direccion_envio, inputs.direccion_envio.value.trim().length >= 5, errors.direccion_envio);

      if (validName && validEmail && validPassword && validTop && validBottom && validAddress) {
        const formData = new FormData(form);
        const res = await fetch("../server/registro.php", {
          method: "POST",
          body: formData
        });

        const result = await res.text();
        document.getElementById("responseMessage").innerText = result;
        form.reset();
        for (let input of Object.values(inputs)) {
          input.classList.remove("valid", "invalid");
        }
      } else {
        document.getElementById("responseMessage").innerText = "";
      }
    });