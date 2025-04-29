document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const errorMessages = [];
    
    // Validación de email
    if (!validateEmail(email)) {
        errorMessages.push('Por favor ingresa un correo electrónico válido');
    }
    
    // Validación de contraseña
    if (password.length < 6) {
        errorMessages.push('La contraseña debe tener al menos 6 caracteres');
    }
    
    // Mostrar errores o enviar formulario
    if (errorMessages.length > 0) {
        alert(errorMessages.join('\n'));
    } else {
        // Aquí iría la lógica de autenticación real
        alert('Inicio de sesión exitoso!');
        this.reset();
    }
});

// Función para validar email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Evento para el botón de Google
document.querySelector('.google-btn').addEventListener('click', function() {
    // Aquí iría la integración con Google Sign-In
    alert('Iniciando sesión con Google...');
});