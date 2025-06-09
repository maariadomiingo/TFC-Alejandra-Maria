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

  // ... (código existente hasta la función validateCurrentPassword)

 // En tu editar.js
window.validateCurrentPassword = async function() {
    const entered = document.getElementById('current_password_input').value;
    
    try {
        const response = await fetch('../homeUser/verificarPasword.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ password_actual: entered })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Marcar como verificado en la sesión
            const verifyResponse = await fetch('../homeUser/set_verified_session.php');
            const verifyData = await verifyResponse.json();
            
            if (verifyData.success) {
                // Reenviar el formulario
                document.querySelector('form').submit();
            } else {
                showError("Error al verificar la sesión");
            }
        } else {
            showError(data.message || "Contraseña incorrecta");
        }
    } catch (error) {
        showError("Error al verificar la contraseña");
        console.error("Error:", error);
    }
}

function showError(message) {
    const errorElement = document.getElementById('currentPasswordError');
    errorElement.textContent = message;
    errorElement.classList.remove('hidden');
}
  // Modificación del evento submit del formulario
  form.addEventListener("submit", function(e) {
    e.preventDefault();
    
    let errores = false;
    // ... (validaciones existentes)
    
    if (errores) return;
    
    // Verificar si se está cambiando el correo o contraseña
    const isChangingEmail = correoInput.value !== "<?php echo htmlspecialchars($correo); ?>";
    const isChangingPassword = document.getElementById('hidden_password').value !== '';
    
    if (isChangingEmail || isChangingPassword) {
      if (!isPasswordVerified) {
        openCurrentPasswordModal();
      } else {
        form.submit();
      }
    } else {
      form.submit();
    }
  });

});
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

  function calcularTallas() {
    const hombro = parseFloat(document.getElementById('hombro').value) || 0;
    const pecho = parseFloat(document.getElementById('pecho').value) || 0;
    const cintura = parseFloat(document.getElementById('cintura').value) || 0;
    const cadera = parseFloat(document.getElementById('cadera').value) || 0;
    const altura = parseFloat(document.getElementById('altura').value) || 0;

    // Lógica de recomendación (simplificada)
    let talla_top = 'M'; // Default
    if (pecho < 90) talla_top = 'XS';
    else if (pecho < 95) talla_top = 'S';
    else if (pecho < 100) talla_top = 'M';
    else if (pecho < 105) talla_top = 'L';
    else talla_top = 'XL';

    let talla_bottom = '32'; // Default
    if (cintura < 70) talla_bottom = '28';
    else if (cintura < 75) talla_bottom = '30';
    else if (cintura < 80) talla_bottom = '32';
    else if (cintura < 85) talla_bottom = '34';
    else talla_bottom = '36';

    // Mostrar recomendación
    document.getElementById('talla_top').value = talla_top;
    document.getElementById('talla_bottom').value = talla_bottom;
    
    // Opcional: Mostrar mensaje al usuario
    alert(`Recommended sizes:\nTop: ${talla_top}\nBottom: ${talla_bottom}`);
}

window.changePassword = async function() {
  const current = document.getElementById('current_password').value;
  const nueva = document.getElementById('new_password').value;
  const error = document.getElementById('passwordError');
  error.classList.add('hidden');
  error.textContent = '';

  // Mensaje de éxito
  let successMsg = document.getElementById('passwordSuccess');
  if (!successMsg) {
    successMsg = document.createElement('p');
    successMsg.id = 'passwordSuccess';
    successMsg.className = 'text-green-600 text-sm mb-2';
    error.parentNode.insertBefore(successMsg, error.nextSibling);
  }
  successMsg.classList.add('hidden');
  successMsg.textContent = '';

  if (!current || !nueva) {
    error.textContent = 'Rellena ambos campos.';
    error.classList.remove('hidden');
    successMsg.classList.add('hidden');
    return;
  }

  // 1. Verificar contraseña actual
  const response = await fetch('verificarPasword.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ password_actual: current })
  });
  const data = await response.json();

  if (!data.success) {
    error.textContent = 'La contraseña actual es incorrecta.';
    error.classList.remove('hidden');
    successMsg.classList.add('hidden');
    return;
  }

  // 2. Cambiar contraseña
  const response2 = await fetch('cambiar-password.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ nueva_password: nueva })
  });
  const data2 = await response2.json();

  if (data2.success) {
    error.classList.add('hidden');
    successMsg.textContent = '¡Contraseña cambiada correctamente!';
    successMsg.classList.remove('hidden');
    // Limpia los campos
    document.getElementById('current_password').value = '';
    document.getElementById('new_password').value = '';
    // Opcional: Oculta el mensaje tras unos segundos y cierra el modal
    setTimeout(() => {
      document.getElementById('passwordModal').classList.add('hidden');
      successMsg.classList.add('hidden');
    }, 2000);
  } else {
    error.textContent = data2.message || 'Error al cambiar la contraseña.';
    error.classList.remove('hidden');
    successMsg.classList.add('hidden');
  }
}

window.toggleModal = function() {
  document.getElementById('passwordModal').classList.toggle('hidden');
  document.getElementById('current_password').value = '';
  document.getElementById('new_password').value = '';
  document.getElementById('passwordError').classList.add('hidden');
}