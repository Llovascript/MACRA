import './bootstrap';

// Elementos del DOM
const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");
const loginForm = document.querySelector("#loginForm");
const registerForm = document.querySelector("#registerForm");

// Configurar CSRF token para todas las peticiones AJAX
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Animaciones del formulario
sign_up_btn.addEventListener("click", () => {
  container.classList.add("sign-up-mode");
});

sign_in_btn.addEventListener("click", () => {
  container.classList.remove("sign-up-mode");
});

// Función para mostrar errores
function showError(elementId, message) {
  const errorElement = document.getElementById(elementId);
  errorElement.textContent = message;
  errorElement.style.display = 'block';
  errorElement.style.color = 'red';
  errorElement.style.marginTop = '10px';
}

// Función para ocultar errores
function hideError(elementId) {
  const errorElement = document.getElementById(elementId);
  errorElement.style.display = 'none';
}

// Manejar envío del formulario de login
loginForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  hideError('login-error');
  
  const formData = new FormData(loginForm);
  const loginData = {
    correo: formData.get('correo'),
    contraseña: formData.get('contraseña')
  };

  try {
    const response = await fetch('/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify(loginData)
    });

    const data = await response.json();

    if (response.ok) {
      // Login exitoso
      if (data.success) {
        // Guardar token si es necesario
        localStorage.setItem('access_token', data.access_token);
        // Redirigir a dashboard o página principal
        window.location.href = data.redirect_url || '/dashboard';
      } else {
        showError('login-error', data.message || 'Error en el login');
      }
    } else {
      showError('login-error', data.message || 'Credenciales incorrectas');
    }
  } catch (error) {
    console.error('Error:', error);
    showError('login-error', 'Error de conexión. Intenta nuevamente.');
  }
});

// Manejar envío del formulario de registro
registerForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  hideError('register-error');
  
  const formData = new FormData(registerForm);
  
  // Validar que las contraseñas coincidan
  const password = formData.get('contraseña');
  const confirmPassword = formData.get('confirmar_contraseña');
  
  if (password !== confirmPassword) {
    showError('register-error', 'Las contraseñas no coinciden');
    return;
  }

  const registerData = {
    nombre: formData.get('nombre'),
    correo: formData.get('correo'),
    contraseña: password,
    rol_id: parseInt(formData.get('rol_id'))
  };

  try {
    const response = await fetch('/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify(registerData)
    });

    const data = await response.json();

    if (response.ok) {
      // Registro exitoso
      if (data.success) {
        alert('Registro exitoso! Puedes iniciar sesión ahora.');
        // Cambiar al formulario de login
        container.classList.remove("sign-up-mode");
        registerForm.reset();
      } else {
        showError('register-error', data.message || 'Error en el registro');
      }
    } else {
      showError('register-error', data.message || 'Error al registrar usuario');
    }
  } catch (error) {
    console.error('Error:', error);
    showError('register-error', 'Error de conexión. Intenta nuevamente.');
  }
});